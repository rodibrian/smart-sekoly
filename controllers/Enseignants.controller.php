<?php

class EnseignantsController
{
    private $module;
    private $action;
    private $parametre;

    public function __construct($module = 'enseignants', $action = 'index', $parametre = null)
    {
        $this->module = $module;
        $this->action = $action;
        $this->parametre = $parametre;
    }

    public function executer(): void
    {
        $dao = new EnseignantDAO();

        if ($this->action === 'liste') {
            $donnees = [
                'module' => $this->module,
                'action' => $this->action,
                'enseignants' => $dao->listerEnseignants(),
            ];
            require TEMPLATES_PATH . 'enseignants/liste.view.php';
            return;
        }

        if ($this->action === 'inscription') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $resultat = $this->traiterInscription($_POST);
                if ($resultat['valide']) {
                    $id = $dao->creerEnseignant($resultat['donnees']);
                    $_SESSION['messages']['enseignant'] = 'Enseignant ajouté avec succès.';
                    header('Location: ' . BASE_URL . '/enseignants/fiche/' . $id);
                    return;
                }

                $donnees = [
                    'module' => $this->module,
                    'action' => $this->action,
                    'token_csrf' => generer_token_csrf(),
                    'valeurs' => $resultat['donnees'],
                    'erreurs' => $resultat['erreurs'],
                ];
                require TEMPLATES_PATH . 'enseignants/formulaire_inscription.view.php';
                return;
            }

            $donnees = [
                'module' => $this->module,
                'action' => $this->action,
                'token_csrf' => generer_token_csrf(),
                'valeurs' => [
                    'statut_enseignant' => 'actif',
                    'date_embauche' => date('Y-m-d'),
                ],
                'erreurs' => [],
            ];
            require TEMPLATES_PATH . 'enseignants/formulaire_inscription.view.php';
            return;
        }

        if ($this->action === 'fiche') {
            $id = (int) ($this->parametre ?? 0);
            $enseignant = $dao->trouverParId($id) ?? [
                'id' => $id,
                'nom' => 'Rakoto',
                'prenom' => 'Jean',
                'email' => 'jean@example.com',
                'matricule' => 'ENS-2026-001',
                'date_embauche' => '2024-01-15',
                'statut' => 'actif',
            ];

            $donnees = [
                'module' => $this->module,
                'action' => $this->action,
                'token_csrf' => generer_token_csrf(),
                'enseignant' => $enseignant,
            ];
            require TEMPLATES_PATH . 'enseignants/fiche.view.php';
            return;
        }

        if ($this->action === 'edition') {
            $id = (int) ($this->parametre ?? 0);
            $enseignant = $dao->trouverParId($id);
            if ($enseignant === null) {
                header('Location: ' . BASE_URL . '/enseignants/liste');
                return;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $resultat = $this->traiterInscription($_POST);
                if ($resultat['valide']) {
                    $dao->mettreAJour($id, $resultat['donnees']);
                    $_SESSION['messages']['enseignant'] = 'Profil enseignant mis à jour.';
                    header('Location: ' . BASE_URL . '/enseignants/fiche/' . $id);
                    return;
                }

                $enseignant = array_merge($enseignant, $resultat['donnees']);
                $donnees = [
                    'module' => $this->module,
                    'action' => $this->action,
                    'token_csrf' => generer_token_csrf(),
                    'id_enseignant' => $id,
                    'enseignant' => $enseignant,
                    'erreurs' => $resultat['erreurs'],
                ];
                require TEMPLATES_PATH . 'enseignants/edition.view.php';
                return;
            }

            $donnees = [
                'module' => $this->module,
                'action' => $this->action,
                'token_csrf' => generer_token_csrf(),
                'id_enseignant' => $id,
                'enseignant' => $enseignant,
                'erreurs' => [],
            ];
            require TEMPLATES_PATH . 'enseignants/edition.view.php';
            return;
        }

        header('Location: ' . BASE_URL . '/enseignants/liste');
    }

    private function traiterInscription(array $donnees): array
    {
        $erreurs = [];

        $nom = nettoyer_chaine($donnees['nom'] ?? '');
        $prenom = nettoyer_chaine($donnees['prenom'] ?? '');
        $email = nettoyer_chaine($donnees['email'] ?? '');
        $date_naissance = nettoyer_chaine($donnees['date_naissance'] ?? '');
        $matricule = nettoyer_chaine($donnees['matricule'] ?? '');
        $date_embauche = nettoyer_chaine($donnees['date_embauche'] ?? '');
        $statut = nettoyer_chaine($donnees['statut_enseignant'] ?? 'actif');

        if ($nom === '') {
            $erreurs[] = 'Le nom est obligatoire.';
        }
        if ($prenom === '') {
            $erreurs[] = 'Le prénom est obligatoire.';
        }
        if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $erreurs[] = 'L’email est invalide.';
        }
        if ($date_naissance === '' || strtotime($date_naissance) === false) {
            $erreurs[] = 'La date de naissance est invalide.';
        }
        if ($matricule === '') {
            $erreurs[] = 'Le matricule est obligatoire.';
        }
        if ($date_embauche === '' || strtotime($date_embauche) === false) {
            $erreurs[] = 'La date d’embauche est invalide.';
        }
        if (!in_array($statut, ['actif', 'en_conge', 'sorti'], true)) {
            $erreurs[] = 'Le statut enseignant est invalide.';
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
                'date_embauche' => $date_embauche,
                'statut_enseignant' => $statut,
            ],
        ];
    }
}
