<?php
// Étape 3 — Monnaie & langue
$param = $donnees['parametrage'] ?? null;
?>
<form method="post" action="<?= e(BASE_URL . '/parametrage/assistant?step=3') ?>">
    <input type="hidden" name="csrf_token" value="<?= e($donnees['token_csrf']) ?>">
    <label>Monnaie (ex. MGA)</label>
    <input name="monnaie" value="<?= e($param ? $param->get_monnaie() : 'MGA') ?>" required>
    <label>Langue par défaut</label>
    <input name="langue_par_defaut" value="<?= e($param ? $param->get_langue_par_defaut() : 'fr') ?>">
    <div class="actions" style="margin-top:12px">
        <button type="submit">Enregistrer et continuer</button>
    </div>
</form>
