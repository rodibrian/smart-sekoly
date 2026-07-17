<?php
// Étape 18 — Tests & vérifications
$param = $donnees['parametrage'] ?? null;
?>
<form method="post" action="<?= e(BASE_URL . '/parametrage/assistant?step=18') ?>">
    <input type="hidden" name="csrf_token" value="<?= e($donnees['token_csrf']) ?>">
    <p>Exécuter les scénarios de test principaux après configuration (seed, génération matricule, création utilisateur).</p>
    <div class="actions" style="margin-top:12px">
        <button type="submit">Marquer tests réalisés et continuer</button>
    </div>
</form>
