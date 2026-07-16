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

    public function traiter_formulaire(array $donnees_formulaire): array
    {
        $erreurs = [];

        $nom = nettoyer_chaine($donnees_formulaire['nom'] ?? '');
        $prenom = nettoyer_chaine($donnees_formulaire['prenom'] ?? '');
        $email = nettoyer_chaine($donnees_formulaire['email'] ?? '');
        $date_naissance = nettoyer_chaine($donnees_formulaire['date_naissance'] ?? '');
        $matricule = nettoyer_chaine($donnees_formulaire['matricule'] ?? '');

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
