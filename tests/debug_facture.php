<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Enable output buffering and logging
ob_start();
$log_file = __DIR__ . '/debug_facture_output.log';
file_put_contents($log_file, "=== Debug Facture " . date('Y-m-d H:i:s') . " ===\n", FILE_APPEND);

session_start();
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/ParametrageEtablissement.class.php';
require_once __DIR__ . '/../classes/TypeFraisDAO.class.php';
require_once __DIR__ . '/../classes/SequenceNumerotation.class.php';
require_once __DIR__ . '/../classes/FactureDAO.class.php';

function log_msg($msg) {
    global $log_file;
    file_put_contents($log_file, $msg . "\n", FILE_APPEND);
    echo $msg . "\n";
}

$pdo = get_connexion_base_donnees();
log_msg("DB connected: " . ($pdo instanceof PDO ? "OK" : "FAIL"));

// Clean up
try {
    $pdo->exec("DELETE FROM ligne_facture WHERE id_facture IN (SELECT id_facture FROM facture WHERE numero_sequentiel LIKE 'DEBUG-%')");
    $pdo->exec("DELETE FROM facture WHERE numero_sequentiel LIKE 'DEBUG-%'");
    $pdo->exec("DELETE FROM eleve WHERE id_eleve = 888888");
    $pdo->exec("DELETE FROM type_frais WHERE libelle LIKE 'DEBUG_%'");
    log_msg("Cleanup OK");
} catch (Throwable $e) {
    log_msg("Cleanup error: " . $e->getMessage());
}

try {
    // Create year
    $stmt = $pdo->prepare('INSERT INTO annee_scolaire (libelle, date_debut, date_fin, etat) VALUES (:libelle, :date_debut, :date_fin, :etat) ON DUPLICATE KEY UPDATE id_annee=id_annee');
    $stmt->execute([':libelle' => '2025-2026', ':date_debut' => '2025-09-15', ':date_fin' => '2026-06-30', ':etat' => 'active']);
    $stmt_year = $pdo->query("SELECT id_annee FROM annee_scolaire WHERE libelle='2025-2026' LIMIT 1");
    $year = $stmt_year->fetch(PDO::FETCH_ASSOC);
    $id_annee = (int) ($year['id_annee'] ?? 1);
    log_msg("✓ Année : $id_annee");
    
    // Create parametrage
    $stmt_param = $pdo->query("SELECT id_parametrage FROM parametrage_etablissement LIMIT 1");
    $param_row = $stmt_param->fetch(PDO::FETCH_ASSOC);
    if (!$param_row) {
        $stmt_p = $pdo->prepare(
            'INSERT INTO parametrage_etablissement (nom_etablissement, monnaie, format_matricule, prefixe_matricule, annee_courante) VALUES (:nom, :monnaie, :format, :prefixe, :annee_courante)'
        );
        $stmt_p->execute([':nom' => 'DEBUG', ':monnaie' => 'CFA', ':format' => '{PREFIXE}-{ANNEE}-{NUMERO_SEQUENTIEL}', ':prefixe' => 'FAC', ':annee_courante' => '2025-2026']);
    }
    log_msg("✓ Paramétrage OK");
    
    // Create eleve
    $pdo->exec("INSERT IGNORE INTO personne (nom, prenom, sexe) VALUES ('DEBUG', 'Test', 'F')");
    $stmt_personne = $pdo->query("SELECT id_personne FROM personne WHERE nom='DEBUG' AND prenom='Test' LIMIT 1");
    $personne = $stmt_personne->fetch(PDO::FETCH_ASSOC);
    $id_personne = (int) ($personne['id_personne'] ?? 1);
    
    $stmt_eleve = $pdo->prepare(
        'INSERT IGNORE INTO eleve (id_personne, id_eleve, matricule, date_entree, statut_scolaire) VALUES (:id_personne, :id_eleve, :matricule, :date_entree, :statut_scolaire)'
    );
    $stmt_eleve->execute([':id_personne' => $id_personne, ':id_eleve' => 888888, ':matricule' => 'DEBUG-888888', ':date_entree' => date('Y-m-d'), ':statut_scolaire' => 'actif']);
    log_msg("✓ Élève créé : 888888");
    
    // Create fee types
    $typeFraisDAO = new TypeFraisDAO();
    $id1 = $typeFraisDAO->creer(['libelle' => 'DEBUG_Type1', 'montant_defaut' => 10000.00]);
    $id2 = $typeFraisDAO->creer(['libelle' => 'DEBUG_Type2', 'montant_defaut' => 20000.00]);
    log_msg("✓ Types créés : $id1, $id2");
    
    // Test creerFacture
    log_msg("\n--- Appel creerFacture ---");
    $factureDAO = new FactureDAO();
    
    log_msg("Paramètres :");
    log_msg("  - id_eleve: 888888");
    log_msg("  - id_types_frais: [" . implode(', ', [$id1, $id2]) . "]");
    log_msg("  - id_annee: $id_annee");
    
    $id_facture = $factureDAO->creerFacture(888888, [$id1, $id2], $id_annee);
    
    log_msg("Résultat : " . var_export($id_facture, true));
    
    if ($id_facture && is_int($id_facture) && $id_facture > 0) {
        log_msg("✓ Facture générée : $id_facture");
        
        // Verify
        $stmt_verify = $pdo->query("SELECT * FROM facture WHERE id_facture = $id_facture");
        $facture = $stmt_verify->fetch(PDO::FETCH_ASSOC);
        log_msg("✓ Facture vérifiée en base :");
        log_msg("  - Numéro : " . $facture['numero_sequentiel']);
        log_msg("  - Montant : " . $facture['montant_total']);
    } else {
        log_msg("✗ Facture nulle ou invalide");
    }
    
} catch (Throwable $e) {
    log_msg("ERREUR : " . $e->getMessage());
    log_msg("Stack : " . $e->getTraceAsString());
}

log_msg("\n--- Fin ---");
$output = ob_get_clean();
file_put_contents($log_file, $output, FILE_APPEND);
echo $output;

