<?php
// Étape 16 — Permissions et profils
$param = $donnees['parametrage'] ?? null;
?>
<form method="post" action="<?= e(BASE_URL . '/parametrage/assistant?step=16') ?>">
    <input type="hidden" name="csrf_token" value="<?= e($donnees['token_csrf']) ?>">
    <p>Les profils par défaut seront créés via l'administration — continuer pour accepter les valeurs par défaut.</p>
    <div class="actions" style="margin-top:12px">
        <button type="submit">Continuer</button>
    </div>
</form>
