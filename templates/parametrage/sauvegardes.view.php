<?php
/**
 * Vue de gestion des sauvegardes automatiques.
 *
 * @package Smart-Sekoly
 * @subpackage Templates
 */
$configuration = $_SESSION['sauvegarde_config'] ?? [
    'frequence' => 'quotidienne',
    'repertoire' => 'backups',
    'retention' => '7',
    'activer' => '1',
];
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Sauvegardes</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .conteneur { max-width: 760px; margin: 40px auto; padding: 28px; background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        label { display: block; margin-top: 12px; font-weight: 700; }
        input, select { width: 100%; padding: 10px; margin-top: 6px; border: 1px solid #cbd5e1; border-radius: 8px; }
        button { margin-top: 16px; padding: 10px 16px; border: 0; border-radius: 8px; background: #0f766e; color: #fff; cursor: pointer; }
        .message { margin-top: 16px; padding: 12px; background: #ecfeff; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="conteneur">
        <h1>Gestion des sauvegardes automatiques</h1>
        <p>Définissez la périodicité et le répertoire de sauvegarde du système.</p>

        <form method="post" action="<?= e(BASE_URL . '/parametrage/sauvegardes') ?>">
            <input type="hidden" name="csrf_token" value="<?= e($donnees['token_csrf']) ?>">

            <label for="activer">Activer les sauvegardes</label>
            <select id="activer" name="activer">
                <option value="1" <?= $configuration['activer'] === '1' ? 'selected' : '' ?>>Oui</option>
                <option value="0" <?= $configuration['activer'] === '0' ? 'selected' : '' ?>>Non</option>
            </select>

            <label for="frequence">Fréquence</label>
            <select id="frequence" name="frequence">
                <option value="quotidienne" <?= $configuration['frequence'] === 'quotidienne' ? 'selected' : '' ?>>Quotidienne</option>
                <option value="hebdomadaire" <?= $configuration['frequence'] === 'hebdomadaire' ? 'selected' : '' ?>>Hebdomadaire</option>
                <option value="mensuelle" <?= $configuration['frequence'] === 'mensuelle' ? 'selected' : '' ?>>Mensuelle</option>
            </select>

            <label for="repertoire">Répertoire de sauvegarde</label>
            <input id="repertoire" name="repertoire" value="<?= e($configuration['repertoire']) ?>">

            <label for="retention">Nombre de sauvegardes conservées</label>
            <input id="retention" name="retention" value="<?= e($configuration['retention']) ?>">

            <button type="submit">Enregistrer</button>
        </form>

        <div class="message">
            La configuration actuelle est stockée dans la session et sera prêtée à la base de données à l’étape suivante.
        </div>
    </div>
</body>
</html>
