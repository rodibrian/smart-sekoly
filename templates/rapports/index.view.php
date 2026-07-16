<?php
/**
 * Rapports et Statistiques - Tableau de bord
 */
?>

<div class="rapports-container">
    <h1>📊 Rapports et Statistiques</h1>
    <p>Accédez aux rapports académiques, financiers et officiels.</p>

    <!-- Grille des rapports disponibles -->
    <div class="rapports-grid">
        <?php foreach ($data['rapports_disponibles'] as $rapport): ?>
            <div class="rapport-card">
                <div class="rapport-icon"><?= $rapport['icone']; ?></div>
                <h3><?= htmlspecialchars($rapport['titre']); ?></h3>
                <p><?= htmlspecialchars($rapport['description']); ?></p>
                <a href="?module=rapports&action=<?= $rapport['action']; ?>" class="btn-rapport">
                    Accéder →
                </a>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Formats d'export disponibles -->
    <section class="export-formats">
        <h3>📥 Formats d'export disponibles</h3>
        <div class="format-list">
            <?php foreach ($data['export_formats'] as $format): ?>
                <span class="format-badge"><?= $format; ?></span>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<style>
.rapports-container {
    padding: 20px;
}

.rapports-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin: 30px 0;
}

.rapport-card {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    transition: transform 0.3s, box-shadow 0.3s;
    background: #f9f9f9;
}

.rapport-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.rapport-icon {
    font-size: 2.5em;
    margin-bottom: 10px;
}

.rapport-card h3 {
    color: #333;
    margin: 10px 0;
}

.rapport-card p {
    color: #666;
    font-size: 0.9em;
}

.btn-rapport {
    display: inline-block;
    margin-top: 15px;
    padding: 10px 20px;
    background: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    transition: background 0.3s;
}

.btn-rapport:hover {
    background: #0056b3;
}

.export-formats {
    margin-top: 40px;
    padding: 20px;
    background: #f0f0f0;
    border-radius: 8px;
}

.export-formats h3 {
    margin-top: 0;
}

.format-list {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.format-badge {
    display: inline-block;
    padding: 8px 16px;
    background: white;
    border: 1px solid #ddd;
    border-radius: 20px;
    color: #333;
    font-size: 0.9em;
}
</style>
