<?php
// Étape 11 — Utilisateurs initiaux
$param = $donnees['parametrage'] ?? null;
?>
<form method="post" action="<?= e(BASE_URL . '/parametrage/assistant?step=11') ?>">
    <input type="hidden" name="csrf_token" value="<?= e($donnees['token_csrf']) ?>">
    <p>Création des comptes initiaux (Directeur, Comptable, Enseignant, Secrétaire) peut être fait plus tard via l'administration.</p>
    <div class="actions" style="margin-top:12px">
        <button type="submit">Continuer</button>
    </div>
</form>
