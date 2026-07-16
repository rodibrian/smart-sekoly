<?php
/**
 * Rapports Financiers - Revenus, dépenses, paiements
 */
?>

<div class="rapports-container">
    <h1>💰 Rapports Financiers</h1>
    <p>Suivi des revenus, dépenses et équilibre financier.</p>

    <!-- Indicateurs financiers clés -->
    <div class="finance-grid">
        <div class="finance-card">
            <div class="finance-label">Total Factures</div>
            <div class="finance-value"><?= number_format($data['total_factures'], 0, '', ' '); ?> FCFA</div>
            <div class="finance-meta"><?= $data['nombre_factures']; ?> factures émises</div>
        </div>
        <div class="finance-card">
            <div class="finance-label">Total Paiements</div>
            <div class="finance-value" style="color: #28a745;"><?= number_format($data['total_paiements'], 0, '', ' '); ?> FCFA</div>
            <div class="finance-meta"><?= $data['nombre_paiements']; ?> paiements reçus</div>
        </div>
        <div class="finance-card">
            <div class="finance-label">Montant Impayé</div>
            <div class="finance-value" style="color: <?= $data['montant_impaye'] > 0 ? '#dc3545' : '#28a745'; ?>">
                <?= number_format($data['montant_impaye'], 0, '', ' '); ?> FCFA
            </div>
            <div class="finance-meta"><?= $data['nombre_impaye']; ?> factures impayées</div>
        </div>
        <div class="finance-card">
            <div class="finance-label">Taux Recouvrement</div>
            <div class="finance-value"><?= number_format($data['taux_recouvrement'], 1); ?>%</div>
            <div class="finance-meta">Performance de collecte</div>
        </div>
    </div>

    <!-- Rapport mensuel -->
    <section class="rapports-section">
        <h2>Résultats Mensuels</h2>
        <table class="rapports-table">
            <thead>
                <tr>
                    <th>Mois</th>
                    <th>Factures</th>
                    <th>Montant Factures</th>
                    <th>Paiements</th>
                    <th>Montant Reçu</th>
                    <th>Taux Recouvrement</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['rapports_mensuels'] as $rapport): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($rapport['mois']); ?></strong></td>
                        <td><?= $rapport['factures_emises']; ?></td>
                        <td><?= number_format($rapport['montant_factures'], 0, '', ' '); ?> FCFA</td>
                        <td><?= $rapport['paiements_recus']; ?></td>
                        <td><?= number_format($rapport['montant_paiements'], 0, '', ' '); ?> FCFA</td>
                        <td>
                            <span class="taux-recouvrement" style="color: <?= $rapport['taux_recouvrement'] >= 90 ? '#28a745' : '#ffc107'; ?>">
                                <?= $rapport['taux_recouvrement']; ?>%
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <!-- Actions d'export -->
    <div class="export-actions">
        <a href="#" class="btn-export">📥 Exporter PDF</a>
        <a href="#" class="btn-export">📋 Exporter Excel</a>
        <a href="#" class="btn-export">🖨️ Imprimer</a>
    </div>
</div>

<style>
.rapports-container {
    padding: 20px;
}

.finance-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
    margin: 20px 0;
}

.finance-card {
    background: white;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.finance-label {
    font-size: 0.9em;
    color: #666;
    margin-bottom: 10px;
}

.finance-value {
    font-size: 1.5em;
    font-weight: bold;
    color: #333;
}

.finance-meta {
    font-size: 0.8em;
    color: #999;
    margin-top: 8px;
}

.rapports-section {
    margin: 40px 0;
}

.rapports-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.rapports-table thead {
    background: #f8f9fa;
}

.rapports-table th {
    padding: 15px;
    text-align: left;
    font-weight: 600;
    color: #333;
    border-bottom: 2px solid #dee2e6;
}

.rapports-table td {
    padding: 12px 15px;
    border-bottom: 1px solid #dee2e6;
}

.rapports-table tbody tr:hover {
    background: #f9f9f9;
}

.taux-recouvrement {
    font-weight: 600;
}

.export-actions {
    display: flex;
    gap: 10px;
    margin-top: 30px;
    flex-wrap: wrap;
}

.btn-export {
    padding: 10px 20px;
    background: #28a745;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    transition: background 0.3s;
}

.btn-export:hover {
    background: #218838;
}
</style>
