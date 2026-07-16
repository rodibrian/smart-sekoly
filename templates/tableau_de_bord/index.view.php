<?php
/**
 * Vue du tableau de bord avec indicateurs clés.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Tableau de bord</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .page { max-width: 1200px; margin: 32px auto; padding: 24px; }
        h1 { margin-top: 0; }
        .grille { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 20px; }
        .indicateur { background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .indicateur h3 { margin: 0 0 10px 0; color: #64748b; font-size: 14px; font-weight: 600; text-transform: uppercase; }
        .indicateur .valeur { font-size: 32px; font-weight: 700; color: #0f172a; }
        .indicateur .sous-texte { font-size: 12px; color: #94a3b8; margin-top: 8px; }
        .actions { margin-top: 20px; }
        .bouton { display: inline-block; padding: 10px 16px; background: #2563eb; color: #fff; text-decoration: none; border-radius: 6px; margin-right: 10px; }
        .bouton:hover { background: #1d4ed8; }
    </style>
</head>
<body>
    <div class="page">
        <h1>Tableau de bord</h1>
        <p>Vue d'ensemble des indicateurs clés de l'établissement.</p>

        <div class="grille">
            <div class="indicateur">
                <h3>Élèves</h3>
                <div class="valeur"><?= $donnees['indicateurs']['total_eleves'] ?></div>
                <div class="sous-texte">Nombre total d'élèves inscrits</div>
            </div>

            <div class="indicateur">
                <h3>Enseignants</h3>
                <div class="valeur"><?= $donnees['indicateurs']['total_enseignants'] ?></div>
                <div class="sous-texte">Nombre total d'enseignants</div>
            </div>

            <div class="indicateur">
                <h3>Absences (mois)</h3>
                <div class="valeur"><?= $donnees['indicateurs']['absences_mois'] ?></div>
                <div class="sous-texte">Absences enregistrées ce mois</div>
            </div>

            <div class="indicateur">
                <h3>Taux de présence</h3>
                <div class="valeur"><?= number_format($donnees['indicateurs']['taux_presence'], 1) ?>%</div>
                <div class="sous-texte">Taux de présence global</div>
            </div>

            <div class="indicateur">
                <h3>Paiements (mois)</h3>
                <div class="valeur"><?= $donnees['indicateurs']['paiements_mois'] ?></div>
                <div class="sous-texte">Transactions enregistrées</div>
            </div>
        </div>

        <div class="actions">
            <a href="<?= e(BASE_URL . '/tableau-de-bord/agenda') ?>" class="bouton">Voir l'agenda</a>
            <a href="<?= e(BASE_URL . '/tableau-de-bord/actualites') ?>" class="bouton">Voir les actualités</a>
            <a href="<?= e(BASE_URL . '/eleves/liste') ?>" class="bouton">Gestion des élèves</a>
            <a href="<?= e(BASE_URL . '/enseignants/liste') ?>" class="bouton">Gestion des enseignants</a>
            <a href="?module=rapports&action=index" class="bouton">Rapports et statistiques</a>
            <a href="?module=portails&action=index" class="bouton">Portails Élève / Parent</a>
            <a href="?module=communication&action=index" class="bouton">Communication interne</a>
            <a href="<?= e(BASE_URL . '/bibliotheque/index') ?>" class="bouton">Bibliothèque documentaire</a>
        </div>
    </div>
</body>
</html>
