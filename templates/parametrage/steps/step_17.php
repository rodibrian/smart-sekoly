<?php
// Étape 17 — Paramètres pédagogiques
$param = $donnees['parametrage'] ?? null;
?>
<form method="post" action="<?= e(BASE_URL . '/parametrage/assistant?step=17') ?>">
    <input type="hidden" name="csrf_token" value="<?= e($donnees['token_csrf']) ?>">
    <label>Effectif max par classe (par défaut)</label>
    <input name="effectif_max_par_defaut" value="30">
    <div class="actions" style="margin-top:12px">
        <button type="submit">Enregistrer et continuer</button>
    </div>
</form>
