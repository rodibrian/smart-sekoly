<?php
/**
 * Contrôleur de gestion des élèves.
 *
 * @package Smart-Sekoly
 * @subpackage Controllers
 */
class EleveController
{
    private $module;
    private $action;
    private $parametre;

    public function __construct($module = 'eleves', $action = 'inscription', $parametre = null)
    {
        $this->module = $module;
        $this->action = $action;
        $this->parametre = $parametre;
    }

    public function executer(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->action === 'inscription') {
            $resultat = $this->traiter_formulaire($_POST);
            if ($resultat['valide']) {
                $this->enregistrer_inscription($resultat['donnees']);
                $_SESSION['messages']['eleve'] = 'Inscription enregistrée avec succès.';
                header('Location: ' . BASE_URL . '/eleves/dossier/1');
                return;
            }

            $daoRole = new RoleDAO();
            $donnees = [
                'module' => $this->module,
                'action' => $this->action,
                'token_csrf' => generer_token_csrf(),
                'erreurs' => $resultat['erreurs'],
                'valeurs' => $resultat['donnees'],
                'roles' => $daoRole->listerRoles(),
            ];
            require TEMPLATES_PATH . 'eleves/formulaire_inscription.view.php';
            return;
        }

        if ($this->action === 'liste') {
            $donnees = $this->preparer_liste_eleves();
            require TEMPLATES_PATH . 'eleves/liste.view.php';
            return;
        }

        if ($this->action === 'edition') {
            $donnees = $this->preparer_formulaire_edition();
            require TEMPLATES_PATH . 'eleves/edition.view.php';
            return;
        }

        if ($this->action === 'dossier') {
            $donnees = $this->preparer_donnees_dossier();
            require TEMPLATES_PATH . 'eleves/dossier.view.php';
            return;
        }

        if ($this->action === 'documents') {
            $donnees = $this->preparer_documents_obligatoires();
            require TEMPLATES_PATH . 'eleves/documents.view.php';
            return;
        }

        if ($this->action === 'documents-post') {
            $this->traiter_documents_obligatoires();
            header('Location: ' . BASE_URL . '/eleves/documents/' . ($this->parametre ?? 1));
            return;
        }

        if ($this->action === 'carnet') {
            $donnees = $this->preparer_carnet_suivi();
            require TEMPLATES_PATH . 'eleves/carnet.view.php';
            return;
        }

        if ($this->action === 'changement-classe') {
            $donnees = $this->preparer_changement_classe();
            require TEMPLATES_PATH . 'eleves/changement_classe.view.php';
            return;
        }

        if ($this->action === 'redoublement') {
            $donnees = $this->preparer_redoublement();
            require TEMPLATES_PATH . 'eleves/redoublement.view.php';
            return;
        }

        if ($this->action === 'transfert') {
            $donnees = $this->preparer_transfert();
            require TEMPLATES_PATH . 'eleves/transfert.view.php';
            return;
        }

        if ($this->action === 'absences') {
            $donnees = $this->preparer_absences();
            require TEMPLATES_PATH . 'eleves/absences.view.php';
            return;
        }

        if ($this->action === 'sanctions') {
            $donnees = $this->preparer_sanctions();
            require TEMPLATES_PATH . 'eleves/sanctions.view.php';
            return;
        }

        $daoRole = new RoleDAO();

        $donnees = [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'roles' => $daoRole->listerRoles(),
            'valeurs' => [
                'role_id' => 0,
            ],
        ];

        require TEMPLATES_PATH . 'eleves/formulaire_inscription.view.php';
    }

    private function enregistrer_inscription(array $donnees): void
    {
        $dao = new EleveDAO();
        $id_eleve = $dao->creerEleve([
            'nom' => $donnees['nom'],
            'prenom' => $donnees['prenom'],
            'email' => $donnees['email'],
            'date_naissance' => $donnees['date_naissance'],
            'matricule' => $donnees['matricule'],
            'statut_scolaire' => 'actif',
        ]);

        if (!empty($donnees['role_id'])) {
            $roleDao = new RoleDAO();
            $roleDao->assignerRoleAPersonne($id_eleve, (int) $donnees['role_id']);
        }

        $_SESSION['dernier_id_eleve'] = $id_eleve;
    }

    private function preparer_formulaire_edition(): array
    {
        $id_eleve = (int) ($this->parametre ?? 0);
        $dao = new EleveDAO();
        $roleDao = new RoleDAO();
        $eleve = $dao->trouverParId($id_eleve) ?? [
            'id' => $id_eleve,
            'nom' => 'Andriamihaja',
            'prenom' => 'Lova',
            'email' => 'lova@example.com',
            'date_naissance' => '2015-03-05',
            'matricule' => 'EL-2026-001',
            'statut' => 'Actif',
        ];

        $roleSelectionne = $roleDao->trouverPremierRolePersonne($id_eleve);
        $roleId = (int) ($roleSelectionne['id_role'] ?? 0);

        $erreurs = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $eleve['nom'] = nettoyer_chaine($_POST['nom'] ?? $eleve['nom']);
            $eleve['prenom'] = nettoyer_chaine($_POST['prenom'] ?? $eleve['prenom']);
            $eleve['email'] = nettoyer_chaine($_POST['email'] ?? $eleve['email']);
            $eleve['date_naissance'] = nettoyer_chaine($_POST['date_naissance'] ?? $eleve['date_naissance']);
            $eleve['matricule'] = nettoyer_chaine($_POST['matricule'] ?? $eleve['matricule']);
            $roleId = (int) ($_POST['role_id'] ?? $roleId);

            if ($roleId <= 0 || $roleDao->trouverRoleParId($roleId) === null) {
                $erreurs['role_id'] = 'Le rôle sélectionné est invalide.';
            }

            if (empty($erreurs)) {
                $dao->mettreAJour($id_eleve, [
                    'nom' => $eleve['nom'],
                    'prenom' => $eleve['prenom'],
                    'email' => $eleve['email'],
                    'date_naissance' => $eleve['date_naissance'],
                    'matricule' => $eleve['matricule'],
                    'statut_scolaire' => 'actif',
                ]);

                $roleDao->assignerRoleAPersonne($id_eleve, $roleId);
                $_SESSION['messages']['eleve'] = 'Profil mis à jour.';
            }
        }

        return [
            'id_eleve' => $id_eleve,
            'eleve' => $eleve,
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
            'roles' => $roleDao->listerRoles(),
            'role_id' => $roleId,
            'erreurs' => $erreurs,
        ];
    }

    public function preparer_liste_eleves(): array
    {
        $dao = new EleveDAO();
        $eleves = $dao->listerEleves();
        $recherche = nettoyer_chaine($_GET['q'] ?? '');
        $liste = [];

        foreach ($eleves as $eleve) {
            if (!is_array($eleve)) {
                continue;
            }
            $nom = $eleve['nom'] ?? '';
            $prenom = $eleve['prenom'] ?? '';
            $matricule = $eleve['matricule'] ?? '';
            $texte_recherche = strtolower($recherche);
            $correspond = $recherche === '' || strpos(strtolower($nom), $texte_recherche) !== false || strpos(strtolower($prenom), $texte_recherche) !== false || strpos(strtolower($matricule), $texte_recherche) !== false;

            if ($correspond) {
                $liste[] = [
                    'id' => $eleve['id'] ?? 0,
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'matricule' => $matricule,
                    'email' => $eleve['email'] ?? '',
                    'statut' => $eleve['statut'] ?? 'Actif',
                ];
            }
        }

        if (empty($liste)) {
            $liste[] = [
                'id' => 1,
                'nom' => 'Andriamihaja',
                'prenom' => 'Lova',
                'matricule' => 'EL-2026-001',
                'email' => 'lova@example.com',
                'statut' => 'Actif',
            ];
        }

        return [
            'eleves' => $liste,
            'recherche' => $recherche,
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
        ];
    }

    public function preparer_donnees_dossier(): array
    {
        $id_eleve = (int) ($this->parametre ?? 0);

        $dao = new EleveDAO();
        $eleve_sauvegarde = $dao->trouverParId($id_eleve);

        $eleve = [
            'id' => $id_eleve,
            'nom' => 'Andriamihaja',
            'prenom' => 'Lova',
            'email' => 'lova@example.com',
            'matricule' => 'EL-2026-001',
            'statut' => 'Actif',
            'date_naissance' => '2015-03-05',
            'inscriptions' => [
                ['annee' => '2025-2026', 'classe' => '6e A', 'statut' => 'Actif'],
                ['annee' => '2024-2025', 'classe' => '5e B', 'statut' => 'Terminé'],
            ],
        ];

        if ($eleve_sauvegarde !== null) {
            $eleve['nom'] = $eleve_sauvegarde['nom'] ?? $eleve['nom'];
            $eleve['prenom'] = $eleve_sauvegarde['prenom'] ?? $eleve['prenom'];
            $eleve['email'] = $eleve_sauvegarde['email'] ?? $eleve['email'];
            $eleve['matricule'] = $eleve_sauvegarde['matricule'] ?? $eleve['matricule'];
            $eleve['date_naissance'] = $eleve_sauvegarde['date_naissance'] ?? $eleve['date_naissance'];
            $eleve['statut'] = $eleve_sauvegarde['statut'] ?? $eleve['statut'];
        }

        return [
            'id_eleve' => $id_eleve,
            'eleve' => $eleve,
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
        ];
    }

    public function preparer_documents_obligatoires(): array
    {
        $id_eleve = (int) ($this->parametre ?? 0);

        $documents = $_SESSION['documents_eleves'][$id_eleve] ?? [
            ['nom' => 'CNI', 'statut' => 'recu'],
            ['nom' => 'Certificat de naissance', 'statut' => 'recu'],
            ['nom' => 'Photo d’identité', 'statut' => 'manquant'],
            ['nom' => 'Bulletin précédent', 'statut' => 'manquant'],
        ];

        return [
            'id_eleve' => $id_eleve,
            'documents' => array_map(function (array $document): array {
                return [
                    'nom' => $document['nom'] ?? '',
                    'statut' => $document['statut'] ?? 'manquant',
                ];
            }, $documents),
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
        ];
    }

    private function traiter_documents_obligatoires(): void
    {
        $id_eleve = (int) ($this->parametre ?? 0);
        $documents = [];

        foreach ($_POST['documents'] ?? [] as $nom => $statut) {
            $documents[] = [
                'nom' => nettoyer_chaine($nom),
                'statut' => nettoyer_chaine($statut),
            ];
        }

        if (empty($documents)) {
            $documents = [
                ['nom' => 'CNI', 'statut' => 'recu'],
                ['nom' => 'Certificat de naissance', 'statut' => 'recu'],
                ['nom' => 'Photo d’identité', 'statut' => 'manquant'],
                ['nom' => 'Bulletin précédent', 'statut' => 'manquant'],
            ];
        }

        $_SESSION['documents_eleves'][$id_eleve] = $documents;
    }

    public function preparer_carnet_suivi(): array
    {
        $id_eleve = (int) ($this->parametre ?? 0);
        $evenements = $_SESSION['carnets'][$id_eleve] ?? [];

        if (empty($evenements)) {
            $carnet = new CarnetSuivi($id_eleve);
            $carnet->ajouter_evenement('Rappel', 'Documents à fournir', 'info');
            $carnet->ajouter_evenement('Absence', 'Absence non justifiée', 'warning');
            $evenements = $carnet->get_evenements();
        }

        return [
            'id_eleve' => $id_eleve,
            'evenements' => $evenements,
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
        ];
    }

    public function preparer_changement_classe(): array
    {
        $id_eleve = (int) ($this->parametre ?? 0);
        $changement = new ChangementClasse([
            'id_eleve' => $id_eleve,
            'ancienne_classe' => '5e B',
            'nouvelle_classe' => '6e A',
        ]);
        $changement->valider();

        return [
            'id_eleve' => $id_eleve,
            'changement' => [
                'ancienne_classe' => $changement->get_ancienne_classe(),
                'nouvelle_classe' => $changement->get_nouvelle_classe(),
                'statut' => $changement->get_statut(),
            ],
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
        ];
    }

    public function preparer_redoublement(): array
    {
        $id_eleve = (int) ($this->parametre ?? 0);
        $redoublement = new Redoublement(['id_eleve' => $id_eleve]);
        $redoublement->proposer('Faible progression');
        $redoublement->valider();

        return [
            'id_eleve' => $id_eleve,
            'redoublement' => [
                'motif' => $redoublement->get_motif(),
                'decision' => $redoublement->get_decision(),
            ],
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
        ];
    }

    public function preparer_transfert(): array
    {
        $id_eleve = (int) ($this->parametre ?? 0);
        $transfert = new TransfertEleve([
            'id_eleve' => $id_eleve,
            'type' => 'depart',
            'destination' => 'Lycée Moderne',
        ]);
        $transfert->valider();

        return [
            'id_eleve' => $id_eleve,
            'transfert' => [
                'type' => $transfert->get_type(),
                'destination' => $transfert->get_destination(),
                'statut' => $transfert->get_statut(),
            ],
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
        ];
    }

    public function preparer_absences(): array
    {
        $id_eleve = (int) ($this->parametre ?? 0);
        $absences = [
            new Absence(['id_eleve' => $id_eleve, 'date_absence' => '2026-07-10', 'motif' => 'Maladie', 'statut' => 'valide']),
            new Absence(['id_eleve' => $id_eleve, 'date_absence' => '2026-07-15', 'motif' => 'Rendez-vous médical', 'statut' => 'en_attente']),
        ];

        return [
            'id_eleve' => $id_eleve,
            'absences' => array_map(function (Absence $absence): array {
                return [
                    'date_absence' => $absence->get_date_absence(),
                    'motif' => $absence->get_motif(),
                    'statut' => $absence->get_statut(),
                ];
            }, $absences),
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
        ];
    }

    public function preparer_sanctions(): array
    {
        $id_eleve = (int) ($this->parametre ?? 0);
        $sanctions = [
            new Sanction(['id_eleve' => $id_eleve, 'type' => 'avertissement', 'description' => 'Retard répété', 'statut' => 'validee']),
            new Sanction(['id_eleve' => $id_eleve, 'type' => 'blâme', 'description' => 'Comportement inapproprié', 'statut' => 'proposee']),
        ];

        return [
            'id_eleve' => $id_eleve,
            'sanctions' => array_map(function (Sanction $sanction): array {
                return [
                    'type' => $sanction->get_type(),
                    'description' => $sanction->get_description(),
                    'statut' => $sanction->get_statut(),
                ];
            }, $sanctions),
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
        ];
    }

    public function traiter_formulaire(array $donnees_formulaire): array
    {
        $erreurs = [];

        $nom = nettoyer_chaine($donnees_formulaire['nom'] ?? '');
        $prenom = nettoyer_chaine($donnees_formulaire['prenom'] ?? '');
        $email = nettoyer_chaine($donnees_formulaire['email'] ?? '');
        $date_naissance = nettoyer_chaine($donnees_formulaire['date_naissance'] ?? '');
        $matricule = nettoyer_chaine($donnees_formulaire['matricule'] ?? '');

        if ($matricule === '') {
            $matricule = generer_matricule();
        }

        if ($nom === '') {
            $erreurs['nom'] = 'Le nom est obligatoire.';
        }

        if ($prenom === '') {
            $erreurs['prenom'] = 'Le prénom est obligatoire.';
        }

        if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $erreurs['email'] = 'L’email est invalide.';
        }

        if ($date_naissance === '' || strtotime($date_naissance) === false) {
            $erreurs['date_naissance'] = 'La date de naissance est invalide.';
        }

        if ($matricule === '') {
            $erreurs['matricule'] = 'Le matricule est obligatoire.';
        }

        $roleId = (int) ($donnees_formulaire['role_id'] ?? 0);
        $roleDao = new RoleDAO();
        if ($roleId <= 0 || $roleDao->trouverRoleParId($roleId) === null) {
            $erreurs['role_id'] = 'Le rôle sélectionné est invalide.';
        }

        return [
            'valide' => empty($erreurs),
            'erreurs' => $erreurs,
            'donnees' => [
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'date_naissance' => $date_naissance,
                'matricule' => $matricule,
                'role_id' => $roleId,
            ],
        ];
    }
}
