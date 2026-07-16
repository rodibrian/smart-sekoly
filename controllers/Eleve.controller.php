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

        $donnees = [
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
        ];

        require TEMPLATES_PATH . 'eleves/formulaire_inscription.view.php';
    }

    public function preparer_donnees_dossier(): array
    {
        $id_eleve = (int) ($this->parametre ?? 0);

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

        $documents = [
            new DocumentObligatoire(['nom' => 'CNI', 'statut' => 'recu']),
            new DocumentObligatoire(['nom' => 'Certificat de naissance', 'statut' => 'recu']),
            new DocumentObligatoire(['nom' => 'Photo d’identité', 'statut' => 'manquant']),
            new DocumentObligatoire(['nom' => 'Bulletin précédent', 'statut' => 'manquant']),
        ];

        return [
            'id_eleve' => $id_eleve,
            'documents' => array_map(function (DocumentObligatoire $document): array {
                return [
                    'nom' => $document->get_nom(),
                    'statut' => $document->get_statut(),
                ];
            }, $documents),
            'module' => $this->module,
            'action' => $this->action,
            'token_csrf' => generer_token_csrf(),
        ];
    }

    public function preparer_carnet_suivi(): array
    {
        $id_eleve = (int) ($this->parametre ?? 0);
        $carnet = new CarnetSuivi($id_eleve);
        $carnet->ajouter_evenement('Rappel', 'Documents à fournir', 'info');
        $carnet->ajouter_evenement('Absence', 'Absence non justifiée', 'warning');

        return [
            'id_eleve' => $id_eleve,
            'evenements' => $carnet->get_evenements(),
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

        return [
            'valide' => empty($erreurs),
            'erreurs' => $erreurs,
            'donnees' => [
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'date_naissance' => $date_naissance,
                'matricule' => $matricule,
            ],
        ];
    }
}
