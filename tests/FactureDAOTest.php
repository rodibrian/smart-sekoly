<?php
session_start();
require_once __DIR__ . '/../includes/fonctions.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/ParametrageEtablissement.class.php';
require_once __DIR__ . '/../classes/TypeFraisDAO.class.php';
require_once __DIR__ . '/../classes/SequenceNumerotation.class.php';
require_once __DIR__ . '/../classes/FactureDAO.class.php';

// Test FactureDAO — Persistance réelle des factures avec lignes

$pdo = get_connexion_base_donnees();
if ($pdo instanceof PDO) {
    try {
        $pdo->exec("DELETE FROM facture WHERE numero_sequentiel LIKE 'TEST-%'");
        $pdo->exec("DELETE FROM eleve WHERE id_eleve = 999999");
        $pdo->exec("DELETE FROM type_frais WHERE libelle LIKE 'TEST_%'");
    } catch (Throwable $e) {
        // Ignore
    }
}

echo "=== Test FactureDAO ===\n";

// Étape 0 : Créer un élève de test
$pdo->exec("INSERT IGNORE INTO personne (nom, prenom, sexe) VALUES ('DAO', 'TestFacture', 'M')");
$stmt_p = $pdo->query("SELECT id_personne FROM personne WHERE nom='DAO' AND prenom='TestFacture' LIMIT 1");
$p = $stmt_p->fetch(PDO::FETCH_ASSOC);
$id_personne = (int) ($p['id_personne'] ?? 1);

$stmt = $pdo->prepare(
    'INSERT IGNORE INTO eleve (id_personne, id_eleve, matricule, date_entree, statut_scolaire) 
     VALUES (:id_personne, :id_eleve, :matricule, :date_entree, :statut_scolaire)'
);
$stmt->execute([
    ':id_personne' => $id_personne,
    ':id_eleve' => 999999,
    ':matricule' => 'TEST-ELV-999999',
    ':date_entree' => date('Y-m-d'),
    ':statut_scolaire' => 'actif',
]);
echo "✓ Élève de test créé (ID=999999)\n";

// Étape 1 : Créer les types de frais
$typeFraisDAO = new TypeFraisDAO();

$id_type_scolarite = $typeFraisDAO->creer([
    'libelle' => 'TEST_Scolarité',
    'montant_defaut' => 50000.00,
]);
$id_type_cantine = $typeFraisDAO->creer([
    'libelle' => 'TEST_Cantine',
    'montant_defaut' => 10000.00,
]);
$id_type_transport = $typeFraisDAO->creer([
    'libelle' => 'TEST_Transport',
    'montant_defaut' => 5000.00,
]);

if (!$id_type_scolarite || !$id_type_cantine || !$id_type_transport) {
    throw new RuntimeException('Échec création types de frais');
}
echo "✓ Types de frais créés\n";

// Étape 2 : Créer une facture avec 3 lignes
$factureDAO = new FactureDAO();
$id_eleve_test = 999999;  // Élève fictif (test)
$id_annee = 1;  // Année active

$id_facture = $factureDAO->creerFacture(
    $id_eleve_test,
    [$id_type_scolarite, $id_type_cantine, $id_type_transport],
    $id_annee,
    'TEST-2026-00001'  // Numéro override pour test
);

if (!is_int($id_facture) || $id_facture <= 0) {
    throw new RuntimeException('Échec création facture');
}
echo "✓ Facture créée : ID=$id_facture\n";

// Étape 3 : Récupérer la facture avec ses lignes
$facture = $factureDAO->trouverParId($id_facture);
if (!$facture) {
    throw new RuntimeException('Facture non trouvée');
}
if ($facture['numero_sequentiel'] !== 'TEST-2026-00001') {
    throw new RuntimeException('Numéro séquentiel incorrect');
}
if ((float) $facture['montant_total'] !== 65000.0) {
    throw new RuntimeException('Montant total incorrect : ' . $facture['montant_total']);
}
if (count($facture['lignes']) !== 3) {
    throw new RuntimeException('Nombre de lignes incorrect : ' . count($facture['lignes']));
}
echo "✓ Récupération facture OK\n";

// Étape 4 : Vérifier les lignes
$total_lignes = 0;
foreach ($facture['lignes'] as $ligne) {
    $total_lignes += (float) $ligne['montant_ligne'];
}
if ($total_lignes !== 65000.0) {
    throw new RuntimeException("Total des lignes incorrect : $total_lignes");
}
echo "✓ Lignes vérifiées : total=$total_lignes CFA\n";

// Étape 5 : Calcul du total indépendant
$total_calc = $factureDAO->calculerTotal($id_facture);
if ($total_calc !== 65000.0) {
    throw new RuntimeException("Calcul du total incorrect : $total_calc");
}
echo "✓ Calcul du total OK\n";

// Étape 6 : Lister les factures de l'élève
$factures = $factureDAO->listerParEleve($id_eleve_test);
if (count($factures) < 1) {
    throw new RuntimeException('Facture non trouvée dans liste élève');
}
echo "✓ Listage par élève OK\n";

// Étape 7 : Annuler la facture
$success = $factureDAO->annuler($id_facture, 1);  // id_utilisateur=1
if (!$success) {
    throw new RuntimeException('Annulation échouée');
}

$facture_annulee = $factureDAO->trouverParId($id_facture);
if ($facture_annulee['statut'] !== 'annulee') {
    throw new RuntimeException('Statut non changé');
}
echo "✓ Annulation facture OK\n";

echo "\n=== Tous les tests FactureDAO passent ! ===\n";
