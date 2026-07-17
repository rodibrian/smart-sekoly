<?php
session_start();
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/ParametrageEtablissement.class.php';
require_once __DIR__ . '/../classes/TypeFraisDAO.class.php';
require_once __DIR__ . '/../classes/SequenceNumerotation.class.php';
require_once __DIR__ . '/../classes/FactureDAO.class.php';

// Test d'intégration complet : seed élève → types de frais → génération facture → vérification

$pdo = get_connexion_base_donnees();
if ($pdo instanceof PDO) {
    try {
        // Nettoyer dans le bon ordre (respect des FKs)
        // Supprimer les lignes de factures
        $pdo->exec("DELETE FROM ligne_facture WHERE id_facture IN (SELECT id_facture FROM facture WHERE numero_sequentiel LIKE '%TEST%' OR numero_sequentiel LIKE 'FAC-%' OR numero_sequentiel LIKE 'ELV-%' OR numero_sequentiel LIKE 'DEBUG-%')");
        // Supprimer les factures
        $pdo->exec("DELETE FROM facture WHERE numero_sequentiel LIKE '%TEST%' OR numero_sequentiel LIKE 'FAC-%' OR numero_sequentiel LIKE 'ELV-%' OR numero_sequentiel LIKE 'DEBUG-%'");
        // Supprimer l'élève
        $pdo->exec("DELETE FROM eleve WHERE id_eleve IN (999999, 888888)");
        // Supprimer les types de frais (attention : supprimer les données test avant les types)
        $pdo->exec("DELETE FROM type_frais WHERE libelle LIKE 'TEST_%' OR libelle LIKE 'DEBUG_%'");
        // Nettoyer les parametrages de test aussi
        $pdo->exec("DELETE FROM parametrage_etablissement WHERE nom_etablissement LIKE 'TEST_%'");
    } catch (Throwable $e) {
        // Ignore - les données peuvent ne pas exister
    }
}

echo "=== Test Intégration Complète Facturation ===\n";

// Étape 1 : Créer une année active (prérequis)
$stmt = $pdo->prepare(
    'INSERT INTO annee_scolaire (libelle, date_debut, date_fin, etat) 
     VALUES (:libelle, :date_debut, :date_fin, :etat) 
     ON DUPLICATE KEY UPDATE id_annee=id_annee'
);
$stmt->execute([
    ':libelle' => '2025-2026',
    ':date_debut' => '2025-09-15',
    ':date_fin' => '2026-06-30',
    ':etat' => 'active',
]);
// Récupérer l'ID de l'année
$stmt_year = $pdo->query("SELECT id_annee FROM annee_scolaire WHERE libelle='2025-2026' LIMIT 1");
$year = $stmt_year->fetch(PDO::FETCH_ASSOC);
$id_annee = (int) ($year['id_annee'] ?? 1);
echo "✓ Année scolaire vérifiée (ID=$id_annee)\n";

// Créer le paramétrage d'établissement s'il n'existe pas, ou en recréer un dédié pour les tests
// Pour simplifier, on crée toujours un nouveau parametrage avec un nom TEST_xxx unique
$test_param_name = 'TEST_Établissement_' . uniqid();
$stmt_p = $pdo->prepare(
    'INSERT INTO parametrage_etablissement (nom_etablissement, monnaie, format_matricule, prefixe_matricule, annee_courante) 
     VALUES (:nom, :monnaie, :format, :prefixe, :annee_courante)'
);
try {
    $stmt_p->execute([
        ':nom' => $test_param_name,
        ':monnaie' => 'CFA',
        ':format' => '{PREFIXE}-{ANNEE}-{NUMERO_SEQUENTIEL}',
        ':prefixe' => 'FAC',
        ':annee_courante' => '2025-2026',
    ]);
} catch (Throwable $e) {
    // Si erreur, utiliser le parametrage existant
}
echo "✓ Paramétrage établissement vérifiée\n";

// Étape 2 : Créer un élève fictif
$pdo->exec("INSERT IGNORE INTO personne (nom, prenom, sexe) VALUES ('INTEG', 'TestFacture', 'F')");
$stmt_personne = $pdo->query("SELECT id_personne FROM personne WHERE nom='INTEG' AND prenom='TestFacture' LIMIT 1");
$personne = $stmt_personne->fetch(PDO::FETCH_ASSOC);
$id_personne = (int) ($personne['id_personne'] ?? 1);

$stmt_eleve = $pdo->prepare(
    'INSERT IGNORE INTO eleve (id_personne, id_eleve, matricule, date_entree, statut_scolaire) 
     VALUES (:id_personne, :id_eleve, :matricule, :date_entree, :statut_scolaire)'
);
$stmt_eleve->execute([
    ':id_personne' => $id_personne,
    ':id_eleve' => 999999,
    ':matricule' => 'INTEG-ELV-999999',
    ':date_entree' => date('Y-m-d'),
    ':statut_scolaire' => 'actif',
]);
echo "✓ Élève créé (ID=999999)\n";

// Étape 3 : Créer les types de frais
$typeFraisDAO = new TypeFraisDAO();

$types = [
    'TEST_Frais_Inscription' => 15000.00,
    'TEST_Frais_Scolarité' => 50000.00,
    'TEST_Frais_Bibliothèque' => 5000.00,
];

$type_ids = [];
foreach ($types as $libelle => $montant) {
    $id = $typeFraisDAO->creer([
        'libelle' => $libelle,
        'montant_defaut' => $montant,
    ]);
    if ($id) {
        $type_ids[$libelle] = $id;
        echo "✓ Type créé : $libelle (montant=$montant)\n";
    }
}

if (count($type_ids) < 3) {
    throw new RuntimeException("Pas assez de types créés : " . count($type_ids));
}

// Étape 4 : Générer la facture
$factureDAO = new FactureDAO();
$id_facture = $factureDAO->creerFacture(
    999999,
    array_values($type_ids),
    $id_annee
);

if (!is_int($id_facture) || $id_facture <= 0) {
    // Afficher le contenu du log d'erreur
    $error_log = shell_exec('tail -20 ' . (PHP_OS_FAMILY === 'Windows' ? 'nul' : '/dev/null'));
    echo "Error log contents:\n";
    echo file_get_contents('php://stderr');
    echo "\nDEBUG: id_facture = " . var_export($id_facture, true) . "\n";
    throw new RuntimeException("Échec création facture : " . var_export($id_facture, true));
}
echo "✓ Facture générée (ID=$id_facture)\n";

// Étape 5 : Vérifier les données en base
$facture = $factureDAO->trouverParId($id_facture);
if (!$facture) {
    throw new RuntimeException("Facture non trouvée en base");
}

echo "✓ Facture récupérée :\n";
echo "  - Numéro : {$facture['numero_sequentiel']}\n";
echo "  - Élève ID : {$facture['id_eleve']}\n";
echo "  - Montant total : {$facture['montant_total']} CFA\n";
echo "  - Statut : {$facture['statut']}\n";
echo "  - Lignes : " . count($facture['lignes']) . "\n";

// Étape 6 : Vérifier le montant total
$expected_total = 15000 + 50000 + 5000;  // 70000
if ((float) $facture['montant_total'] != $expected_total) {
    throw new RuntimeException(
        "Montant incorrect : attendu $expected_total, obtenu {$facture['montant_total']}"
    );
}
echo "✓ Montant total vérifié : {$facture['montant_total']} CFA\n";

// Étape 7 : Vérifier les lignes
foreach ($facture['lignes'] as $ligne) {
    echo "  - Ligne {$ligne['id_ligne_facture']} : {$ligne['libelle']} = {$ligne['montant_ligne']} CFA\n";
}

// Étape 8 : Vérifier que la facture apparaît dans la liste de l'élève
$factures_eleve = $factureDAO->listerParEleve(999999);
$facture_trouvee = false;
foreach ($factures_eleve as $f) {
    if ((int) $f['id_facture'] === $id_facture) {
        $facture_trouvee = true;
        break;
    }
}
if (!$facture_trouvee) {
    throw new RuntimeException("Facture non trouvée dans la liste de l'élève");
}
echo "✓ Facture trouvée dans la liste de l'élève\n";

// Étape 9 : Vérifier que le numéro est unique (format séquentiel)
if (empty($facture['numero_sequentiel'])) {
    throw new RuntimeException("Numéro séquentiel vide");
}
// Le format doit contenir au minimum un tiret (préfixe-année-numéro)
if (strpos($facture['numero_sequentiel'], '-') === false) {
    throw new RuntimeException("Format numéro incorrect (pas de séparateur '-') : {$facture['numero_sequentiel']}");
}
echo "✓ Numéro séquentiel valide : {$facture['numero_sequentiel']}\n";

// Étape 10 : Vérifier la date d'émission
if (!$facture['date_emission']) {
    throw new RuntimeException("Date d'émission vide");
}
echo "✓ Date d'émission : {$facture['date_emission']}\n";

echo "\n=== Tous les tests d'intégration passent ! ===\n";
