<?php
/**
 * Rapports Académiques - Moyennes, taux de réussite, performances
 */
?>

<div class="rapports-container">
    <h1>📊 Rapports Académiques</h1>
    <p>Moyennes, taux de réussite et performances par classe — Période: <?= htmlspecialchars($data['periode']); ?></p>

    <!-- Indicateurs généraux -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Classes</div>
            <div class="stat-value"><?= $data['total_classes']; ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Moyenne Établissement</div>
            <div class="stat-value"><?= number_format($data['moyenne_etablissement'], 2); ?>/20</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Taux Réussite</div>
            <div class="stat-value"><?= number_format($data['taux_reussite_etablissement'], 1); ?>%</div>
        </div>
    </div>

    <!-- Tableau des rapports par classe -->
    <section class="rapports-section">
        <h2>Résultats par Classe</h2>
        <table class="rapports-table">
            <thead>
                <tr>
                    <th>Classe</th>
                    <th>Total Élèves</th>
                    <th>Moyenne</th>
                    <th>Taux Réussite</th>
                    <th>Meilleure</th>
                    <th>Plus faible</th>
                    <th>En difficulté</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['rapports'] as $rapport): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($rapport['classe']); ?></strong></td>
                        <td><?= $rapport['total_eleves']; ?></td>
                        <td><?= number_format($rapport['moyenne_generale'], 2); ?>/20</td>
                        <td>
                            <span class="taux-reussite" style="color: <?= $rapport['taux_reussite'] >= 90 ? '#28a745' : ($rapport['taux_reussite'] >= 80 ? '#ffc107' : '#dc3545'); ?>">
                                <?= number_format($rapport['taux_reussite'], 1); ?>%
                            </span>
                        </td>
                        <td><?= number_format($rapport['meilleure_moyenne'], 2); ?></td>
                        <td><?= number_format($rapport['pire_moyenne'], 2); ?></td>
                        <td><?= $rapport['eleves_en_difficulte']; ?></td>
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

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin: 20px 0;
}

.stat-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
}

.stat-label {
    font-size: 0.9em;
    opacity: 0.9;
}

.stat-value {
    font-size: 2em;
    font-weight: bold;
    margin-top: 10px;
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

.taux-reussite {
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
