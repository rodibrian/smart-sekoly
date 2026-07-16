<?php
/**
 * Vue de réservation et historique des repas pour le portail.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Réservation Repas</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8fafc; margin: 0; color: #111827; }
        .page { max-width: 980px; margin: 32px auto; padding: 24px; }
        .card { background: white; border-radius: 14px; box-shadow: 0 12px 24px rgba(15,23,42,.08); padding: 22px; margin-bottom: 20px; }
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
            <h1>Réservation repas</h1>
            <p>Réservez un repas pour un élève et consultez l'historique des réservations.</p>
            <form method="post" action="?module=portails&action=repas">
                <input type="hidden" name="csrf_token" value="<?= e($data['token_csrf']) ?>">
                <div class="grid">
                    <div class="form-group">
                        <label for="eleve">Élève</label>
                        <select id="eleve" name="eleve" required>
                            <?php foreach ($data['eleves'] as $eleve): ?>
                                <option value="<?= e($eleve['id_eleve'] ?? $eleve['id']) ?>"><?= e($eleve['prenom'] . ' ' . $eleve['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="option_repas">Option repas</label>
                        <select id="option_repas" name="option_repas" required>
                            <?php foreach ($data['options'] as $option): ?>
                                <option value="<?= e($option) ?>"><?= e($option) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <button type="submit">Réserver</button>
            </form>
        </div>

        <div class="card">
            <h2>Historique des réservations</h2>
            <?php if (empty($data['reservations'])): ?>
                <p>Aucune réservation de repas n'a encore été effectuée.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Élève</th>
                            <th>Option</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['reservations'] as $reservation): ?>
                            <tr>
                                <td><?= e($reservation['date']) ?></td>
                                <td><?= e($reservation['id_eleve']) ?></td>
                                <td><?= e($reservation['option']) ?></td>
                                <td><?= e($reservation['statut']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
