<?php
/**
 * Rapports Personnalisés - Créer un rapport avec sélection personnalisée
 */
?>

<div class="rapports-container">
    <h1>⚙️ Rapports Personnalisés</h1>
    <p>Créez des rapports selon vos besoins spécifiques.</p>

    <!-- Formulaire de création de rapport -->
    <section class="formulaire-rapport">
        <h2>Générer un Rapport</h2>
        <form method="POST" class="rapport-form">
            <div class="form-group">
                <label>Type de Rapport *</label>
                <select name="type_rapport" required>
                    <option value="">-- Sélectionner un type --</option>
                    <?php foreach ($data['types_rapports'] as $key => $label): ?>
                        <option value="<?= htmlspecialchars($key); ?>"><?= htmlspecialchars($label); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Période *</label>
                <select name="periode" required>
                    <option value="">-- Sélectionner une période --</option>
                    <?php foreach ($data['periodes'] as $key => $label): ?>
                        <option value="<?= htmlspecialchars($key); ?>"><?= htmlspecialchars($label); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Format d'Export *</label>
                <select name="format_export" required>
                    <option value="">-- Sélectionner un format --</option>
                    <?php foreach ($data['formats_export'] as $key => $label): ?>
                        <option value="<?= htmlspecialchars($key); ?>"><?= htmlspecialchars($label); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn-submit">✓ Générer le Rapport</button>
        </form>
    </section>

    <!-- Liste des rapports générés -->
    <?php if ($data['total_rapports_generes'] > 0): ?>
        <section class="rapports-generes">
            <h2>Rapports Générés (<?= $data['total_rapports_generes']; ?>)</h2>
            <table class="rapports-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Type</th>
                        <th>Période</th>
                        <th>Format</th>
                        <th>Date</th>
                        <th>Taille</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['rapports_generes'] as $rapport): ?>
                        <tr>
                            <td><code><?= htmlspecialchars($rapport['id']); ?></code></td>
                            <td><?= htmlspecialchars($rapport['type']); ?></td>
                            <td><?= htmlspecialchars($rapport['periode']); ?></td>
                            <td><strong><?= strtoupper(htmlspecialchars($rapport['format'])); ?></strong></td>
                            <td><?= htmlspecialchars($rapport['date_creation']); ?></td>
                            <td><?= htmlspecialchars($rapport['taille']); ?></td>
                            <td><span class="badge-success"><?= htmlspecialchars($rapport['statut']); ?></span></td>
                            <td>
                                <a href="#" class="btn-action">📥</a>
                                <a href="#" class="btn-action">👁️</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    <?php else: ?>
        <section class="rapports-generes">
            <p style="color: #999;">Aucun rapport généré encore. Créez votre premier rapport en remplissant le formulaire ci-dessus.</p>
        </section>
    <?php endif; ?>
</div>

<style>
.rapports-container {
    padding: 20px;
}

.formulaire-rapport {
    background: #f9f9f9;
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
}

.rapport-form {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    font-weight: 600;
    margin-bottom: 8px;
    color: #333;
}

.form-group select {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1em;
}

.form-group select:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
}

.btn-submit {
    grid-column: 1 / -1;
    padding: 12px 20px;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 600;
    transition: background 0.3s;
}

.btn-submit:hover {
    background: #0056b3;
}

.rapports-generes {
    margin-top: 40px;
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

.badge-success {
    display: inline-block;
    padding: 4px 12px;
    background: #d4edda;
    color: #155724;
    border-radius: 12px;
    font-size: 0.85em;
}

.btn-action {
    display: inline-block;
    padding: 5px 10px;
    background: #e9ecef;
    text-decoration: none;
    border-radius: 4px;
    margin: 0 2px;
    transition: background 0.3s;
}

.btn-action:hover {
    background: #dee2e6;
}
</style>
