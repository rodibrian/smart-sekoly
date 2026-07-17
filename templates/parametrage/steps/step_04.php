<?php
// Étape 4 — Thème & accessibilité
$param = $donnees['parametrage'] ?? null;
?>
<form method="post" action="<?= e(BASE_URL . '/parametrage/assistant?step=4') ?>">
    <input type="hidden" name="csrf_token" value="<?= e($donnees['token_csrf']) ?>">
    <label>Thème par défaut (clair/sombre)</label>
    <select name="theme_par_defaut">
        <option value="clair" <?= ($param && $param->get_theme_par_defaut() === 'clair') ? 'selected' : '' ?>>Clair</option>
        <option value="sombre" <?= ($param && $param->get_theme_par_defaut() === 'sombre') ? 'selected' : '' ?>>Sombre</option>
    </select>
    <div class="actions" style="margin-top:12px">
        <button type="submit">Enregistrer et continuer</button>
    </div>
</form>
