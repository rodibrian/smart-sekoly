<?php
/**
 * Rapports Officiels - Rapports formatés pour le Ministère
 */
?>

<div class="rapports-container">
    <h1>📋 Rapports Officiels</h1>
    <p>Rapports formatés selon les standards du Ministère de l'Éducation.</p>

    <!-- Informations générales -->
    <section class="info-etablissement">
        <div class="info-box">
            <h3>📍 Établissement</h3>
            <p><strong><?= htmlspecialchars($data['etablissement']); ?></strong></p>
        </div>
        <div class="info-box">
            <h3>📅 Année Scolaire</h3>
            <p><?= htmlspecialchars($data['annee_scolaire']); ?></p>
        </div>
        <div class="info-box">
            <h3>🕐 Dernière Génération</h3>
            <p><?= htmlspecialchars($data['derniere_generation']); ?></p>
        </div>
    </section>

    <!-- Liste des rapports officiels -->
    <section class="rapports-officiels">
        <h2>Rapports Disponibles</h2>
        <div class="rapports-list">
            <?php foreach ($data['rapports'] as $rapport): ?>
                <div class="rapport-item">
                    <div class="rapport-header">
                        <h3><?= htmlspecialchars($rapport['type']); ?></h3>
                        <span class="format-badge"><?= htmlspecialchars($rapport['format']); ?></span>
                    </div>
                    
                    <p class="rapport-description"><?= htmlspecialchars($rapport['description']); ?></p>
                    
                    <div class="rapport-details">
                        <?php 
                        // Afficher les détails spécifiques selon le type
                        foreach ($rapport as $key => $value) {
                            if (!in_array($key, ['type', 'description', 'format', 'date_derniere_generation'])) {
                                echo "<div class='detail-item'>";
                                echo "<strong>" . ucfirst(str_replace('_', ' ', $key)) . ":</strong> ";
                                echo htmlspecialchars($value);
                                echo "</div>";
                            }
                        }
                        ?>
                    </div>
                    
                    <div class="rapport-actions">
                        <a href="#" class="btn-rapport">📥 Télécharger PDF</a>
                        <a href="#" class="btn-rapport">📊 Télécharger Excel</a>
                        <a href="#" class="btn-rapport">👁️ Aperçu</a>
                    </div>
                    
                    <div class="rapport-meta">
                        Généré le: <?= htmlspecialchars($rapport['date_derniere_generation']); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Notes importantes -->
    <section class="notes-importantes">
        <h3>📌 Notes Importantes</h3>
        <ul>
            <li>Les rapports sont conformes aux normes du Ministère de l'Éducation</li>
            <li>Tous les champs obligatoires doivent être complétés avant génération</li>
            <li>Les données sont mises à jour automatiquement chaque nuit</li>
            <li>Un archivage des rapports est effectué chaque trimestre</li>
            <li>Pour support: contactez l'administrateur système</li>
        </ul>
    </section>
</div>

<style>
.rapports-container {
    padding: 20px;
}

.info-etablissement {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin: 20px 0;
}

.info-box {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    border-radius: 8px;
}

.info-box h3 {
    margin: 0 0 10px 0;
    font-size: 0.9em;
    opacity: 0.9;
}

.info-box p {
    margin: 0;
    font-size: 1.2em;
    font-weight: 600;
}

.rapports-officiels {
    margin: 40px 0;
}

.rapports-list {
    display: grid;
    gap: 20px;
}

.rapport-item {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    background: white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: box-shadow 0.3s;
}

.rapport-item:hover {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.rapport-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
    padding-bottom: 15px;
}

.rapport-header h3 {
    margin: 0;
    color: #333;
}

.format-badge {
    display: inline-block;
    padding: 5px 15px;
    background: #f0f0f0;
    color: #333;
    border-radius: 20px;
    font-size: 0.85em;
    font-weight: 600;
}

.rapport-description {
    color: #666;
    margin: 10px 0;
    font-size: 0.95em;
}

.rapport-details {
    background: #f9f9f9;
    padding: 15px;
    border-radius: 5px;
    margin: 15px 0;
}

.detail-item {
    padding: 5px 0;
    font-size: 0.9em;
}

.rapport-actions {
    display: flex;
    gap: 10px;
    margin: 15px 0;
    flex-wrap: wrap;
}

.btn-rapport {
    padding: 8px 16px;
    background: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-size: 0.9em;
    transition: background 0.3s;
}

.btn-rapport:hover {
    background: #0056b3;
}

.rapport-meta {
    font-size: 0.85em;
    color: #999;
    margin-top: 10px;
}

.notes-importantes {
    background: #fff3cd;
    border: 1px solid #ffc107;
    padding: 20px;
    border-radius: 8px;
    margin-top: 40px;
}

.notes-importantes h3 {
    margin-top: 0;
    color: #856404;
}

.notes-importantes ul {
    margin: 15px 0;
    padding-left: 20px;
}

.notes-importantes li {
    margin: 8px 0;
    color: #856404;
}
</style>
