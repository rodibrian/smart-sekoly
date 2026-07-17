<?php
/**
 * Vue du tableau de bord avec indicateurs clés.
 */
$pageTitle = APP_NAME . ' — Tableau de bord';
require TEMPLATES_PATH . 'layout/header.php';
?>
<div class="space-y-8">
    <section class="space-y-3">
        <h1 class="text-3xl font-semibold text-slate-900">Tableau de bord</h1>
        <p class="text-slate-600">Vue d'ensemble des indicateurs clés de l'établissement.</p>
    </section>

    <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
        <article class="card p-6">
            <h3 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">Élèves</h3>
            <div class="mt-4 text-4xl font-bold text-slate-900"><?= e($donnees['indicateurs']['total_eleves']) ?></div>
            <p class="mt-3 text-slate-600">Nombre total d'élèves inscrits</p>
        </article>

        <article class="card p-6">
            <h3 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">Enseignants</h3>
            <div class="mt-4 text-4xl font-bold text-slate-900"><?= e($donnees['indicateurs']['total_enseignants']) ?></div>
            <p class="mt-3 text-slate-600">Nombre total d'enseignants</p>
        </article>

        <article class="card p-6">
            <h3 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">Absences (mois)</h3>
            <div class="mt-4 text-4xl font-bold text-primary"><?= e($donnees['indicateurs']['absences_mois']) ?></div>
            <p class="mt-3 text-slate-600">Absences enregistrées ce mois</p>
        </article>

        <article class="card p-6">
            <h3 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">Taux de présence</h3>
            <div class="mt-4 text-4xl font-bold text-secondary"><?= e(number_format($donnees['indicateurs']['taux_presence'], 1)) ?>%</div>
            <p class="mt-3 text-slate-600">Taux de présence global</p>
        </article>

        <article class="card p-6">
            <h3 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">Paiements (mois)</h3>
            <div class="mt-4 text-4xl font-bold text-accent"><?= e($donnees['indicateurs']['paiements_mois']) ?></div>
            <p class="mt-3 text-slate-600">Transactions enregistrées</p>
        </article>
    </div>

    <div class="grid gap-4 lg:grid-cols-2">
        <a href="<?= e(BASE_URL . '/tableau-de-bord/agenda') ?>" class="btn-primary">Voir l'agenda</a>
        <a href="<?= e(BASE_URL . '/tableau-de-bord/actualites') ?>" class="btn-primary">Voir les actualités</a>
        <a href="<?= e(BASE_URL . '/eleves/liste') ?>" class="btn-primary">Gestion des élèves</a>
        <a href="<?= e(BASE_URL . '/enseignants/liste') ?>" class="btn-primary">Gestion des enseignants</a>
        <a href="?module=rapports&action=index" class="btn-primary">Rapports et statistiques</a>
        <a href="?module=portails&action=index" class="btn-primary">Portails Élève / Parent</a>
        <a href="?module=communication&action=index" class="btn-primary">Communication interne</a>
        <a href="<?= e(BASE_URL . '/bibliotheque/index') ?>" class="btn-primary">Bibliothèque documentaire</a>
    </div>
</div>
<?php require TEMPLATES_PATH . 'layout/footer.php';
