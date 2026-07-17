<?php
require_once __DIR__ . '/config/database.php';
$pdo = get_connexion_base_donnees();
if (!$pdo) {
    echo "No PDO\n";
    exit(1);
}
try {
    $pdo->beginTransaction();
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
    $tables = [
        'sequence_numerotation', 'modele_document', 'parametrage_etablissement', 'document_personne', 'document_administratif',
        'version_document', 'planning_surveillant', 'agenda_evenement', 'seance_reelle', 'emploi_du_temps', 'creneau_horaire',
        'salaire', 'heure_supplementaire', 'paiement', 'mouvement_caisse', 'echeance', 'facture_remise', 'remise', 'ligne_facture',
        'facture', 'mouvement_stock', 'pret_materiel', 'materiel', 'billet', 'evenement_carnet', 'incident_eleve', 'incident',
        'sanction', 'retard', 'absence', 'bulletin', 'note', 'evaluation', 'periode', 'affectation', 'contrat', 'inscription',
        'transfert', 'programme', 'matiere', 'salle', 'classe', 'serie', 'niveau', 'cycle', 'acces_parent_eleve', 'role_permission',
        'permission', 'utilisateur', 'personnel_administratif', 'enseignant', 'eleve', 'personne_role', 'personne', 'journal_audit',
        'journal_connexion'
    ];
    foreach ($tables as $table) {
        echo "truncating {$table}\n";
        $pdo->exec("TRUNCATE TABLE {$table}");
    }
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    echo "truncates succeeded\n";
    $stmt = $pdo->prepare('INSERT INTO parametrage_etablissement (nom_etablissement, logo, monnaie, langue_par_defaut, theme_par_defaut, chemin_stockage_documents) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute(['Smart-Sekoly', '', 'MGA', 'fr', 'light', 'documents/']);
    echo 'insert ok id=' . $pdo->lastInsertId() . "\n";
    $pdo->commit();
} catch (Throwable $e) {
    echo 'error: ' . $e->getMessage() . "\n";
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
}
