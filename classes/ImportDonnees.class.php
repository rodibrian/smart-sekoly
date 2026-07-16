<?php
/**
 * Classe d'import de données élèves depuis un fichier CSV.
 */
class ImportDonnees
{
    public function generer_modele(): string
    {
        return "nom,prenom,email,date_naissance,matricule\n";
    }

    public function importer(string $chemin_fichier): array
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

        $total = 0;
        $validees = 0;
        $erreurs = [];
        $importes = 0;

        foreach (array_slice($lignes, 1) as $ligne) {
            $total++;
            $colonnes = str_getcsv($ligne);
            if (count($colonnes) < 5) {
                $erreurs[] = 'Ligne ' . $total . ' : structure invalide.';
                continue;
            }

            $nom = nettoyer_chaine($colonnes[0] ?? '');
            $prenom = nettoyer_chaine($colonnes[1] ?? '');
            $email = nettoyer_chaine($colonnes[2] ?? '');
            $date_naissance = nettoyer_chaine($colonnes[3] ?? '');
            $matricule = nettoyer_chaine($colonnes[4] ?? '');

            if ($nom === '' || $prenom === '' || $email === '' || $date_naissance === '' || $matricule === '') {
                $erreurs[] = 'Ligne ' . $total . ' : informations incomplètes.';
                continue;
            }

            if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
                $erreurs[] = 'Ligne ' . $total . ' : email invalide.';
                continue;
            }

            if (strtotime($date_naissance) === false) {
                $erreurs[] = 'Ligne ' . $total . ' : date de naissance invalide.';
                continue;
            }

            $validees++;
            $importes++;

            $eleves = $_SESSION['eleves'] ?? [];
            $id = generer_identifiant($eleves, 'id');
            $eleves[$id] = [
                'id' => $id,
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'date_naissance' => $date_naissance,
                'matricule' => $matricule,
                'statut' => 'Actif',
            ];
            $_SESSION['eleves'] = $eleves;
        }

        return [
            'total_lignes' => $total,
            'lignes_validees' => $validees,
            'lignes_erreur' => count($erreurs),
            'importes' => $importes,
            'erreurs' => $erreurs,
        ];
    }
}
