<?php
/**
 * Vue formulaire d'ajout d'heures supplémentaires.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= e(BASE_URL . '/assets/css/responsive.css') ?>">
    <title><?= e(APP_NAME) ?> — Nouvelle heure supplémentaire</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .conteneur { max-width: 760px; margin: 40px auto; padding: 28px; background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        form { display: grid; gap: 18px; }
        label { display: block; font-weight: 700; margin-bottom: 8px; }
        input, select, textarea { width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; }
        button { padding: 12px 18px; border: none; border-radius: 10px; background: #1d4ed8; color: white; cursor: pointer; }
    </style>
</head>
<body>
    <div class="conteneur">
        <h1>Nouvelle heure supplémentaire</h1>
        <form method="post" action="<?= e(BASE_URL . '/heures-supplementaires/nouvelle') ?>">
            <input type="hidden" name="token_csrf" value="<?= e($donnees['token_csrf']) ?>">

            <label for="enseignant">Enseignant</label>
            <select id="enseignant" name="enseignant">
                <?php foreach ($donnees['enseignants'] as $enseignant): ?>
                    <option value="<?= e($enseignant['id']) ?>"><?= e($enseignant['nom']) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="classe">Classe</label>
            <select id="classe" name="classe">
                <?php foreach ($donnees['classes'] as $classe): ?>
                    <option value="<?= e($classe) ?>"><?= e($classe) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="matiere">Matière</label>
            <select id="matiere" name="matiere">
                <?php foreach ($donnees['matieres'] as $matiere): ?>
                    <option value="<?= e($matiere) ?>"><?= e($matiere) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="date_heure">Date</label>
            <input id="date_heure" name="date_heure" type="date" required>

            <label for="nombre_heures">Nombre d'heures</label>
            <input id="nombre_heures" name="nombre_heures" type="number" step="0.5" required>

            <label for="taux">Taux/MGA</label>
            <input id="taux" name="taux" type="number" value="15000" required>

            <button type="submit">Enregistrer</button>
        </form>
    </div>
</body>
</html>
