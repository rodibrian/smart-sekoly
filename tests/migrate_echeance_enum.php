<?php
require_once __DIR__ . '/../config/database.php';

$pdo = get_connexion_base_donnees();

try {
    // Ajouter 'a_venir' à l'enum du statut_echeance
    $pdo->exec("ALTER TABLE echeance MODIFY statut_echeance ENUM('a_venir','payee','partielle','en_retard') NOT NULL DEFAULT 'a_venir'");
    echo "✓ Enum statut_echeance modifié : ajouté 'a_venir'\n";
    
    // Mettre les échéances non payées et futures en 'a_venir' (au lieu de 'payee')
    $pdo->exec("UPDATE echeance SET statut_echeance = 'a_venir' WHERE montant_paye = 0 AND date_echeance > CURDATE() AND statut_echeance = 'payee'");
    echo "✓ Mise à jour des statuts : non payées + futures = 'a_venir'\n";
    
} catch (Throwable $e) {
    echo "⚠ Erreur : " . $e->getMessage() . "\n";
}
