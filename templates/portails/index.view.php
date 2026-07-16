<?php
/**
 * Vue principale du module Portails Élève / Parent.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Portails Élève / Parent</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: #f3f4f6; color: #111827; }
        .page { max-width: 1100px; margin: 32px auto; padding: 24px; }
        .actions { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-top: 24px; }
        .carte { background: #fff; border-radius: 14px; box-shadow: 0 12px 24px rgba(15,23,42,.08); padding: 22px; }
        .carte h2 { margin: 0 0 10px; font-size: 20px; }
        .carte p { margin: 0 0 14px; color: #475569; }
        .btn { display: inline-block; padding: 10px 16px; background: #2563eb; color: #fff; text-decoration: none; border-radius: 8px; }
        .btn:hover { background: #1d4ed8; }
        .stats { display: flex; gap: 16px; flex-wrap: wrap; margin-top: 16px; }
        .stat { flex: 1; min-width: 180px; background: #eff6ff; padding: 16px; border-radius: 12px; text-align: center; }
        .stat strong { display: block; font-size: 28px; margin-bottom: 8px; }
    </style>
</head>
<body>
    <div class="page">
        <h1>Portails Élève / Parent</h1>
        <p>Accédez aux portails de consultation, paiements, emploi du temps et réservation de repas.</p>

        <div class="stats">
            <div class="stat">
                <strong><?= e($data['statut']['codes_actifs']) ?></strong>
                Codes d'accès actifs
            </div>
            <div class="stat">
                <strong><?= e($data['statut']['eleves_actifs']) ?></strong>
                Élèves enregistrés
            </div>
            <div class="stat">
                <strong><?= e($data['statut']['parents_actifs']) ?></strong>
                Parents référencés
            </div>
        </div>

        <div class="actions">
            <div class="carte">
                <h2>Gérer les codes d'accès</h2>
                <p>Créer et consulter les codes d'accès parents / élèves.</p>
                <a href="?module=portails&action=acces-codes" class="btn">Accéder</a>
            </div>
            <div class="carte">
                <h2>Portail consultation</h2>
                <p>Consulter notes, bulletins et absences.</p>
                <a href="?module=portails&action=portail-consultation" class="btn">Accéder</a>
            </div>
            <div class="carte">
                <h2>Portail paiements</h2>
                <p>Suivre les factures et paiements liés aux enfants.</p>
                <a href="?module=portails&action=portail-paiements" class="btn">Accéder</a>
            </div>
            <div class="carte">
                <h2>Emploi du temps</h2>
                <p>Voir l’emploi du temps de la semaine pour les enfants.</p>
                <a href="?module=portails&action=emplois-du-temps" class="btn">Accéder</a>
            </div>
            <div class="carte">
                <h2>Réservation repas</h2>
                <p>Réserver des repas et vérifier l'historique des réservations.</p>
                <a href="?module=portails&action=repas" class="btn">Accéder</a>
            </div>
        </div>
    </div>
</body>
</html>
