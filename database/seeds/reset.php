<?php

require_once __DIR__ . '/../../config/database.php';

$pdo = get_connexion_base_donnees();
if ($pdo === null) {
    echo "Erreur : impossible de se connecter à la base de données.\n";
    exit(1);
}

$schemaFile = __DIR__ . '/../schema_smart_sekoly.sql';
if (!file_exists($schemaFile)) {
    echo "Erreur : fichier de schéma introuvable : {$schemaFile}\n";
    exit(1);
}

$sql = file_get_contents($schemaFile);
if ($sql === false) {
    echo "Erreur : impossible de lire le fichier de schéma.\n";
    exit(1);
}

$sql = str_replace(["\r\n", "\r"], "\n", $sql);

try {
    $database = DB_NAME;
    $tables = $pdo->query("SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema = '" . addslashes($database) . "'")->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($tables)) {
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
        foreach ($tables as $table) {
            $pdo->exec('DROP TABLE IF EXISTS `' . $table . '`');
        }
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    }

    $statements = preg_split('/;\s*\n+/', $sql);
    foreach ($statements as $statement) {
        $lines = preg_split('/\n/', trim($statement));
        $lines = array_filter($lines, function ($line) {
            $line = trim($line);
            return $line !== '' && !str_starts_with($line, '--') && !str_starts_with($line, '#');
        });

        if (empty($lines)) {
            continue;
        }

        $statement = implode("\n", $lines);
        $pdo->exec($statement);
    }

    echo "Réinitialisation de la base terminée avec succès.\n";
    echo "Vous pouvez ensuite exécuter `php database/seeds/seed_etablissement.php` pour charger les données initiales.\n";
} catch (Throwable $exception) {
    echo "Erreur lors de la réinitialisation de la base : " . $exception->getMessage() . "\n";
    exit(1);
}
