<?php
/**
 * Assistant multi-étapes (vue shell). Inclut les partials `steps/step_{n}.php`.
 */
$step = (int) ($donnees['step'] ?? 1);
$param = $donnees['parametrage'] ?? null;
?><!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Assistant — étape <?= e($step) ?></title>
    <link rel="stylesheet" href="<?= e(BASE_URL . '/assets/css/responsive.css') ?>">
    <style>
        .conteneur{max-width:900px;margin:30px auto;padding:20px;background:#fff;border-radius:8px}
        .progress{height:10px;background:#e6edf0;border-radius:6px;margin-bottom:16px}
        .progress > span{display:block;height:100%;background:#0f766e;border-radius:6px}
        .nav{margin-top:14px}
        .actions button{padding:8px 12px;border-radius:6px}
    </style>
</head>
<body>
<div class="conteneur">
    <h1>Assistant de configuration — Étape <?= e($step) ?> / 19</h1>
    <div class="progress"><span style="width:<?= (int)(($step/19)*100) ?>%"></span></div>

    <?php
    $partial = TEMPLATES_PATH . 'parametrage/steps/step_' . str_pad($step,2,'0',STR_PAD_LEFT) . '.php';
    if (is_file($partial)) {
        require $partial;
    } else {
        echo '<p>Étape non trouvée.</p>';
    }
    ?>

    <div class="nav">
        <?php if ($step > 1): ?>
            <a href="<?= e(BASE_URL . '/parametrage/assistant?step=' . ($step-1)) ?>"><button type="button">‹ Précédent</button></a>
        <?php endif; ?>
        <?php if ($step < 19): ?>
            <a href="<?= e(BASE_URL . '/parametrage/assistant?step=' . ($step+1)) ?>"><button type="button">Suivant ›</button></a>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
