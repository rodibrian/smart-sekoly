<?php
// Étape 13 — Paramètres de communication
$param = $donnees['parametrage'] ?? null;
?>
<form method="post" action="<?= e(BASE_URL . '/parametrage/assistant?step=13') ?>">
    <input type="hidden" name="csrf_token" value="<?= e($donnees['token_csrf']) ?>">
    <label>Email administratif</label>
    <input name="email_admin" value="">
    <label>Mode notification (email/sms)</label>
    <select name="mode_notification">
        <option value="email">Email</option>
        <option value="sms">SMS</option>
    </select>
    <div class="actions" style="margin-top:12px">
        <button type="submit">Enregistrer et continuer</button>
    </div>
</form>
