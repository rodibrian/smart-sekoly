<?php
/**
 * Vue de connexion utilisateur.
 */
$pageTitle = APP_NAME . ' — Connexion';
require TEMPLATES_PATH . 'layout/header.php';
?>
<div class="min-h-[calc(100vh-5rem)] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md card p-8">
        <h1 class="text-2xl font-semibold text-slate-900">Connexion</h1>
        <p class="mt-2 text-slate-600">Connectez-vous pour accéder à votre espace Smart-Sekoly.</p>

        <?php if (!empty($donnees['erreurs'])): ?>
            <div class="mt-6 rounded-xl border border-rose-200 bg-rose-50 p-4 text-rose-800">
                <ul class="list-disc space-y-1 pl-5">
                    <?php foreach ($donnees['erreurs'] as $erreur): ?>
                        <li><?= e($erreur) ?></li>
                    <?php endforeach ?>
                </ul>
            </div>
        <?php endif ?>

        <form class="mt-6 space-y-5" method="post" action="<?= e(BASE_URL . '/auth/login') ?>">
            <input type="hidden" name="csrf_token" value="<?= e($donnees['token_csrf']) ?>">

            <div>
                <label class="block text-sm font-medium text-slate-700" for="identifiant">Identifiant</label>
                <input id="identifiant" name="identifiant" type="text" value="<?= e($donnees['valeurs']['identifiant'] ?? '') ?>" required class="mt-2 w-full rounded-xl border border-slate-300 bg-surface px-4 py-3 text-base text-slate-900 outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20" />
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700" for="mot_de_passe">Mot de passe</label>
                <input id="mot_de_passe" name="mot_de_passe" type="password" required class="mt-2 w-full rounded-xl border border-slate-300 bg-surface px-4 py-3 text-base text-slate-900 outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20" />
            </div>

            <button type="submit" class="btn-primary w-full">Se connecter</button>
        </form>
    </div>
</div>
<?php require TEMPLATES_PATH . 'layout/footer.php';
