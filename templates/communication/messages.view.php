<?php
/**
 * Vue des messages internes.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Messages internes</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #111827; }
        .page { max-width: 1000px; margin: 32px auto; padding: 24px; }
        .carte { background: white; padding: 22px; border-radius: 14px; box-shadow: 0 12px 24px rgba(15,23,42,.08); margin-bottom: 20px; }
        .grille { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-group { margin-bottom: 16px; }
        label { display: block; margin-bottom: 6px; color: #334155; }
        input, textarea { width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 10px; }
        textarea { min-height: 140px; resize: vertical; }
        button { padding: 12px 18px; background: #2563eb; color: white; border: none; border-radius: 10px; cursor: pointer; }
        button:hover { background: #1d4ed8; }
        .liste { margin-top: 20px; }
        .message { background: #eff6ff; padding: 14px; border-radius: 12px; margin-bottom: 12px; }
        .message h3 { margin: 0 0 6px; }
        .message small { color: #64748b; }
    </style>
</head>
<body>
    <div class="page">
        <div class="carte">
            <h1>Messages internes</h1>
            <p>Envoyez un message à un autre utilisateur du système.</p>
            <form method="post" action="?module=communication&action=messages">
                <input type="hidden" name="csrf_token" value="<?= e($data['token_csrf']) ?>">
                <div class="form-group">
                    <label for="destinataire">Destinataire</label>
                    <input type="text" id="destinataire" name="destinataire" required>
                </div>
                <div class="form-group">
                    <label for="contenu">Message</label>
                    <textarea id="contenu" name="contenu" required></textarea>
                </div>
                <button type="submit">Envoyer</button>
            </form>
        </div>

        <div class="carte liste">
            <h2>Historique des messages</h2>
            <?php if (empty($data['messages'])): ?>
                <p>Aucun message envoyé pour le moment.</p>
            <?php else: ?>
                <?php foreach ($data['messages'] as $message): ?>
                    <div class="message">
                        <h3>À <?= e($message['destinataire']) ?></h3>
                        <small><?= e($message['date']) ?></small>
                        <p><?= e($message['contenu']) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
