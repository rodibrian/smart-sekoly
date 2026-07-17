<?php
/**
 * Shared page header for Smart-Sekoly.
 */

if (!defined('BASE_URL')) {
    exit('BASE_URL not defined.');
}

?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= e(BASE_URL . '/assets/css/dist/app.css') ?>">
    <script src="<?= e(BASE_URL . '/assets/js/app.js') ?>" defer></script>
</head>
<body class="bg-background text-slate-900">
    <header class="border-b border-slate-200 bg-surface/80 backdrop-blur-xl">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <div>
                <a href="<?= e(BASE_URL . '/') ?>" class="text-lg font-semibold text-slate-900"><?= e(APP_NAME) ?></a>
            </div>
            <button id="themeToggle" type="button" class="btn-secondary hidden sm:inline-flex">Thème</button>
        </div>
    </header>
    <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
