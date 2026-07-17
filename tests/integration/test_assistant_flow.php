<?php
// Integration test: simulate assistant flow by using model APIs and DB checks.
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/fonctions.php';
require_once __DIR__ . '/../../classes/ParametrageEtablissement.class.php';
require_once __DIR__ . '/../../classes/SequenceNumerotation.class.php';
require_once __DIR__ . '/../../classes/JournalAudit.class.php';
require_once __DIR__ . '/../../controllers/Parametrage.controller.php';

session_start();

// minimal constants expected by controllers when executed in CLI
if (!defined('ROOT_PATH')) define('ROOT_PATH', realpath(__DIR__ . '/../../') . DIRECTORY_SEPARATOR);
if (!defined('TEMPLATES_PATH')) define('TEMPLATES_PATH', ROOT_PATH . 'templates' . DIRECTORY_SEPARATOR);
if (!defined('BASE_URL')) define('BASE_URL', '');

$pdo = get_connexion_base_donnees();
if (!$pdo instanceof PDO) {
    echo "DB unavailable\n";
    exit(1);
}

function info($msg) { echo "[INFO] $msg\n"; }

try {
    // Step 1: basic info
    // Insert a parametrage row directly (schema has a limited set of columns)
    $insP = $pdo->prepare('INSERT INTO parametrage_etablissement (nom_etablissement, logo, monnaie, langue_par_defaut, theme_par_defaut, chemin_stockage_documents) VALUES (:nom, :logo, :monnaie, :langue, :theme, :chemin)');
    $insP->execute([':nom' => 'Ecole Test Maison', ':logo' => 'assets/logo.png', ':monnaie' => 'MGA', ':langue' => 'fr', ':theme' => 'clair', ':chemin' => 'documents']);
    $p = ParametrageEtablissement::findCurrent();
    if ($p === null) throw new RuntimeException('Unable to create parametrage row');
    info('Step1 inserted parametrage row id=' . $p->get_id_parametrage());

    // Step 3: update currency directly
    $upd = $pdo->prepare('UPDATE parametrage_etablissement SET monnaie = :m WHERE id_parametrage = :id');
    $upd->execute([':m' => 'EUR', ':id' => $p->get_id_parametrage()]);
    $p = ParametrageEtablissement::findCurrent();
    info('Step3 updated currency');

    // Step 6: set active year
    $libelle = date('Y') . '-' . (date('Y')+1);
    $pdo->beginTransaction();
    $pdo->prepare("UPDATE annee_scolaire SET etat='inactive' WHERE etat='active'")->execute();
    $stmt = $pdo->prepare('SELECT id_annee FROM annee_scolaire WHERE libelle = :libelle LIMIT 1');
    $stmt->execute([':libelle' => $libelle]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row === false) {
        $ins = $pdo->prepare('INSERT INTO annee_scolaire (libelle, etat) VALUES (:lib, :etat)');
        $ins->execute([':lib' => $libelle, ':etat' => 'active']);
        $id_annee = (int)$pdo->lastInsertId();
    } else {
        $id_annee = (int)$row['id_annee'];
        $pdo->prepare('UPDATE annee_scolaire SET etat = :etat WHERE id_annee = :id')->execute([':etat' => 'active', ':id' => $id_annee]);
    }
    $pdo->commit();
    info('Step6 year set: ' . $libelle);

    // Initialize sequences (step7)
    $types = ['matricule','recu','facture'];
    foreach ($types as $t) {
        $s = $pdo->prepare('SELECT id_sequence FROM sequence_numerotation WHERE type_document = :type AND id_annee = :annee LIMIT 1');
        $s->execute([':type'=>$t,':annee'=>$id_annee]);
        $r = $s->fetch(PDO::FETCH_ASSOC);
        if ($r === false) {
            $ins2 = $pdo->prepare('INSERT INTO sequence_numerotation (id_parametrage, type_document, id_annee, dernier_numero, format) VALUES (:id_param, :type, :annee, :dernier, :format)');
            $ins2->execute([':id_param' => $p->get_id_parametrage(), ':type'=>$t,':annee'=>$id_annee,':dernier'=>0,':format'=>'{PREFIXE}-{ANNEE}-{NUMERO_SEQUENTIEL}']);
        }
    }
    info('Step7 sequences initialized');

    // Generate a matricule to verify
    $res = SequenceNumerotation::getNext('matricule', $id_annee);
    info('Generated matricule: ' . $res['formatte']);

    // Step8: persist seuils into seuil_alerte if exists
    $chk = $pdo->prepare("SELECT COUNT(*) as cnt FROM information_schema.tables WHERE table_name = 'seuil_alerte' AND table_schema = DATABASE()");
    $chk->execute();
    if ((int)$chk->fetch(PDO::FETCH_ASSOC)['cnt'] > 0) {
        $ins = $pdo->prepare('INSERT INTO seuil_alerte (id_parametrage, type_seuil, valeur_seuil) VALUES (:id_param, :type, :val)');
        $ins->execute([':id_param' => $p->get_id_parametrage(), ':type'=>'seuil_redoublement', ':val'=>9.5]);
        info('Step8 seuil inserted');
    } else {
        info('Step8 seuil table missing; skipped insert');
    }

    // Step9 models: persist modele_bulletin if table exists
    $chk2 = $pdo->prepare("SELECT COUNT(*) as cnt FROM information_schema.tables WHERE table_name = 'modele_document' AND table_schema = DATABASE()");
    $chk2->execute();
    if ((int)$chk2->fetch(PDO::FETCH_ASSOC)['cnt'] > 0) {
        $insm = $pdo->prepare('INSERT INTO modele_document (id_parametrage, type_modele, contenu_parametrable) VALUES (:id_param, :type, :contenu)');
        $insm->execute([':id_param' => $p->get_id_parametrage(), ':type'=>'bulletin', ':contenu'=>json_encode(['html' => '<html><body>Bulletin test</body></html>'])]);
        info('Step9 modele inserted');
    } else {
        info('Step9 modele table missing; skipped');
    }

    // Step10 backup config into session
    $_SESSION['sauvegarde_config'] = ['frequence'=>'quotidienne','repertoire'=>'backups'];
    info('Step10 backup config set in session');

    // Step15 security settings persist to parametrage_kv or session
    $chkKV = $pdo->prepare("SELECT COUNT(*) as cnt FROM information_schema.tables WHERE table_name = 'parametrage_kv' AND table_schema = DATABASE()");
    $chkKV->execute();
    $hasKV = (int)$chkKV->fetch(PDO::FETCH_ASSOC)['cnt'] > 0;
    if ($hasKV) {
        $insk = $pdo->prepare('INSERT INTO parametrage_kv (`cle`,`valeur`) VALUES (:k,:v)');
        $insk->execute([':k'=>'pwd_min_length',':v'=>'8']);
        $insk->execute([':k'=>'pwd_lock_after',':v'=>'5']);
        info('Step15 security settings saved in parametrage_kv');
    } else {
        $_SESSION['parametrage_extra']['pwd_min_length'] = 8;
        $_SESSION['parametrage_extra']['pwd_lock_after'] = 5;
        info('Step15 security settings saved in session');
    }

    // Finalize assistant
    $_SESSION['assistant_termine'] = true;
    info('Assistant marked complete');

    // --- Now simulate controller POST for step 18 (policies) ---
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_GET['step'] = 18;
    $_POST = [
        'csrf_token' => generer_token_csrf(),
        'politiq_password_policy' => 'min8',
        'assistant_tests_step18' => 'ok',
    ];

    $ctrl = new ParametrageController('parametrage', 'assistant');
    $ctrl->executer();

    // verify parametrage_kv or session fallback
    $chkKV = $pdo->prepare("SELECT COUNT(*) as cnt FROM information_schema.tables WHERE table_name = 'parametrage_kv' AND table_schema = DATABASE()");
    $chkKV->execute();
    $hasKV = (int)$chkKV->fetch(PDO::FETCH_ASSOC)['cnt'] > 0;
    if ($hasKV) {
        $q = $pdo->prepare('SELECT valeur FROM parametrage_kv WHERE `cle` = :cle LIMIT 1');
        $q->execute([':cle' => 'politiq_password_policy']);
        $r = $q->fetch(PDO::FETCH_ASSOC);
        if ($r === false) throw new RuntimeException('parametrage_kv missing expected key');
        info('Step18 persisted in parametrage_kv: ' . $r['valeur']);
    } else {
        if (empty($_SESSION['parametrage_extra']['politiq_password_policy'])) throw new RuntimeException('Session fallback missing politq key');
        info('Step18 persisted in session: ' . $_SESSION['parametrage_extra']['politiq_password_policy']);
    }

    // check audit for step 18
    $journal = new JournalAudit();
    $found = false;
    foreach ($journal->lister(100) as $entry) {
        if (($entry['type_action'] ?? '') === 'parametrage:step:18') { $found = true; break; }
    }
    if (!$found) throw new RuntimeException('Audit entry for step 18 not found');
    info('Audit entry for step 18 present');

    // --- Now simulate controller POST for step 19 (finalize + create admin) ---
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_GET['step'] = 19;
    $_POST = [
        'csrf_token' => generer_token_csrf(),
        'create_admin' => '1',
        'email_admin' => 'admin@example.test',
        'admin_password' => 'Secret123!'
    ];

    $ctrl = new ParametrageController('parametrage', 'assistant');
    $ctrl->executer();

    // verify admin user exists
    $q2 = $pdo->prepare('SELECT u.id_utilisateur, p.email FROM utilisateur u JOIN personne p ON p.id_personne = u.id_personne WHERE u.identifiant = :ident LIMIT 1');
    $q2->execute([':ident' => 'admin@example.test']);
    $ra = $q2->fetch(PDO::FETCH_ASSOC);
    if ($ra === false) throw new RuntimeException('Admin user not created');
    info('Admin user created id=' . $ra['id_utilisateur']);

    // check audit for admin creation
    $foundAdminAudit = false;
    foreach ($journal->lister(200) as $entry) {
        if (($entry['type_action'] ?? '') === 'parametrage:created_admin') { $foundAdminAudit = true; break; }
    }
    if (!$foundAdminAudit) throw new RuntimeException('Audit entry for admin creation not found');
    info('Audit entry for admin creation present');

    echo "ALL STEPS SIMULATED OK\n";
    exit(0);

} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(2);
}
