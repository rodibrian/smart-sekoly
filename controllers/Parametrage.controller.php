<?php
/**
 * Contrôleur de paramétrage de l'établissement.
 *
 * @package Smart-Sekoly
 * @subpackage Controllers
 */
class ParametrageController
{
    private $module;
    private $action;

    public function __construct($module = 'parametrage', $action = 'assistant')
    {
        $this->module = $module;
        $this->action = $action;
    }

    /**
     * Handle the multi-step assistant (19 steps). Saves per-step data and logs changes.
     */
    private function handleAssistant(): void
    {
        $step = isset($_GET['step']) ? max(1, (int) $_GET['step'] ) : 1;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $posted = $_POST;
            $errors = [];

            // Basic server-side CSRF check
            if (empty($posted['csrf_token']) || !verifier_token_csrf((string)$posted['csrf_token'])) {
                $errors['csrf'] = 'Jeton CSRF invalide.';
            }

            if (empty($errors)) {
                $model = ParametrageEtablissement::findCurrent() ?? new ParametrageEtablissement();
                $before = [
                    'nom_etablissement' => $model->get_nom_etablissement(),
                    'format_matricule' => $model->get_format_matricule(),
                    'prefixe_matricule' => $model->get_prefixe_matricule(),
                    'monnaie' => $model->get_monnaie(),
                    'annee_courante' => $model->get_annee_courante(),
                ];

                // Map posted fields for parametrage model; collect extra keys for specialized persistence
                $saveData = [];
                $allowed = ['nom_etablissement','format_matricule','prefixe_matricule','monnaie','langue_par_defaut','theme_par_defaut','chemin_stockage_documents','annee_courante','logo'];
                foreach ($allowed as $k) {
                    if (isset($posted[$k])) {
                        $saveData[$k] = nettoyer_chaine($posted[$k]);
                    }
                }

                // Extra keys (seuils, modeles, sauvegarde configs, frequence/repertoire)
                $extraData = [];
                foreach ($posted as $k => $v) {
                    if (preg_match('/^(seuil_|modele_|politiq_)/', $k) || in_array($k, ['frequence', 'repertoire'], true)) {
                        $extraData[$k] = nettoyer_chaine($v);
                    }
                    // common assistant keys
                    if (in_array($k, ['email_admin','mode_notification','pwd_min_length','pwd_lock_after','effectif_max_par_defaut','assistant_tests','assistant_tests_step14','assistant_tests_step18','create_admin','admin_password'], true)) {
                        $extraData[$k] = $v;
                    }
                }

                $model->updateFromArray($saveData);

                // Step-specific validations
                if ((int)$step === 18) {
                    $policy = $extraData['politiq_password_policy'] ?? $posted['politiq_password_policy'] ?? '';
                    if ($policy === '') {
                        $errors['politiq_password_policy'] = 'La politique de mot de passe est requise.';
                    } elseif (!preg_match('/^min\d+$/', $policy)) {
                        $errors['politiq_password_policy'] = 'Format de politique invalide (ex: min8).';
                    }
                }

                if ((int)$step === 19 && !empty($posted['create_admin'])) {
                    $emailAdmin = nettoyer_chaine($posted['email_admin'] ?? '');
                    $passwd = $posted['admin_password'] ?? '';
                    if ($emailAdmin === '' || filter_var($emailAdmin, FILTER_VALIDATE_EMAIL) === false) {
                        $errors['email_admin'] = 'Email administrateur invalide.';
                    }
                    if (!is_string($passwd) || strlen($passwd) < 8) {
                        $errors['admin_password'] = 'Mot de passe administrateur trop court (>=8 caractères).';
                    }
                    if (empty($errors)) {
                        $pdoTmp = get_connexion_base_donnees();
                        $chkUser = $pdoTmp->prepare('SELECT id_utilisateur FROM utilisateur WHERE identifiant = :ident LIMIT 1');
                        $chkUser->execute([':ident' => $emailAdmin]);
                        if ($chkUser->fetch(PDO::FETCH_ASSOC) !== false) {
                            $errors['email_admin'] = 'Un utilisateur avec cet identifiant existe déjà.';
                        }
                    }
                }

                try {
                    $ok = $model->sauvegarder();
                } catch (Throwable $e) {
                    $ok = false;
                    $journalErr = new JournalAudit();
                    $journalErr->enregistrer([
                        'id_utilisateur' => $_SESSION['user']['id'] ?? 0,
                        'type_action' => 'parametrage:error:save',
                        'table_concernee' => 'parametrage_etablissement',
                        'id_enregistrement_concerne' => $model->get_id_parametrage(),
                        'ancienne_valeur' => $before,
                        'nouvelle_valeur' => ['error' => $e->getMessage()],
                    ]);
                }

                // Log change
                $after = [
                    'nom_etablissement' => $model->get_nom_etablissement(),
                    'format_matricule' => $model->get_format_matricule(),
                    'prefixe_matricule' => $model->get_prefixe_matricule(),
                    'monnaie' => $model->get_monnaie(),
                    'annee_courante' => $model->get_annee_courante(),
                ];

                $journal = new JournalAudit();
                $journal->enregistrer([
                    'id_utilisateur' => $_SESSION['user']['id'] ?? 0,
                    'type_action' => 'parametrage:step:' . $step,
                    'table_concernee' => 'parametrage_etablissement',
                    'id_enregistrement_concerne' => $model->get_id_parametrage(),
                    'ancienne_valeur' => $before,
                    'nouvelle_valeur' => $after,
                ]);

                {
                    // Process extra posted keys: seuils, modeles, sauvegarde config
                    try {
                        $pdo = get_connexion_base_donnees();

                        // Persist seuil_* keys into seuil_alerte table if exists
                        foreach ($extraData as $k => $v) {
                            if (strpos($k, 'seuil_') === 0) {
                                $chk = $pdo->prepare("SELECT COUNT(*) as cnt FROM information_schema.tables WHERE table_name = 'seuil_alerte' AND table_schema = DATABASE()");
                                $chk->execute();
                                $exists = (int) ($chk->fetch(PDO::FETCH_ASSOC)['cnt'] ?? 0) > 0;
                                if ($exists) {
                                    $sel = $pdo->prepare('SELECT id_seuil FROM seuil_alerte WHERE cle = :cle LIMIT 1');
                                    $sel->execute([':cle' => $k]);
                                    $r = $sel->fetch(PDO::FETCH_ASSOC);
                                    if ($r === false) {
                                        $ins = $pdo->prepare('INSERT INTO seuil_alerte (cle, valeur) VALUES (:cle, :val)');
                                        $ins->execute([':cle' => $k, ':val' => $v]);
                                    } else {
                                        $upd = $pdo->prepare('UPDATE seuil_alerte SET valeur = :val WHERE id_seuil = :id');
                                        $upd->execute([':val' => $v, ':id' => (int) $r['id_seuil']]);
                                    }
                                }
                            }

                            if (strpos($k, 'modele_') === 0) {
                                $chk2 = $pdo->prepare("SELECT COUNT(*) as cnt FROM information_schema.tables WHERE table_name = 'modele_document' AND table_schema = DATABASE()");
                                $chk2->execute();
                                $exists2 = (int) ($chk2->fetch(PDO::FETCH_ASSOC)['cnt'] ?? 0) > 0;
                                if ($exists2) {
                                    $sel = $pdo->prepare('SELECT id_modele FROM modele_document WHERE type_document = :type LIMIT 1');
                                    $sel->execute([':type' => $k]);
                                    $r3 = $sel->fetch(PDO::FETCH_ASSOC);
                                    if ($r3 === false) {
                                        $insm = $pdo->prepare('INSERT INTO modele_document (type_document, contenu) VALUES (:type, :contenu)');
                                        $insm->execute([':type' => $k, ':contenu' => $v]);
                                    } else {
                                        $updm = $pdo->prepare('UPDATE modele_document SET contenu = :contenu WHERE id_modele = :id');
                                        $updm->execute([':contenu' => $v, ':id' => (int) $r3['id_modele']]);
                                    }
                                }
                            }
                        }

                        // Persist backup config (frequence/repertoire) into session and optional table
                        if (isset($extraData['frequence']) || isset($extraData['repertoire'])) {
                            $_SESSION['sauvegarde_config'] = [
                                'frequence' => $extraData['frequence'] ?? ($_SESSION['sauvegarde_config']['frequence'] ?? ''),
                                'repertoire' => $extraData['repertoire'] ?? ($_SESSION['sauvegarde_config']['repertoire'] ?? ''),
                            ];

                            $chk3 = $pdo->prepare("SELECT COUNT(*) as cnt FROM information_schema.tables WHERE table_name = 'sauvegarde_config' AND table_schema = DATABASE()");
                            $chk3->execute();
                            $exists3 = (int) ($chk3->fetch(PDO::FETCH_ASSOC)['cnt'] ?? 0) > 0;
                            if ($exists3) {
                                $selc = $pdo->query('SELECT id_config FROM sauvegarde_config LIMIT 1');
                                $rowc = $selc->fetch(PDO::FETCH_ASSOC);
                                if ($rowc === false) {
                                    $insc = $pdo->prepare('INSERT INTO sauvegarde_config (frequence, repertoire) VALUES (:f, :r)');
                                    $insc->execute([':f' => $_SESSION['sauvegarde_config']['frequence'], ':r' => $_SESSION['sauvegarde_config']['repertoire']]);
                                } else {
                                    $updc = $pdo->prepare('UPDATE sauvegarde_config SET frequence = :f, repertoire = :r WHERE id_config = :id');
                                    $updc->execute([':f' => $_SESSION['sauvegarde_config']['frequence'], ':r' => $_SESSION['sauvegarde_config']['repertoire'], ':id' => (int) $rowc['id_config']]);
                                }
                            }
                        }

                        // Persist common assistant keys into a key/value table `parametrage_kv` if present,
                        // otherwise keep in session. Keys: email_admin, mode_notification, pwd_min_length, pwd_lock_after, effectif_max_par_defaut, assistant_tests
                        $assistantKeys = ['email_admin','mode_notification','pwd_min_length','pwd_lock_after','effectif_max_par_defaut','assistant_tests','assistant_tests_step14','assistant_tests_step18'];
                        $chkKV = $pdo->prepare("SELECT COUNT(*) as cnt FROM information_schema.tables WHERE table_name = 'parametrage_kv' AND table_schema = DATABASE()");
                        $chkKV->execute();
                        $hasKV = (int) ($chkKV->fetch(PDO::FETCH_ASSOC)['cnt'] ?? 0) > 0;

                        foreach ($extraData as $k => $v) {
                            if (in_array($k, $assistantKeys, true)) {
                                if ($hasKV) {
                                    $sel = $pdo->prepare('SELECT id_kv FROM parametrage_kv WHERE `cle` = :cle LIMIT 1');
                                    $sel->execute([':cle' => $k]);
                                    $r4 = $sel->fetch(PDO::FETCH_ASSOC);
                                    if ($r4 === false) {
                                        $insk = $pdo->prepare('INSERT INTO parametrage_kv (`cle`, `valeur`) VALUES (:cle, :val)');
                                        $insk->execute([':cle' => $k, ':val' => $v]);
                                    } else {
                                        $updk = $pdo->prepare('UPDATE parametrage_kv SET valeur = :val WHERE id_kv = :id');
                                        $updk->execute([':val' => $v, ':id' => (int) $r4['id_kv']]);
                                    }
                                } else {
                                    // fallback to session
                                    $_SESSION['parametrage_extra'][$k] = $v;
                                }
                            }
                        }

                        // Specific persistence for steps 7..12: seuils, modeles, sauvegarde configuration
                        if ((int)$step >= 7 && (int)$step <= 12) {
                            try {
                                foreach ($extraData as $k => $v) {
                                    if (strpos($k, 'seuil_') === 0) {
                                        $chkSeuil = $pdo->prepare("SELECT COUNT(*) as cnt FROM information_schema.tables WHERE table_name = 'seuil_alerte' AND table_schema = DATABASE()");
                                        $chkSeuil->execute();
                                        if ((int) $chkSeuil->fetch(PDO::FETCH_ASSOC)['cnt'] > 0) {
                                            $sel = $pdo->prepare('SELECT id_seuil FROM seuil_alerte WHERE id_parametrage = :idp AND type_seuil = :type LIMIT 1');
                                            $sel->execute([':idp' => $model->get_id_parametrage(), ':type' => $k]);
                                            $rowSeuil = $sel->fetch(PDO::FETCH_ASSOC);
                                            if ($rowSeuil === false) {
                                                $ins = $pdo->prepare('INSERT INTO seuil_alerte (id_parametrage, type_seuil, valeur_seuil) VALUES (:idp, :type, :val)');
                                                $ins->execute([':idp' => $model->get_id_parametrage(), ':type' => $k, ':val' => $v]);
                                            } else {
                                                $upd = $pdo->prepare('UPDATE seuil_alerte SET valeur_seuil = :val WHERE id_seuil = :id');
                                                $upd->execute([':val' => $v, ':id' => (int) $rowSeuil['id_seuil']]);
                                            }
                                        }
                                    }

                                    if (strpos($k, 'modele_') === 0) {
                                        $chkModele = $pdo->prepare("SELECT COUNT(*) as cnt FROM information_schema.tables WHERE table_name = 'modele_document' AND table_schema = DATABASE()");
                                        $chkModele->execute();
                                        if ((int) $chkModele->fetch(PDO::FETCH_ASSOC)['cnt'] > 0) {
                                            $typeDoc = str_replace('modele_', '', $k);
                                            $sel = $pdo->prepare('SELECT id_modele FROM modele_document WHERE id_parametrage = :idp AND type_modele = :type LIMIT 1');
                                            $sel->execute([':idp' => $model->get_id_parametrage(), ':type' => $typeDoc]);
                                            $rowModele = $sel->fetch(PDO::FETCH_ASSOC);
                                            $contenu = json_encode(['html' => $v], JSON_UNESCAPED_UNICODE);
                                            if ($rowModele === false) {
                                                $ins = $pdo->prepare('INSERT INTO modele_document (id_parametrage, type_modele, contenu_parametrable) VALUES (:idp, :type, :contenu)');
                                                $ins->execute([':idp' => $model->get_id_parametrage(), ':type' => $typeDoc, ':contenu' => $contenu]);
                                            } else {
                                                $upd = $pdo->prepare('UPDATE modele_document SET contenu_parametrable = :contenu WHERE id_modele = :id');
                                                $upd->execute([':contenu' => $contenu, ':id' => (int) $rowModele['id_modele']]);
                                            }
                                        }
                                    }
                                }

                                $ja7 = new JournalAudit();
                                $ja7->enregistrer([
                                    'id_utilisateur' => $_SESSION['user']['id'] ?? 0,
                                    'type_action' => 'parametrage:step:' . $step,
                                    'table_concernee' => 'parametrage_etablissement',
                                    'id_enregistrement_concerne' => $model->get_id_parametrage(),
                                    'ancienne_valeur' => $before,
                                    'nouvelle_valeur' => $after,
                                ]);
                            } catch (Throwable $e) {
                                $ja7err = new JournalAudit();
                                $ja7err->enregistrer([
                                    'id_utilisateur' => $_SESSION['user']['id'] ?? 0,
                                    'type_action' => 'parametrage:error:step7_12',
                                    'table_concernee' => 'meta',
                                    'id_enregistrement_concerne' => $model->get_id_parametrage(),
                                    'ancienne_valeur' => null,
                                    'nouvelle_valeur' => ['error' => $e->getMessage()],
                                ]);
                            }
                        }

                        // Specific persistence for step 18: politiques / final checks
                        if ((int)$step === 18) {
                            try {
                                foreach ($extraData as $k => $v) {
                                    if (strpos($k, 'politiq_') === 0) {
                                        if ($hasKV) {
                                            $selp = $pdo->prepare('SELECT id_kv FROM parametrage_kv WHERE `cle` = :cle LIMIT 1');
                                            $selp->execute([':cle' => $k]);
                                            $rp = $selp->fetch(PDO::FETCH_ASSOC);
                                            if ($rp === false) {
                                                $inskp = $pdo->prepare('INSERT INTO parametrage_kv (`cle`, `valeur`) VALUES (:cle, :val)');
                                                $inskp->execute([':cle' => $k, ':val' => $v]);
                                            } else {
                                                $updkp = $pdo->prepare('UPDATE parametrage_kv SET valeur = :val WHERE id_kv = :id');
                                                $updkp->execute([':val' => $v, ':id' => (int) $rp['id_kv']]);
                                            }
                                        } else {
                                            $_SESSION['parametrage_extra'][$k] = $v;
                                        }
                                    }
                                }

                                $ja = new JournalAudit();
                                $ja->enregistrer([
                                    'id_utilisateur' => $_SESSION['user']['id'] ?? 0,
                                    'type_action' => 'parametrage:step:18',
                                    'table_concernee' => 'parametrage_etablissement',
                                    'id_enregistrement_concerne' => $model->get_id_parametrage(),
                                    'ancienne_valeur' => $before,
                                    'nouvelle_valeur' => $after,
                                ]);
                            } catch (Throwable $e) {
                                $jx = new JournalAudit();
                                $jx->enregistrer([
                                    'id_utilisateur' => $_SESSION['user']['id'] ?? 0,
                                    'type_action' => 'parametrage:error:step18',
                                    'table_concernee' => 'meta',
                                    'id_enregistrement_concerne' => $model->get_id_parametrage(),
                                    'ancienne_valeur' => null,
                                    'nouvelle_valeur' => ['error' => $e->getMessage()],
                                ]);
                            }
                        }

                        // Ensure sequences exist for current active year when step 7
                        if ((int)$step === 7) {
                            $stmt = $pdo->query("SELECT id_annee FROM annee_scolaire WHERE etat = 'active' LIMIT 1");
                            $rowA = $stmt->fetch(PDO::FETCH_ASSOC);
                            if ($rowA !== false) {
                                $idannee = (int) $rowA['id_annee'];
                                $types = ['matricule','recu','facture'];
                                foreach ($types as $t) {
                                    $s = $pdo->prepare('SELECT id_sequence FROM sequence_numerotation WHERE type_document = :type AND id_annee = :annee LIMIT 1');
                                    $s->execute([':type' => $t, ':annee' => $idannee]);
                                    $r = $s->fetch(PDO::FETCH_ASSOC);
                                    if ($r === false) {
                                        $ins2 = $pdo->prepare('INSERT INTO sequence_numerotation (id_parametrage, type_document, id_annee, dernier_numero, format) VALUES (:id_param, :type, :annee, :dernier, :format)');
                                        $ins2->execute([
                                            ':id_param' => $model->get_id_parametrage(),
                                            ':type' => $t,
                                            ':annee' => $idannee,
                                            ':dernier' => 0,
                                            ':format' => $model->get_format_matricule(),
                                        ]);
                                    }
                                }
                            }
                        }
                    } catch (Throwable $e) {
                        $journalX = new JournalAudit();
                        $journalX->enregistrer([
                            'id_utilisateur' => $_SESSION['user']['id'] ?? 0,
                            'type_action' => 'parametrage:error:extra_persist',
                            'table_concernee' => 'meta',
                            'id_enregistrement_concerne' => $model->get_id_parametrage(),
                            'ancienne_valeur' => null,
                            'nouvelle_valeur' => ['error' => $e->getMessage()],
                        ]);
                    }
                    // If this was the final step, mark assistant completed in session and audit
                    if ((int)$step === 19) {
                        $_SESSION['assistant_termine'] = true;
                        $j = new JournalAudit();
                        $j->enregistrer([
                            'id_utilisateur' => $_SESSION['user']['id'] ?? 0,
                            'type_action' => 'parametrage:assistant:complete',
                            'table_concernee' => 'parametrage_etablissement',
                            'id_enregistrement_concerne' => $model->get_id_parametrage(),
                            'ancienne_valeur' => $before,
                            'nouvelle_valeur' => $after,
                        ]);
                        // Additional finalization actions for step 19: optional admin user creation
                        try {
                            if (!empty($extraData['create_admin']) && !empty($extraData['email_admin']) && !empty($extraData['admin_password'])) {
                                $emailAdmin = nettoyer_chaine($extraData['email_admin']);
                                $passwd = $extraData['admin_password'];
                                // create person
                                $insP = $pdo->prepare('INSERT INTO personne (nom, prenom, email, date_creation) VALUES (:nom, :prenom, :email, NOW())');
                                $insP->execute([':nom' => 'Admin', ':prenom' => 'System', ':email' => $emailAdmin]);
                                $idPersonne = (int) $pdo->lastInsertId();
                                // create utilisateur
                                $hash = password_hash($passwd, PASSWORD_DEFAULT);
                                $insU = $pdo->prepare('INSERT INTO utilisateur (id_personne, identifiant, mot_de_passe_hash, statut_compte, doit_changer_mdp, nombre_essais_echoues, date_creation) VALUES (:idp, :ident, :hash, :statut, :doit, 0, NOW())');
                                $insU->execute([':idp' => $idPersonne, ':ident' => $emailAdmin, ':hash' => $hash, ':statut' => 'actif', ':doit' => 1]);
                                $idUtilisateur = (int) $pdo->lastInsertId();
                                $ja2 = new JournalAudit();
                                $ja2->enregistrer([
                                    'id_utilisateur' => $_SESSION['user']['id'] ?? 0,
                                    'type_action' => 'parametrage:created_admin',
                                    'table_concernee' => 'utilisateur',
                                    'id_enregistrement_concerne' => $idUtilisateur,
                                    'ancienne_valeur' => null,
                                    'nouvelle_valeur' => ['identifiant' => $emailAdmin],
                                ]);
                            }
                        } catch (Throwable $e) {
                            $je = new JournalAudit();
                            $je->enregistrer([
                                'id_utilisateur' => $_SESSION['user']['id'] ?? 0,
                                'type_action' => 'parametrage:error:create_admin',
                                'table_concernee' => 'utilisateur',
                                'id_enregistrement_concerne' => null,
                                'ancienne_valeur' => null,
                                'nouvelle_valeur' => ['error' => $e->getMessage()],
                            ]);
                        }
                    }
                    // If the posted data included an active year, ensure it exists in `annee_scolaire` and mark as active
                    if (!empty($saveData['annee_courante'])) {
                        try {
                            $pdo = get_connexion_base_donnees();
                            $libelle = (string) $saveData['annee_courante'];

                            // deactivate other years
                            $pdo->prepare("UPDATE annee_scolaire SET etat = 'inactive' WHERE etat = 'active'")->execute();

                            // check if year exists
                            $stmt = $pdo->prepare('SELECT id_annee FROM annee_scolaire WHERE libelle = :libelle LIMIT 1');
                            $stmt->execute([':libelle' => $libelle]);
                            $row = $stmt->fetch(PDO::FETCH_ASSOC);
                            if ($row === false) {
                                $ins = $pdo->prepare('INSERT INTO annee_scolaire (libelle, etat) VALUES (:libelle, :etat)');
                                $ins->execute([':libelle' => $libelle, ':etat' => 'active']);
                                $id_annee = (int) $pdo->lastInsertId();
                            } else {
                                $id_annee = (int) $row['id_annee'];
                                $upd = $pdo->prepare('UPDATE annee_scolaire SET etat = :etat WHERE id_annee = :id');
                                $upd->execute([':etat' => 'active', ':id' => $id_annee]);
                            }

                            // initialize core sequences if missing (matricule, recu, facture)
                            $types = ['matricule', 'recu', 'facture'];
                            foreach ($types as $t) {
                                $s = $pdo->prepare('SELECT id_sequence FROM sequence_numerotation WHERE type_document = :type AND id_annee = :annee LIMIT 1');
                                $s->execute([':type' => $t, ':annee' => $id_annee]);
                                $r = $s->fetch(PDO::FETCH_ASSOC);
                                if ($r === false) {
                                    $ins2 = $pdo->prepare('INSERT INTO sequence_numerotation (id_parametrage, type_document, id_annee, dernier_numero, format) VALUES (:id_param, :type, :annee, :dernier, :format)');
                                    $ins2->execute([
                                        ':id_param' => $model->get_id_parametrage(),
                                        ':type' => $t,
                                        ':annee' => $id_annee,
                                        ':dernier' => 0,
                                        ':format' => $model->get_format_matricule(),
                                    ]);
                                }
                            }
                                // Persist generic seuils and modele documents if posted and tables exist
                                foreach ($saveData as $k => $v) {
                                    try {
                                        if (strpos($k, 'seuil_') === 0) {
                                            // check table exists
                                            $chk = $pdo->prepare("SELECT COUNT(*) as cnt FROM information_schema.tables WHERE table_name = 'seuil_alerte' AND table_schema = DATABASE()");
                                            $chk->execute();
                                            $exists = (int) ($chk->fetch(PDO::FETCH_ASSOC)['cnt'] ?? 0) > 0;
                                            if ($exists) {
                                                // upsert by cle
                                                $cle = $k;
                                                $val = $v;
                                                $sel = $pdo->prepare('SELECT id_seuil FROM seuil_alerte WHERE cle = :cle LIMIT 1');
                                                $sel->execute([':cle' => $cle]);
                                                $r2 = $sel->fetch(PDO::FETCH_ASSOC);
                                                if ($r2 === false) {
                                                    $insS = $pdo->prepare('INSERT INTO seuil_alerte (cle, valeur) VALUES (:cle, :val)');
                                                    $insS->execute([':cle' => $cle, ':val' => $val]);
                                                } else {
                                                    $updS = $pdo->prepare('UPDATE seuil_alerte SET valeur = :val WHERE id_seuil = :id');
                                                    $updS->execute([':val' => $val, ':id' => (int) $r2['id_seuil']]);
                                                }
                                            }
                                        }

                                        if (strpos($k, 'modele_') === 0) {
                                            $chk2 = $pdo->prepare("SELECT COUNT(*) as cnt FROM information_schema.tables WHERE table_name = 'modele_document' AND table_schema = DATABASE()");
                                            $chk2->execute();
                                            $exists2 = (int) ($chk2->fetch(PDO::FETCH_ASSOC)['cnt'] ?? 0) > 0;
                                            if ($exists2) {
                                                // store as type / contenu
                                                $typeDoc = $k; // e.g., modele_bulletin
                                                $contenu = $v;
                                                $sel = $pdo->prepare('SELECT id_modele FROM modele_document WHERE type_document = :type LIMIT 1');
                                                $sel->execute([':type' => $typeDoc]);
                                                $r3 = $sel->fetch(PDO::FETCH_ASSOC);
                                                if ($r3 === false) {
                                                    $insM = $pdo->prepare('INSERT INTO modele_document (type_document, contenu) VALUES (:type, :contenu)');
                                                    $insM->execute([':type' => $typeDoc, ':contenu' => $contenu]);
                                                } else {
                                                    $updM = $pdo->prepare('UPDATE modele_document SET contenu = :contenu WHERE id_modele = :id');
                                                    $updM->execute([':contenu' => $contenu, ':id' => (int) $r3['id_modele']]);
                                                }
                                            }
                                        }
                                    } catch (Throwable $e) {
                                        $journal3 = new JournalAudit();
                                        $journal3->enregistrer([
                                            'id_utilisateur' => $_SESSION['user']['id'] ?? 0,
                                            'type_action' => 'parametrage:error:persist_extra',
                                            'table_concernee' => 'meta',
                                            'id_enregistrement_concerne' => 0,
                                            'ancienne_valeur' => null,
                                            'nouvelle_valeur' => ['key' => $k, 'error' => $e->getMessage()],
                                        ]);
                                    }
                                }
                        } catch (Throwable $e) {
                            // Log but do not block the wizard progression
                            $journal2 = new JournalAudit();
                            $journal2->enregistrer([
                                'id_utilisateur' => $_SESSION['user']['id'] ?? 0,
                                'type_action' => 'parametrage:error:annee_init',
                                'table_concernee' => 'annee_scolaire',
                                'id_enregistrement_concerne' => 0,
                                'ancienne_valeur' => null,
                                'nouvelle_valeur' => ['error' => $e->getMessage()],
                            ]);
                        }
                    }

                    // redirect to next step (skip header during CLI/testing)
                    $next = $step < 19 ? $step + 1 : 19;
                    if (php_sapi_name() !== 'cli' && !empty(BASE_URL)) {
                        header('Location: ' . BASE_URL . '/parametrage/assistant?step=' . $next);
                        return;
                    }
                    // in CLI/testing mode, just note next step and continue
                    if (defined('DEBUG_CLI') && DEBUG_CLI) {
                        echo "[CLI] would redirect to step: $next\n";
                    }
                    return;
                }
            }
        }

        // Render step view
        $vue = 'parametrage/assistant_step.view.php';
        $donnees = [
            'module' => $this->module,
            'action' => $this->action,
            'step' => $step,
            'token_csrf' => generer_token_csrf(),
            'parametrage' => ParametrageEtablissement::findCurrent(),
        ];
        require TEMPLATES_PATH . $vue;
    }

    public function executer(): void
    {
        $vue = 'parametrage/assistant.view.php';

        if ($this->action === 'themes') {
            $vue = 'parametrage/themes.view.php';
        } elseif ($this->action === 'courant') {
            $vue = 'parametrage/courant.view.php';
        } elseif ($this->action === 'sauvegardes') {
            $vue = 'parametrage/sauvegardes.view.php';
        } elseif ($this->action === 'generer_matricule') {
            // API: génère un nouveau numéro de matricule pour l'année active ou id_annee fourni
            try {
                $id_annee = $_REQUEST['id_annee'] ?? null;
                $pdo = get_connexion_base_donnees();
                if ($id_annee === null) {
                    $stmt = $pdo->query("SELECT id_annee FROM annee_scolaire WHERE etat = 'active' LIMIT 1");
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($row === false) {
                        throw new RuntimeException('Aucune année active trouvée.');
                    }
                    $id_annee = (int) $row['id_annee'];
                } else {
                    $id_annee = (int) $id_annee;
                }

                $res = SequenceNumerotation::getNext('matricule', $id_annee);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['success' => true, 'numero' => $res['numero'], 'formatte' => $res['formatte']]);
            } catch (Throwable $e) {
                header('Content-Type: application/json; charset=utf-8', true, 500);
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }

            return;
        }

        // Load current parametrage from DB or defaults
        $current = ParametrageEtablissement::findCurrent();
        if ($current === null) {
            $current = new ParametrageEtablissement();
        }

        // If assistant wizard step handling
        if ($this->action === 'assistant') {
            $this->handleAssistant();
            return;
        }

        $donnees = [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'theme_actuel' => $_SESSION['theme_app'] ?? $current->get_theme_par_defaut() ?? 'clair',
            'parametrage' => [
                'nom_etablissement' => $current->get_nom_etablissement(),
                'monnaie' => $current->get_monnaie() ?? 'MGA',
                'langue_par_defaut' => $current->get_langue_par_defaut() ?? 'fr',
                'theme_par_defaut' => $current->get_theme_par_defaut() ?? 'clair',
                'format_matricule' => $current->get_format_matricule(),
                'prefixe_matricule' => $current->get_prefixe_matricule(),
                'auto_download_escpos' => defined('DEFAULT_AUTO_DOWNLOAD_ESC_POS') ? DEFAULT_AUTO_DOWNLOAD_ESC_POS : false,
            ],
        ];

        require TEMPLATES_PATH . $vue;
    }

    public function traiter_formulaire(array $donnees_formulaire): array
    {
        $erreurs = [];

        $nom_etablissement = nettoyer_chaine($donnees_formulaire['nom_etablissement'] ?? '');
        $monnaie = nettoyer_chaine($donnees_formulaire['monnaie'] ?? '');
        $langue_par_defaut = nettoyer_chaine($donnees_formulaire['langue_par_defaut'] ?? '');
        $theme_par_defaut = nettoyer_chaine($donnees_formulaire['theme_par_defaut'] ?? '');
        $format_matricule = nettoyer_chaine($donnees_formulaire['format_matricule'] ?? '');
        $prefixe_matricule = nettoyer_chaine($donnees_formulaire['prefixe_matricule'] ?? '');
        $annee_courante = nettoyer_chaine($donnees_formulaire['annee_courante'] ?? '');
        $auto_download = !empty($donnees_formulaire['auto_download_escpos']) ? '1' : '0';

        if ($nom_etablissement === '') {
            $erreurs['nom_etablissement'] = 'Le nom de l’établissement est obligatoire.';
        }

        if ($format_matricule === '') {
            $erreurs['format_matricule'] = 'Le format du matricule est obligatoire.';
        }

        if ($prefixe_matricule === '') {
            $erreurs['prefixe_matricule'] = 'Le préfixe du matricule est obligatoire.';
        }

        if ($annee_courante === '' || !is_numeric($annee_courante)) {
            $erreurs['annee_courante'] = 'L’année scolaire doit être numérique.';
        }

        if ($monnaie === '') {
            $erreurs['monnaie'] = 'La monnaie est obligatoire.';
        }

        if (!in_array($theme_par_defaut, ['clair', 'sombre'], true)) {
            $erreurs['theme_par_defaut'] = 'Le thème sélectionné est invalide.';
        }

        if (empty($erreurs)) {
            $_SESSION['theme_app'] = $theme_par_defaut;


            $model = ParametrageEtablissement::findCurrent();
            if ($model === null) {
                $model = new ParametrageEtablissement();
            }

            $model->updateFromArray([
                'nom_etablissement' => $nom_etablissement,
                'monnaie' => $monnaie,
                'langue_par_defaut' => $langue_par_defaut,
                'theme_par_defaut' => $theme_par_defaut,
                'format_matricule' => $format_matricule,
                'prefixe_matricule' => $prefixe_matricule,
                'annee_courante' => $annee_courante,
            ]);

            $saved = $model->sauvegarder();

            if (!$saved) {
                $erreurs['general'] = 'Impossible de sauvegarder la configuration en base de données.';
            }
        }

        return [
            'valide' => empty($erreurs),
            'erreurs' => $erreurs,
            'donnees' => [
                'nom_etablissement' => $nom_etablissement,
                'format_matricule' => $format_matricule,
                'prefixe_matricule' => $prefixe_matricule,
                'annee_courante' => $annee_courante,
                'monnaie' => $monnaie,
                'langue_par_defaut' => $langue_par_defaut,
                'theme_par_defaut' => $theme_par_defaut,
                'auto_download_escpos' => $auto_download,
            ],
        ];
    }

    public function traiter_theme_formulaire(array $donnees_formulaire): array
    {
        $erreurs = [];
        $theme = nettoyer_chaine($donnees_formulaire['theme'] ?? '');

        if (!in_array($theme, ['clair', 'sombre'], true)) {
            $erreurs['theme'] = 'Le thème sélectionné est invalide.';
        }

        if (empty($donnees_formulaire['csrf_token'] ?? '')) {
            $erreurs['csrf_token'] = 'Le jeton CSRF est absent.';
        } elseif (!verifier_token_csrf((string) $donnees_formulaire['csrf_token'])) {
            $erreurs['csrf_token'] = 'Le jeton CSRF est invalide.';
        }

        if (empty($erreurs)) {
            $_SESSION['theme_app'] = $theme;
        }

        return [
            'valide' => empty($erreurs),
            'erreurs' => $erreurs,
            'donnees' => [
                'theme' => $theme,
            ],
        ];
    }

    public function traiter_sauvegarde_formulaire(array $donnees_formulaire): array
    {
        $erreurs = [];
        $frequence = nettoyer_chaine($donnees_formulaire['frequence'] ?? '');
        $repertoire = nettoyer_chaine($donnees_formulaire['repertoire'] ?? '');
        $retention = nettoyer_chaine($donnees_formulaire['retention'] ?? '');
        $activer = !empty($donnees_formulaire['activer']) ? '1' : '0';

        if (!in_array($frequence, ['quotidienne', 'hebdomadaire', 'mensuelle'], true)) {
            $erreurs['frequence'] = 'La fréquence de sauvegarde est invalide.';
        }

        if ($repertoire === '') {
            $erreurs['repertoire'] = 'Le répertoire de sauvegarde est obligatoire.';
        }

        if ($retention === '' || !is_numeric($retention)) {
            $erreurs['retention'] = 'La rétention doit être numérique.';
        }

        if (empty($erreurs)) {
            $_SESSION['sauvegarde_config'] = [
                'frequence' => $frequence,
                'repertoire' => $repertoire,
                'retention' => $retention,
                'activer' => $activer,
            ];
        }

        return [
            'valide' => empty($erreurs),
            'erreurs' => $erreurs,
            'donnees' => [
                'frequence' => $frequence,
                'repertoire' => $repertoire,
                'retention' => $retention,
                'activer' => $activer,
            ],
        ];
    }
}
