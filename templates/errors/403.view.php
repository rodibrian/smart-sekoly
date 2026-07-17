<?php
/**
 * Vue d'accès refusé.
 */
$pageTitle = APP_NAME . ' — Accès refusé';
require TEMPLATES_PATH . 'layout/header.php';
?>
<div class="min-h-[calc(100vh-5rem)] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-2xl card p-8 text-slate-800">
        <div class="flex items-center gap-4 rounded-3xl bg-rose-50 p-6 shadow-sm border border-rose-100">
            <div class="rounded-full bg-rose-100 p-4 text-rose-600">
                <span class="text-xl font-bold">403</span>
            </div>
            <div>
                <h1 class="text-2xl font-semibold">Accès refusé</h1>
                <p class="mt-2 text-slate-600">Vous n’avez pas les permissions nécessaires pour accéder à cette page.</p>
            </div>
        </div>

        <div class="mt-6 space-y-3 text-slate-700">
            <p>Veuillez vous connecter avec un compte autorisé ou retourner à la page d’accueil.</p>
            <a class="inline-flex items-center gap-2 btn-secondary" href="<?= e(BASE_URL . '/auth/login') ?>">Se connecter</a>
        </div>
    </div>
</div>
<?php require TEMPLATES_PATH . 'layout/footer.php';
