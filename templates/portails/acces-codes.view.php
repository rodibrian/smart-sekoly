<?php
/**
 * Vue pour la gestion des codes d'accès parent/élève.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= e(BASE_URL . '/assets/css/responsive.css') ?>">
    <title><?= e(APP_NAME) ?> — Codes d'accès Portails</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8fafc; color: #111827; margin: 0; }
        .page { max-width: 1000px; margin: 32px auto; padding: 24px; }
        .card { background: white; border-radius: 14px; box-shadow: 0 12px 24px rgba(15,23,42,.08); padding: 22px; margin-bottom: 20px; }
        .card h1, .card h2 { margin-top: 0; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-group { margin-bottom: 14px; }
        label { display: block; margin-bottom: 6px; color: #334155; font-weight: 600; }
        input, select { width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 10px; }
        button { padding: 12px 18px; background: #2563eb; color: white; border: none; border-radius: 10px; cursor: pointer; }
        button:hover { background: #1d4ed8; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { padding: 12px 10px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        th { background: #f1f5f9; }
    </style>
</head>
<body>
    <div class="page">
        <div class="card">
            <h1>Codes d'accès Portails</h1>
            <p>Générez un code sécurisé pour un parent et associez-le à un ou plusieurs élèves.</p>
            <form method="post" action="?module=portails&action=acces-codes">
                <input type="hidden" name="csrf_token" value="<?= e($data['token_csrf']) ?>">
                <div class="grid">
                    <div class="form-group">
                        <label for="parent_nom">Nom du parent</label>
                        <input type="text" id="parent_nom" name="parent_nom" required>
                    </div>
                    <div class="form-group">
                        <label for="parent_type">Type de parent</label>
                        <select id="parent_type" name="parent_type" required>
                            <?php foreach ($data['parents_types'] as $type): ?>
                                <option value="<?= e($type) ?>"><?= e($type) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="eleves">Élèves associés</label>
                    <select id="eleves" name="eleves[]" multiple size="5" required>
                        <?php foreach ($data['eleves'] as $eleve): ?>
                            <option value="<?= e($eleve['id_eleve'] ?? $eleve['id']) ?>"><?= e($eleve['prenom'] . ' ' . $eleve['nom']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit">Générer le code</button>
            </form>
        </div>

        <div class="card">
            <h2>Codes existants</h2>
            <?php if (empty($data['codes'])): ?>
                <p>Aucun code n'a encore été généré.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Parent</th>
                            <th>Type</th>
                            <th>Enfants</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['codes'] as $code): ?>
                            <tr>
                                <td><?= e($code['code']) ?></td>
                                <td><?= e($code['parent_nom']) ?></td>
                                <td><?= e($code['parent_type']) ?></td>
                                <td><?= e(implode(', ', array_map(fn($id) => 'ID ' . e($id), $code['enfants']))) ?></td>
                                <td><?= e($code['statut']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
