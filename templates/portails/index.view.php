<?php
/**
 * Vue principale du module Portails Élève / Parent.
 */
$pageTitle = APP_NAME . ' — Portails Élève / Parent';
require TEMPLATES_PATH . 'layout/header.php';
?>
<div class="space-y-8">
    <section class="space-y-3">
        <h1 class="text-3xl font-semibold text-slate-900">Portails Élève / Parent</h1>
        <p class="text-slate-600">Accédez aux portails de consultation, paiements, emploi du temps et réservation de repas.</p>
    </section>

    <div class="grid gap-6 md:grid-cols-3">
        <article class="rounded-xl border border-slate-200 bg-surface p-5 shadow-sm">
            <div class="text-sm uppercase tracking-[0.2em] text-slate-500">Codes d'accès actifs</div>
            <div class="mt-4 text-4xl font-bold text-primary"><?= e($data['statut']['codes_actifs']) ?></div>
        </article>
        <article class="rounded-xl border border-slate-200 bg-surface p-5 shadow-sm">
            <div class="text-sm uppercase tracking-[0.2em] text-slate-500">Élèves enregistrés</div>
            <div class="mt-4 text-4xl font-bold text-secondary"><?= e($data['statut']['eleves_actifs']) ?></div>
        </article>
        <article class="rounded-xl border border-slate-200 bg-surface p-5 shadow-sm">
            <div class="text-sm uppercase tracking-[0.2em] text-slate-500">Parents référencés</div>
            <div class="mt-4 text-4xl font-bold text-accent"><?= e($data['statut']['parents_actifs']) ?></div>
        </article>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <article class="card p-6">
            <h2 class="text-xl font-semibold text-slate-900">Gérer les codes d'accès</h2>
            <p class="mt-3 text-slate-600">Créer et consulter les codes d'accès parents / élèves.</p>
            <a href="?module=portails&action=acces-codes" class="btn-primary mt-6 inline-flex">Accéder</a>
        </article>
        <article class="card p-6">
            <h2 class="text-xl font-semibold text-slate-900">Portail consultation</h2>
            <p class="mt-3 text-slate-600">Consulter notes, bulletins et absences.</p>
            <a href="?module=portails&action=portail-consultation" class="btn-primary mt-6 inline-flex">Accéder</a>
        </article>
        <article class="card p-6">
            <h2 class="text-xl font-semibold text-slate-900">Portail paiements</h2>
            <p class="mt-3 text-slate-600">Suivre les factures et paiements liés aux enfants.</p>
            <a href="?module=portails&action=portail-paiements" class="btn-primary mt-6 inline-flex">Accéder</a>
        </article>
        <article class="card p-6">
            <h2 class="text-xl font-semibold text-slate-900">Emploi du temps</h2>
            <p class="mt-3 text-slate-600">Voir l’emploi du temps de la semaine pour les enfants.</p>
            <a href="?module=portails&action=emplois-du-temps" class="btn-primary mt-6 inline-flex">Accéder</a>
        </article>
        <article class="card p-6">
            <h2 class="text-xl font-semibold text-slate-900">Réservation repas</h2>
            <p class="mt-3 text-slate-600">Réserver des repas et vérifier l'historique des réservations.</p>
            <a href="?module=portails&action=repas" class="btn-primary mt-6 inline-flex">Accéder</a>
        </article>
    </div>
</div>
<?php require TEMPLATES_PATH . 'layout/footer.php';
