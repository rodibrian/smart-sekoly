<?php
/**
 * Formulaire d'édition d'un élève.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= e(BASE_URL . '/assets/css/responsive.css') ?>">
    <title><?= e(APP_NAME) ?> — Édition élève</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .conteneur { max-width: 720px; margin: 40px auto; padding: 28px; background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        label { display: block; margin-top: 12px; font-weight: 700; }
        input { width: 100%; padding: 10px; margin-top: 6px; border: 1px solid #cbd5e1; border-radius: 8px; }
        button { margin-top: 16px; padding: 10px 16px; border: 0; border-radius: 8px; background: #2563eb; color: #fff; cursor: pointer; }
    </style>
</head>
<body>
    <div class="conteneur">
        <h1>Éditer le profil de l’élève</h1>
        <form method="post" action="<?= e(BASE_URL . '/eleves/edition/' . $donnees['id_eleve']) ?>">
            <input type="hidden" name="csrf_token" value="<?= e($donnees['token_csrf']) ?>">
            <label>Nom</label>
            <input name="nom" value="<?= e($donnees['eleve']['nom'] ?? '') ?>" required>
            <label>Prénom</label>
            <input name="prenom" value="<?= e($donnees['eleve']['prenom'] ?? '') ?>" required>
            <label>Email</label>
            <input name="email" type="email" value="<?= e($donnees['eleve']['email'] ?? '') ?>" required>
            <label>Date de naissance</label>
            <input name="date_naissance" type="date" value="<?= e($donnees['eleve']['date_naissance'] ?? '') ?>" required>
            <label>Matricule</label>
            <input name="matricule" value="<?= e($donnees['eleve']['matricule'] ?? '') ?>" required>

            <label for="role_id">Rôle</label>
            <select id="role_id" name="role_id" required>
                <option value="">Sélectionnez un rôle</option>
                <?php foreach ($donnees['roles'] ?? [] as $role): ?>
                    <option value="<?= e($role['id_role']) ?>" <?= (int) ($donnees['role_id'] ?? 0) === (int) $role['id_role'] ? 'selected' : '' ?>>
                        <?= e($role['libelle']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (!empty($donnees['erreurs']['role_id'])): ?>
                <div class="message" style="background:#fef2f2;color:#991b1b; margin-top: 8px;"> <?= e($donnees['erreurs']['role_id']) ?></div>
            <?php endif; ?>

            <button type="submit">Enregistrer les modifications</button>
        </form>
    </div>
</body>
</html>
