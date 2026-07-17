<?php
// Étape 2 — Logo & chemin documents
$param = $donnees['parametrage'] ?? null;
?>
<form method="post" action="<?= e(BASE_URL . '/parametrage/assistant?step=2') ?>">
    <input type="hidden" name="csrf_token" value="<?= e($donnees['token_csrf']) ?>">
    <label>Chemin de stockage des documents</label>
    <input name="chemin_stockage_documents" value="<?= e($param ? $param->get_chemin_stockage_documents() : 'documents') ?>">
    <label>Logo (URL ou chemin)</label>
    <input name="logo" value="<?= e($param ? $param->logo ?? '' : '') ?>">
    <div class="actions" style="margin-top:12px">
        <button type="submit">Enregistrer et continuer</button>
    </div>
</form>
