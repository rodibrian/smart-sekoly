<?php
/**
 * Vue dossier enseignant consolidé.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Dossier enseignant</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .conteneur { max-width: 840px; margin: 40px auto; padding: 28px; background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        .ligne { display: flex; justify-content: space-between; gap: 24px; margin-top: 16px; }
        .bloc { flex: 1; background: #f8fafc; padding: 16px; border-radius: 10px; }
        h1, h2 { margin-top: 0; }
        ul { margin: 0; padding-left: 20px; }
    </style>
</head>
<body>
    <div class="conteneur">
        <h1>Dossier enseignant</h1>
        <h2>Identité</h2>
        <div class="ligne">
            <div class="bloc"><strong>Nom complet :</strong><br><?= e($donnees['enseignant']['nom_complet']) ?></div>
            <div class="bloc"><strong>Matricule :</strong><br><?= e($donnees['enseignant']['matricule']) ?></div>
        </div>

        <h2>Coordonnées</h2>
        <div class="ligne">
            <div class="bloc"><strong>Email :</strong><br><?= e($donnees['enseignant']['email']) ?></div>
            <div class="bloc"><strong>Téléphone :</strong><br><?= e($donnees['enseignant']['telephone'] ?: '—') ?></div>
        </div>

        <h2>Informations RH</h2>
        <div class="ligne">
            <div class="bloc"><strong>Fonction :</strong><br><?= e($donnees['enseignant']['fonction'] ?: 'Enseignant') ?></div>
            <div class="bloc"><strong>Date d'embauche :</strong><br><?= e($donnees['enseignant']['date_embauche']) ?></div>
        </div>

        <h2>Historique</h2>
        <div class="bloc">
            <strong>Contrats actifs :</strong>
            <ul>
                <?php foreach ($donnees['contrats'] as $contrat): ?>
                    <li><?= e($contrat['periode']) ?> — <?= e($contrat['type_contrat']) ?> — <?= e($contrat['statut']) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</body>
</html>
