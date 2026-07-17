<?php
/**
 * Vue principale du module Communication interne.
 */
$pageTitle = APP_NAME . ' — Communication interne';
require TEMPLATES_PATH . 'layout/header.php';
?>
<div class="space-y-8">
    <section class="space-y-3">
        <h1 class="text-3xl font-semibold text-slate-900">Communication interne</h1>
        <p class="text-slate-600">Messagerie, annonces et suivi des événements dans les carnets des élèves.</p>
    </section>

    <div class="grid gap-6 md:grid-cols-3">
        <article class="card p-6">
            <h2 class="text-xl font-semibold text-slate-900">Messages internes</h2>
            <div class="mt-4 text-4xl font-bold text-primary"><?= e($data['stats']['messages']) ?></div>
            <p class="mt-3 text-slate-600">Envoyer ou consulter des messages entre utilisateurs.</p>
            <a href="?module=communication&action=messages" class="btn-primary mt-6 inline-flex">Voir les messages</a>
        </article>

        <article class="card p-6">
            <h2 class="text-xl font-semibold text-slate-900">Annonces scolaires</h2>
            <div class="mt-4 text-4xl font-bold text-secondary"><?= e($data['stats']['annonces']) ?></div>
            <p class="mt-3 text-slate-600">Publier des annonces collectives pour l'établissement.</p>
            <a href="?module=communication&action=annonces" class="btn-primary mt-6 inline-flex">Voir les annonces</a>
        </article>

        <article class="card p-6">
            <h2 class="text-xl font-semibold text-slate-900">Événements de carnet</h2>
            <div class="mt-4 text-4xl font-bold text-accent"><?= e($data['stats']['evenements']) ?></div>
            <p class="mt-3 text-slate-600">Tracer des événements dans les carnets des élèves.</p>
            <a href="?module=communication&action=annonces" class="btn-primary mt-6 inline-flex">Gérer les annonces</a>
        </article>
    </div>
</div>
<?php require TEMPLATES_PATH . 'layout/footer.php';
