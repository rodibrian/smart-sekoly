<?php
// Étape 5 — Format matricule
$param = $donnees['parametrage'] ?? null;
?>
<form method="post" action="<?= e(BASE_URL . '/parametrage/assistant?step=5') ?>">
    <input type="hidden" name="csrf_token" value="<?= e($donnees['token_csrf']) ?>">
    <label>Format du matricule (placeholders : {PREFIXE}, {ANNEE}, {NUMERO_SEQUENTIEL})</label>
    <input name="format_matricule" value="<?= e($param ? $param->get_format_matricule() : '{PREFIXE}-{ANNEE}-{NUMERO_SEQUENTIEL}') ?>" required>
    <label>Préfixe</label>
    <input name="prefixe_matricule" value="<?= e($param ? $param->get_prefixe_matricule() : 'EL') ?>" required>
    <div class="actions" style="margin-top:12px">
        <button type="submit">Enregistrer et continuer</button>
    </div>
</form>
