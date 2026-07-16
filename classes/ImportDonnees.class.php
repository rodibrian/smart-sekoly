<?php
/**
 * Classe d'import de données élèves depuis un fichier CSV.
 */
class ImportDonnees
{
    public function generer_modele(string $type = 'eleves'): string
    {
        if ($type === 'notes') {
            return "matricule,id_evaluation,date_evaluation,matiere,periode,valeur,appreciation,coefficient,enseignant\n";
        }

        return "nom,prenom,email,date_naissance,matricule\n";
    }

    public function importer(string $chemin_fichier, string $type = 'eleves'): array
    {
        if (!is_file($chemin_fichier)) {
            return [
                'total_lignes' => 0,
                'lignes_validees' => 0,
                'lignes_erreur' => 0,
                'erreurs' => ['Le fichier est introuvable.'],
            ];
        }

        $lignes = file($chemin_fichier, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lignes === false || count($lignes) <= 1) {
            return [
                'total_lignes' => 0,
                'lignes_validees' => 0,
                'lignes_erreur' => 0,
                'erreurs' => ['Le fichier ne contient aucune donnée à importer.'],
            ];
        }

        $entetes = array_map('strtolower', array_map('trim', str_getcsv(array_shift($lignes))));
        $colonnes_attendues = $type === 'notes'
            ? ['matricule', 'id_evaluation', 'date_evaluation', 'matiere', 'periode', 'valeur', 'appreciation', 'coefficient', 'enseignant']
            : ['nom', 'prenom', 'email', 'date_naissance', 'matricule'];

        if ($entetes !== $colonnes_attendues) {
            return [
                'total_lignes' => 0,
                'lignes_validees' => 0,
                'lignes_erreur' => 0,
                'erreurs' => ['Le format du fichier ne correspond pas au modèle attendu.'],
            ];
        }

        $total = 0;
        $validees = 0;
        $erreurs = [];
        $importes = 0;

        foreach ($lignes as $ligne) {
            $total++;
            $colonnes = str_getcsv($ligne);
            if (count($colonnes) < count($colonnes_attendues)) {
                $erreurs[] = 'Ligne ' . $total . ' : structure invalide.';
                continue;
            }

            $donnees = array_combine($colonnes_attendues, array_map('nettoyer_chaine', $colonnes));
            if ($donnees === false) {
                $erreurs[] = 'Ligne ' . $total . ' : impossible de lire la ligne.';
                continue;
            }

            if ($type === 'notes') {
                $erreursAvant = count($erreurs);
                $this->validerNote($donnees, $total, $erreurs);
                if (count($erreurs) > $erreursAvant) {
                    continue;
                }

                $id_eleve = $this->trouverIdEleveParMatricule($donnees['matricule']);
                if ($id_eleve === null) {
                    $erreurs[] = 'Ligne ' . $total . ' : matricule élève introuvable (' . $donnees['matricule'] . ').';
                    continue;
                }

                $evaluations = $_SESSION['evaluations'] ?? [];
                $id_evaluation = (int) $donnees['id_evaluation'];
                if (!isset($evaluations[$id_evaluation])) {
                    $evaluations[$id_evaluation] = [
                        'id_evaluation' => $id_evaluation,
                        'date_evaluation' => $donnees['date_evaluation'],
                        'matiere' => $donnees['matiere'],
                        'periode' => $donnees['periode'],
                        'coefficient' => is_numeric($donnees['coefficient']) ? (float) $donnees['coefficient'] : 1.0,
                        'enseignant' => $donnees['enseignant'],
                    ];
                    $_SESSION['evaluations'] = $evaluations;
                }

                $notes = $_SESSION['notes'] ?? [];
                $id_note = generer_identifiant($notes, 'id_note');
                $notes[$id_note] = [
                    'id_note' => $id_note,
                    'id_eleve' => $id_eleve,
                    'matricule' => $donnees['matricule'],
                    'id_evaluation' => $id_evaluation,
                    'date_evaluation' => $donnees['date_evaluation'],
                    'matiere' => $donnees['matiere'],
                    'periode' => $donnees['periode'],
                    'valeur' => (float) $donnees['valeur'],
                    'appreciation' => $donnees['appreciation'],
                    'coefficient' => is_numeric($donnees['coefficient']) ? (float) $donnees['coefficient'] : 1.0,
                    'enseignant' => $donnees['enseignant'],
                ];
                $_SESSION['notes'] = $notes;
                $validees++;
                $importes++;
                continue;
            }

            $erreursAvant = count($erreurs);
            $this->validerEleve($donnees, $total, $erreurs);
            if (count($erreurs) > $erreursAvant) {
                continue;
            }

            $eleves = $_SESSION['eleves'] ?? [];
            $id = generer_identifiant($eleves, 'id');
            $eleves[$id] = [
                'id' => $id,
                'nom' => $donnees['nom'],
                'prenom' => $donnees['prenom'],
                'email' => $donnees['email'],
                'date_naissance' => $donnees['date_naissance'],
                'matricule' => $donnees['matricule'],
                'statut' => 'Actif',
            ];
            $_SESSION['eleves'] = $eleves;
            $validees++;
            $importes++;
        }

        return [
            'total_lignes' => $total,
            'lignes_validees' => $validees,
            'lignes_erreur' => count($erreurs),
            'importes' => $importes,
            'erreurs' => $erreurs,
        ];
    }

    private function validerEleve(array $donnees, int $ligne, array &$erreurs): void
    {
        if ($donnees['nom'] === '' || $donnees['prenom'] === '' || $donnees['email'] === '' || $donnees['date_naissance'] === '' || $donnees['matricule'] === '') {
            $erreurs[] = 'Ligne ' . $ligne . ' : informations incomplètes.';
            return;
        }

        if (filter_var($donnees['email'], FILTER_VALIDATE_EMAIL) === false) {
            $erreurs[] = 'Ligne ' . $ligne . ' : email invalide.';
            return;
        }

        if (strtotime($donnees['date_naissance']) === false) {
            $erreurs[] = 'Ligne ' . $ligne . ' : date de naissance invalide.';
        }
    }

    private function validerNote(array $donnees, int $ligne, array &$erreurs): void
    {
        if ($donnees['matricule'] === '' || $donnees['id_evaluation'] === '' || $donnees['date_evaluation'] === '' || $donnees['matiere'] === '' || $donnees['periode'] === '' || $donnees['valeur'] === '') {
            $erreurs[] = 'Ligne ' . $ligne . ' : informations de note incomplètes.';
            return;
        }

        if (!is_numeric($donnees['id_evaluation'])) {
            $erreurs[] = 'Ligne ' . $ligne . ' : identifiant d’évaluation invalide.';
            return;
        }

        if (strtotime($donnees['date_evaluation']) === false) {
            $erreurs[] = 'Ligne ' . $ligne . ' : date d’évaluation invalide.';
            return;
        }

        if (!is_numeric($donnees['valeur'])) {
            $erreurs[] = 'Ligne ' . $ligne . ' : valeur de note invalide.';
            return;
        }

        if ($donnees['coefficient'] !== '' && !is_numeric($donnees['coefficient'])) {
            $erreurs[] = 'Ligne ' . $ligne . ' : coefficient invalide.';
        }
    }

    private function trouverIdEleveParMatricule(string $matricule): ?int
    {
        foreach ($_SESSION['eleves'] ?? [] as $eleve) {
            if (isset($eleve['matricule']) && strcasecmp($eleve['matricule'], $matricule) === 0) {
                return (int) $eleve['id'];
            }
        }

        return null;
    }
}
