<?php
/**
 * Vue du suivi des documents obligatoires d’un élève.
 *
 * @package Smart-Sekoly
 * @subpackage Templates
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= e(BASE_URL . '/assets/css/responsive.css') ?>">
    <title><?= e(APP_NAME) ?> — Documents obligatoires</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .conteneur { max-width: 860px; margin: 40px auto; padding: 28px; background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { padding: 10px; border-bottom: 1px solid #e5e7eb; text-align: left; }
        .badge { display: inline-block; padding: 6px 10px; border-radius: 999px; background: #dbeafe; color: #1d4ed8; }
        .badge.manquant { background: #fee2e2; color: #b91c1c; }
    </style>
</head>
<body>
    <div class="conteneur">
        <h1>Documents obligatoires</h1>
        <p>Suivi des pièces à fournir pour l’élève n°<?= e($donnees['id_eleve']) ?>.</p>

        <form method="post" action="<?= e(BASE_URL . '/eleves/documents-post/' . $donnees['id_eleve']) ?>">
            <input type="hidden" name="csrf_token" value="<?= e($donnees['token_csrf']) ?>">
            <table>
                <thead>
                    <tr>
                        <th>Document</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($donnees['documents'] as $document): ?>
                        <tr>
                            <td><?= e($document['nom']) ?></td>
                            <td>
                                <select name="documents[<?= e($document['nom']) ?>]">
                                    <option value="recu" <?= $document['statut'] === 'recu' ? 'selected' : '' ?>>Reçu</option>
                                    <option value="manquant" <?= $document['statut'] === 'manquant' ? 'selected' : '' ?>>Manquant</option>
                                </select>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button type="submit" style="margin-top: 16px; padding: 10px 16px; border: 0; border-radius: 8px; background: #2563eb; color: #fff; cursor: pointer;">Enregistrer le suivi</button>
        </form>
    </div>
</body>
</html>
