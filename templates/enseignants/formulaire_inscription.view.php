<?php
/**
 * Formulaire d'inscription d'un enseignant.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= e(BASE_URL . '/assets/css/responsive.css') ?>">
    <title><?= e(APP_NAME) ?> — Inscription enseignant</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .conteneur { max-width: 760px; margin: 40px auto; padding: 28px; background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        label { display: block; margin-top: 14px; font-weight: 700; }
        input, select { width: 100%; padding: 10px; margin-top: 6px; border: 1px solid #cbd5e1; border-radius: 8px; }
        button { margin-top: 20px; padding: 10px 16px; border: 0; border-radius: 8px; background: #2563eb; color: #fff; cursor: pointer; }
        .message { margin-top: 16px; padding: 12px; background: #eff6ff; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="conteneur">
        <h1>Ajouter un enseignant</h1>
        <?php if (!empty($donnees['message'])): ?>
            <div class="message"><?= e($donnees['message']) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= e(BASE_URL . '/enseignants/inscription') ?>">
            <input type="hidden" name="csrf_token" value="<?= e($donnees['token_csrf']) ?>">

            <label for="nom">Nom</label>
            <input id="nom" name="nom" value="<?= e($donnees['valeurs']['nom'] ?? '') ?>" required>

            <label for="prenom">Prénom</label>
            <input id="prenom" name="prenom" value="<?= e($donnees['valeurs']['prenom'] ?? '') ?>" required>

            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="<?= e($donnees['valeurs']['email'] ?? '') ?>" required>

            <label for="date_naissance">Date de naissance</label>
            <input id="date_naissance" name="date_naissance" type="date" value="<?= e($donnees['valeurs']['date_naissance'] ?? '') ?>" required>

            <label for="matricule">Matricule</label>
            <input id="matricule" name="matricule" value="<?= e($donnees['valeurs']['matricule'] ?? '') ?>" placeholder="ENS-2026-001" required>

            <label for="date_embauche">Date d'embauche</label>
            <input id="date_embauche" name="date_embauche" type="date" value="<?= e($donnees['valeurs']['date_embauche'] ?? date('Y-m-d')) ?>" required>

            <label for="statut_enseignant">Statut</label>
            <select id="statut_enseignant" name="statut_enseignant" required>
                <?php foreach (['actif' => 'Actif', 'en_conge' => 'En congé', 'sorti' => 'Sorti'] as $valeur => $libelle): ?>
                    <option value="<?= e($valeur) ?>" <?= ($donnees['valeurs']['statut_enseignant'] ?? 'actif') === $valeur ? 'selected' : '' ?>><?= e($libelle) ?></option>
                <?php endforeach; ?>
            </select>

            <?php if (!empty($donnees['erreurs'])): ?>
                <div class="message" style="background:#fef2f2;color:#991b1b;">
                    <strong>Erreurs :</strong>
                    <ul>
                        <?php foreach ($donnees['erreurs'] as $message): ?>
                            <li><?= e($message) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <button type="submit">Enregistrer</button>
        </form>
    </div>
</body>
</html>
