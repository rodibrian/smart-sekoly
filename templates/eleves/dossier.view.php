<?php
/**
 * Vue du dossier élève unique.
 *
 * @package Smart-Sekoly
 * @subpackage Templates
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Dossier élève</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .conteneur { max-width: 860px; margin: 40px auto; padding: 28px; background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        .carte { border: 1px solid #e2e8f0; padding: 16px; border-radius: 10px; margin-top: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { padding: 10px; border-bottom: 1px solid #e5e7eb; text-align: left; }
        .badge { display: inline-block; padding: 6px 10px; background: #dbeafe; color: #1d4ed8; border-radius: 999px; }
    </style>
</head>
<body>
    <div class="conteneur">
        <h1>Dossier élève</h1>
        <p>Vue consolidée des informations principales de l’élève et de son parcours.</p>

        <div class="carte">
            <h2><?= e($donnees['eleve']['prenom'] . ' ' . $donnees['eleve']['nom']) ?></h2>
            <p><strong>Matricule :</strong> <?= e($donnees['eleve']['matricule']) ?></p>
            <p><strong>Email :</strong> <?= e($donnees['eleve']['email']) ?></p>
            <p><strong>Statut :</strong> <span class="badge"><?= e($donnees['eleve']['statut']) ?></span></p>
            <p><strong>Date de naissance :</strong> <?= e($donnees['eleve']['date_naissance']) ?></p>
        </div>

        <div class="carte">
            <h3>Historique des inscriptions</h3>
            <table>
                <thead>
                    <tr>
                        <th>Année scolaire</th>
                        <th>Classe</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($donnees['eleve']['inscriptions'] as $inscription): ?>
                        <tr>
                            <td><?= e($inscription['annee']) ?></td>
                            <td><?= e($inscription['classe']) ?></td>
                            <td><?= e($inscription['statut']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
