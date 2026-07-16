<?php
/**
 * Journal de suivi produit pour tracer les étapes de développement.
 */
class JournalSuivi
{
    private string $fichier;

    public function __construct(?string $fichier = null)
    {
        $racine = defined('ROOT_PATH') ? ROOT_PATH : dirname(__DIR__) . DIRECTORY_SEPARATOR;
        $this->fichier = $fichier ?? $racine . 'logs' . DIRECTORY_SEPARATOR . 'journal_suivi.log';
        if (!is_dir(dirname($this->fichier))) {
            mkdir(dirname($this->fichier), 0777, true);
        }
    }

    public function ajouter(string $categorie, string $message): void
    {
        $ligne = date('Y-m-d H:i:s') . " | " . $categorie . " | " . $message . PHP_EOL;
        file_put_contents($this->fichier, $ligne, FILE_APPEND);
    }

    public function lister(int $limit = 20): array
    {
        if (!is_file($this->fichier)) {
            return [];
        }

        $lignes = file($this->fichier, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lignes === false) {
            return [];
        }

        $resultat = [];
        foreach (array_slice(array_reverse($lignes), 0, $limit) as $ligne) {
            $parts = explode(' | ', $ligne, 3);
            if (count($parts) === 3) {
                $resultat[] = [
                    'date' => $parts[0],
                    'categorie' => $parts[1],
                    'message' => $parts[2],
                ];
            }
        }

        return array_reverse($resultat);
    }
}
