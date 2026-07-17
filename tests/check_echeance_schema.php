<?php
require_once __DIR__ . '/../config/database.php';

$pdo = get_connexion_base_donnees();

// Vérifier la structure de la table echeance
try {
    $stmt = $pdo->query("DESCRIBE echeance");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "=== Structure table echeance ===\n";
    foreach ($columns as $col) {
        echo "- " . $col['Field'] . " (" . $col['Type'] . ")\n";
    }
} catch (Throwable $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
}
