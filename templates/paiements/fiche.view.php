<?php
/**
 * Fiche paiement.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Paiement</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .conteneur { max-width: 760px; margin: 40px auto; padding: 28px; background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        .bloc { margin-bottom: 18px; }
        .bloc strong { display: block; margin-bottom: 6px; color: #0f172a; }
    </style>
</head>
<body>
    <div class="conteneur">
        <h1>Paiement <?= e($donnees['paiement']['recu']) ?></h1>
        <div class="bloc"><strong>Date de paiement :</strong> <?= e($donnees['paiement']['date']) ?></div>
        <div class="bloc"><strong>Montant :</strong> <?= e($donnees['paiement']['montant']) ?> MGA</div>
        <div class="bloc"><strong>Mode :</strong> <?= e(ucfirst($donnees['paiement']['mode'])) ?></div>
        <div class="bloc"><strong>Statut :</strong> <?= e(ucfirst($donnees['paiement']['statut'])) ?></div>
        <p><a href="<?= e(BASE_URL . '/paiements') ?>">Retour à la liste des paiements</a></p>
    </div>
</body>
</html>
