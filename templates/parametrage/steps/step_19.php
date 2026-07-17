<?php
// Étape 19 — Finalisation
$param = $donnees['parametrage'] ?? null;
?>
<form method="post" action="<?= e(BASE_URL . '/parametrage/assistant?step=19') ?>">
    <input type="hidden" name="csrf_token" value="<?= e($donnees['token_csrf']) ?>">
    <p>Configuration terminée. Vous pouvez désormais utiliser l'application.</p>
    <div style="margin-top:12px">
        <a href="<?= e(BASE_URL . '/') ?>"><button type="button">Aller au tableau de bord</button></a>
        <button type="submit">Terminer et sauvegarder</button>
    </div>
</form>
