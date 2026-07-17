<?php
/**
 * Vue de liste des élèves.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= e(BASE_URL . '/assets/css/responsive.css') ?>">
    <title><?= e(APP_NAME) ?> — Liste des élèves</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .conteneur { max-width: 960px; margin: 40px auto; padding: 28px; background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #e2e8f0; padding: 10px; text-align: left; }
        th { background: #eff6ff; }
        a { color: #2563eb; text-decoration: none; }
    </style>
</head>
<body>
    <div class="conteneur">
        <h1>Liste des élèves</h1>
        <p>Vue de consultation rapide de l’annuaire des élèves.</p>
        <form method="get" action="<?= e(BASE_URL . '/eleves/liste') ?>" style="margin-bottom: 16px;">
            <input type="text" name="q" value="<?= e($donnees['recherche'] ?? '') ?>" placeholder="Rechercher par nom, prénom ou matricule" style="width: 60%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px;">
            <button type="submit" style="padding: 10px 14px; border: 0; border-radius: 8px; background: #2563eb; color: #fff; cursor: pointer;">Rechercher</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Matricule</th>
                    <th>Email</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($donnees['eleves'] as $eleve): ?>
                    <tr>
                        <td><a href="<?= e(BASE_URL . '/eleves/dossier/' . ($eleve['id'] ?? 0)) ?>"><?= e($eleve['nom'] ?? '') ?></a></td>
                        <td><?= e($eleve['prenom'] ?? '') ?></td>
                        <td><?= e($eleve['matricule'] ?? '') ?></td>
                        <td><?= e($eleve['email'] ?? '') ?></td>
                        <td><?= e($eleve['statut'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
