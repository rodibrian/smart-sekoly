<?php
// Étape 6 — Année scolaire active
$param = $donnees['parametrage'] ?? null;
?>
<form method="post" action="<?= e(BASE_URL . '/parametrage/assistant?step=6') ?>">
    <input type="hidden" name="csrf_token" value="<?= e($donnees['token_csrf']) ?>">
    <label>Année scolaire (libellé ex. 2026-2027)</label>
    <input name="annee_courante" value="<?= e($param ? $param->get_annee_courante() : '') ?>" required>
    <div class="actions" style="margin-top:12px">
        <button type="submit">Enregistrer et continuer</button>
    </div>
</form>
