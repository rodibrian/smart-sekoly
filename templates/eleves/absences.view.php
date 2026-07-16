<?php
/**
 * Vue de consultation des absences d’un élève.
 *
 * @package Smart-Sekoly
 * @subpackage Templates
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Absences</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .conteneur { max-width: 860px; margin: 40px auto; padding: 28px; background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { padding: 10px; border-bottom: 1px solid #e5e7eb; text-align: left; }
    </style>
</head>
<body>
    <div class="conteneur">
        <h1>Consultation des absences</h1>
        <p>Suivi des absences pour l’élève n°<?= e($donnees['id_eleve']) ?>.</p>

        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Motif</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($donnees['absences'] as $absence): ?>
                    <tr>
                        <td><?= e($absence['date_absence']) ?></td>
                        <td><?= e($absence['motif']) ?></td>
                        <td><?= e($absence['statut']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
