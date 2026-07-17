<?php
// Étape 8 — Seuils d'alerte
$param = $donnees['parametrage'] ?? null;
?>
<form method="post" action="<?= e(BASE_URL . '/parametrage/assistant?step=8') ?>">
    <input type="hidden" name="csrf_token" value="<?= e($donnees['token_csrf']) ?>">
    <label>Seuil redoublement (moyenne)</label>
    <input name="seuil_redoublement" value="">
    <label>Seuil absences (jours)</label>
    <input name="seuil_absences" value="">
    <div class="actions" style="margin-top:12px">
        <button type="submit">Enregistrer et continuer</button>
    </div>
</form>
