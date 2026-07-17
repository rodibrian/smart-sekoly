<?php
// Étape 1 — Informations basiques
$param = $donnees['parametrage'] ?? null;
?>
<form method="post" action="<?= e(BASE_URL . '/parametrage/assistant?step=1') ?>">
    <input type="hidden" name="csrf_token" value="<?= e($donnees['token_csrf']) ?>">
    <label>Nom de l'établissement</label>
    <input name="nom_etablissement" value="<?= e($param ? $param->get_nom_etablissement() : '') ?>" required>
    <div class="actions" style="margin-top:12px">
        <button type="submit">Enregistrer et continuer</button>
    </div>
</form>
