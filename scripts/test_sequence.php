<?php
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../classes/ParametrageEtablissement.class.php';
require __DIR__ . '/../classes/SequenceNumerotation.class.php';

$pdo = get_connexion_base_donnees();
if (!$pdo) {
    echo "DB unavailable\n";
    exit(1);
}

// find active year
$stmt = $pdo->query("SELECT id_annee, libelle FROM annee_scolaire WHERE etat = 'active' LIMIT 1");
$annee = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$annee) {
    echo "No active school year found.\n";
    exit(1);
}

try {
    $res = SequenceNumerotation::getNext('matricule', (int)$annee['id_annee']);
    echo "Next numero: " . $res['numero'] . "\n";
    echo "Formaté: " . $res['formatte'] . "\n";
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

exit(0);
