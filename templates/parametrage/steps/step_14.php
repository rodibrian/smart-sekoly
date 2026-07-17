<?php
// Étape 14 — Sauvegarde & restauration (confirmation)
$param = $donnees['parametrage'] ?? null;
?>
<form method="post" action="<?= e(BASE_URL . '/parametrage/assistant?step=14') ?>">
    <input type="hidden" name="csrf_token" value="<?= e($donnees['token_csrf']) ?>">
    <p>Tester une sauvegarde et une restauration après l'installation initiale est recommandé.</p>
    <div class="actions" style="margin-top:12px">
        <button type="submit">Marquer comme testé et continuer</button>
    </div>
</form>
