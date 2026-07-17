<?php
// Étape 12 — Paramètres financiers de base
$param = $donnees['parametrage'] ?? null;
?>
<form method="post" action="<?= e(BASE_URL . '/parametrage/assistant?step=12') ?>">
    <input type="hidden" name="csrf_token" value="<?= e($donnees['token_csrf']) ?>">
    <label>Devise affichée</label>
    <input name="monnaie" value="<?= e($param ? $param->get_monnaie() : 'MGA') ?>">
    <div class="actions" style="margin-top:12px">
        <button type="submit">Enregistrer et continuer</button>
    </div>
</form>
