<?php
// Étape 15 — Règles de sécurité des comptes
$param = $donnees['parametrage'] ?? null;
?>
<form method="post" action="<?= e(BASE_URL . '/parametrage/assistant?step=15') ?>">
    <input type="hidden" name="csrf_token" value="<?= e($donnees['token_csrf']) ?>">
    <label>Longueur minimale mot de passe</label>
    <input name="pwd_min_length" value="8">
    <label>Verrouillage après échecs</label>
    <input name="pwd_lock_after" value="5">
    <div class="actions" style="margin-top:12px">
        <button type="submit">Enregistrer et continuer</button>
    </div>
</form>
