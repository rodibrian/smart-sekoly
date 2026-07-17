<?php
require_once __DIR__ . '/../config/database.php';

// Connect to DB
$pdo = get_connexion_base_donnees();
if (!$pdo) {
    die("⚠ Connexion BD échouée\n");
}

// Create test user
$stmt_user = $pdo->prepare(
    'INSERT IGNORE INTO utilisateur (id_utilisateur, identifiant, mot_de_passe_hash, statut_compte, id_role) VALUES (:id_user, :identifiant, :mdp, :statut, :role)'
);
$id_user = 999999;
$stmt_user->execute([
    ':id_user' => $id_user,
    ':identifiant' => 'test_echo_debug',
    ':mdp' => password_hash('test', PASSWORD_DEFAULT),
    ':statut' => 'actif',
    ':role' => 1,
]);

// Create student
$stmt_eleve = $pdo->prepare(
    'INSERT IGNORE INTO eleve (id_personne, id_eleve, matricule, date_entree, statut_scolaire) VALUES (:id_personne, :id_eleve, :matricule, :date_entree, :statut_scolaire)'
);
$id_personne_eleve = 888888;
$stmt_eleve->execute([
    ':id_personne' => $id_personne_eleve,
    ':id_eleve' => 888888,
    ':matricule' => 'ECH-888888',
    ':date_entree' => date('Y-m-d'),
    ':statut_scolaire' => 'actif',
]);

// Get current academic year
$id_annee = 1;

// Create fee type
require_once __DIR__ . '/../classes/TypeFraisDAO.class.php';
$typeFraisDAO = new TypeFraisDAO();
$id_type = $typeFraisDAO->creer(['libelle' => 'DEBUG_Frais', 'montant_defaut' => 200000.00]);

// Create invoice
require_once __DIR__ . '/../classes/FactureDAO.class.php';
$factureDAO = new FactureDAO();
$id_facture = $factureDAO->creerFacture($id_personne_eleve, [$id_type], $id_annee, 'DEBUG_001');
echo "✓ Facture créée (ID=$id_facture)\n";

// Create 4 echances (4 × 50k)
require_once __DIR__ . '/../classes/EcheancierDAO.class.php';
$echeancierDAO = new EcheancierDAO();
$echances = [
    ['date' => date('Y-m-d', strtotime('+30 days')), 'montant' => 50000],
    ['date' => date('Y-m-d', strtotime('+60 days')), 'montant' => 50000],
    ['date' => date('Y-m-d', strtotime('+90 days')), 'montant' => 50000],
    ['date' => date('Y-m-d', strtotime('+120 days')), 'montant' => 50000],
];
$echeancierDAO->creer($id_facture, $echances, $id_user);
echo "✓ Échéancier créé (4 × 50k)\n";

// Paiement 1 : 50k
echo "\n--- Paiement 1 : 50k ---\n";
$echeancierDAO->impurerPaiement($id_facture, 50000);
$list = $echeancierDAO->listerParFacture($id_facture);
foreach ($list as $i => $ech) {
    echo "Ech " . ($i+1) . ": montant_prevu=". $ech['montant_prevu'] .", montant_paye=" . $ech['montant_paye'] . ", statut=" . $ech['statut'] . "\n";
}
$etat = $echeancierDAO->calculerEtatGlobal($id_facture);
echo "État: a_venir=" . $etat['a_venir'] . ", payees=" . $etat['payees'] . ", partielles=" . $etat['partielles'] . ", en_retard=" . $etat['en_retard'] . "\n";

// Paiement 2 : 100k
echo "\n--- Paiement 2 : 100k ---\n";
$echeancierDAO->impurerPaiement($id_facture, 100000);
$list = $echeancierDAO->listerParFacture($id_facture);
foreach ($list as $i => $ech) {
    echo "Ech " . ($i+1) . ": montant_prevu=". $ech['montant_prevu'] .", montant_paye=" . $ech['montant_paye'] . ", statut=" . $ech['statut'] . "\n";
}
$etat = $echeancierDAO->calculerEtatGlobal($id_facture);
echo "État: a_venir=" . $etat['a_venir'] . ", payees=" . $etat['payees'] . ", partielles=" . $etat['partielles'] . ", en_retard=" . $etat['en_retard'] . "\n";
echo "Montant payé total: " . $etat['montant_total_paye'] . "\n";
