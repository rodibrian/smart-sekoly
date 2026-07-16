<?php
/**
 * Vue de fiche de salaire enseignant.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Salaire enseignant</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .conteneur { max-width: 720px; margin: 40px auto; padding: 28px; background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        .bloc { margin-top: 12px; padding: 12px; background: #f8fafc; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="conteneur">
        <h1>Fiche salaire</h1>
        <div class="bloc"><strong>Période :</strong> <?= e($donnees['salaire']['periode']) ?></div>
        <div class="bloc"><strong>Montant brut :</strong> <?= e($donnees['salaire']['montant_brut']) ?> MGA</div>
        <div class="bloc"><strong>Retenues :</strong> <?= e($donnees['salaire']['retenues']) ?> MGA</div>
        <div class="bloc"><strong>Montant net :</strong> <?= e($donnees['salaire']['montant_net']) ?> MGA</div>
        <div class="bloc"><strong>Statut :</strong> <?= e($donnees['salaire']['statut']) ?></div>
        <div class="bloc"><strong>Date paiement :</strong> <?= e($donnees['salaire']['date_paiement'] ?? '—') ?></div>
    </div>
</body>
</html>
