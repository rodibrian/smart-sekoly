<?php
/**
 * Résumé de suivi du backlog basé sur les statuts présents dans le fichier BACKLOG.md.
 */
class SuiviProjet
{
    private string $backlogPath;

    public function __construct(?string $backlogPath = null)
    {
        $racine = defined('ROOT_PATH') ? ROOT_PATH : dirname(__DIR__) . DIRECTORY_SEPARATOR;
        $this->backlogPath = $backlogPath ?? $racine . 'BACKLOG.md';
    }

    public function generer_resume(): array
    {
        if (!is_file($this->backlogPath)) {
            return ['fait' => 0, 'en_cours' => 0, 'a_faire' => 0, 'bloque' => 0, 'annule' => 0];
        }

        $contenu = file_get_contents($this->backlogPath);
        $resume = ['fait' => 0, 'en_cours' => 0, 'a_faire' => 0, 'bloque' => 0, 'annule' => 0];

        foreach (explode("\n", $contenu) as $ligne) {
            if (strpos($ligne, '✅') !== false) {
                $resume['fait']++;
            } elseif (strpos($ligne, '🔄') !== false) {
                $resume['en_cours']++;
            } elseif (strpos($ligne, '⏳') !== false) {
                $resume['a_faire']++;
            } elseif (strpos($ligne, '⚠️') !== false) {
                $resume['bloque']++;
            } elseif (strpos($ligne, '❌') !== false) {
                $resume['annule']++;
            }
        }

        return $resume;
    }
}
