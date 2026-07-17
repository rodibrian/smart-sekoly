<?php
// Étape 7 — Numérotation (séquences de documents)
$param = $donnees['parametrage'] ?? null;
?>
<form method="post" action="<?= e(BASE_URL . '/parametrage/assistant?step=7') ?>">
    <input type="hidden" name="csrf_token" value="<?= e($donnees['token_csrf']) ?>">
    <p>Les séquences (reçus, factures, matricules) seront initialisées automatiquement lors de la première génération.</p>
    <div class="actions" style="margin-top:12px">
        <button type="submit">Continuer</button>
    </div>
</form>
