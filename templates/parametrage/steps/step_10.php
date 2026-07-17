<?php
// Étape 10 — Sauvegarde automatique
$param = $donnees['parametrage'] ?? null;
?>
<form method="post" action="<?= e(BASE_URL . '/parametrage/assistant?step=10') ?>">
    <input type="hidden" name="csrf_token" value="<?= e($donnees['token_csrf']) ?>">
    <label>Activer sauvegardes automatiques</label>
    <select name="frequence">
        <option value="quotidienne">Quotidienne</option>
        <option value="hebdomadaire">Hebdomadaire</option>
        <option value="mensuelle">Mensuelle</option>
    </select>
    <label>Répertoire de sauvegarde</label>
    <input name="repertoire" value="backups">
    <div class="actions" style="margin-top:12px">
        <button type="submit">Enregistrer et continuer</button>
    </div>
</form>
