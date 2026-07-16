<?php
/**
 * Vue liste des contrats.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Contrats</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .conteneur { max-width: 900px; margin: 40px auto; padding: 28px; background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { padding: 14px 12px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        th { background: #f1f5f9; }
        a { color: #1d4ed8; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .statut-actif { color: #166534; font-weight: 600; }
        .statut-termine { color: #7c2d12; font-weight: 600; }
    </style>
</head>
<body>
    <div class="conteneur">
        <h1>Gestion des contrats</h1>
        <p>Liste des contrats enseignants enregistrés dans le système.</p>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Début</th>
                    <th>Fin</th>
                    <th>Salaire</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($donnees['contrats'] as $contrat): ?>
                    <tr>
                        <td><?= e($contrat['id']) ?></td>
                        <td><?= e($contrat['type']) ?></td>
                        <td><?= e($contrat['debut']) ?></td>
                        <td><?= e($contrat['fin']) ?></td>
                        <td><?= e($contrat['salaire']) ?> MGA</td>
                        <td class="statut-<?= e($contrat['statut']) ?>"><?= e(ucfirst($contrat['statut'])) ?></td>
                        <td><a href="<?= e(BASE_URL . '/contrats/fiche/' . $contrat['id']) ?>">Voir</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
