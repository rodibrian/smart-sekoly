<?php
/**
 * Vue de changement de mot de passe obligatoire.
 */
$pageTitle = APP_NAME . ' — Changer le mot de passe';
require TEMPLATES_PATH . 'layout/header.php';
?>
<div class="min-h-[calc(100vh-5rem)] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-lg card p-8">
        <h1 class="text-2xl font-semibold text-slate-900">Changer le mot de passe</h1>
        <div class="mt-3 rounded-xl bg-slate-50 p-4 text-slate-700">
            Vous devez changer votre mot de passe avant de continuer.
        </div>

        <?php if (!empty($donnees['erreurs'])): ?>
            <div class="mt-6 rounded-xl border border-rose-200 bg-rose-50 p-4 text-rose-800">
                <ul class="list-disc space-y-1 pl-5">
                    <?php foreach ($donnees['erreurs'] as $erreur): ?>
                        <li><?= e($erreur) ?></li>
                    <?php endforeach ?>
                </ul>
            </div>
        <?php endif ?>

        <form class="mt-6 space-y-5" method="post" action="<?= e(BASE_URL . '/auth/changer-mot-de-passe') ?>">
            <input type="hidden" name="csrf_token" value="<?= e($donnees['token_csrf']) ?>">

            <div>
                <label class="block text-sm font-medium text-slate-700" for="mot_de_passe">Nouveau mot de passe</label>
                <input id="mot_de_passe" name="mot_de_passe" type="password" required class="mt-2 w-full rounded-xl border border-slate-300 bg-surface px-4 py-3 text-base text-slate-900 outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20" />
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700" for="confirmation_mot_de_passe">Confirmer le mot de passe</label>
                <input id="confirmation_mot_de_passe" name="confirmation_mot_de_passe" type="password" required class="mt-2 w-full rounded-xl border border-slate-300 bg-surface px-4 py-3 text-base text-slate-900 outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20" />
            </div>

            <button type="submit" class="btn-primary w-full">Valider</button>
        </form>
    </div>
</div>
<?php require TEMPLATES_PATH . 'layout/footer.php';
