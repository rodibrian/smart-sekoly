<?php
// Étape 9 — Modèles de documents
$param = $donnees['parametrage'] ?? null;
?>
<form method="post" action="<?= e(BASE_URL . '/parametrage/assistant?step=9') ?>">
    <input type="hidden" name="csrf_token" value="<?= e($donnees['token_csrf']) ?>">
    <p>Vous pourrez créer des modèles de document (bulletin, reçu...) plus tard dans Paramétrage → Modèles.</p>
    <div class="actions" style="margin-top:12px">
        <button type="submit">Continuer</button>
    </div>
</form>
