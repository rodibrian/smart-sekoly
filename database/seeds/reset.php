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

$statements = preg_split('/;\s*\n/', $sql);

try {
    $pdo->beginTransaction();

    foreach ($statements as $statement) {
        $statement = trim($statement);
        if ($statement === '' || str_starts_with($statement, '--')) {
            continue;
        }

        $pdo->exec($statement);
    }

    $pdo->commit();
    echo "Réinitialisation de la base terminée avec succès.\n";
    echo "Vous pouvez ensuite exécuter `php database/seeds/seed_etablissement.php` pour charger les données initiales.\n";
} catch (Throwable $exception) {
    $pdo->rollBack();
    echo "Erreur lors de la réinitialisation de la base : " . $exception->getMessage() . "\n";
    exit(1);
}
