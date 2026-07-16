<?php
/**
 * Formulaire d'édition d'un enseignant.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Édition enseignant</title>
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
        <h1>Modifier l’enseignant</h1>
        <form method="post" action="<?= e(BASE_URL . '/enseignants/edition/' . $donnees['id_enseignant']) ?>">
            <input type="hidden" name="csrf_token" value="<?= e($donnees['token_csrf']) ?>">

            <label for="nom">Nom</label>
            <input id="nom" name="nom" value="<?= e($donnees['enseignant']['nom'] ?? '') ?>" required>

            <label for="prenom">Prénom</label>
            <input id="prenom" name="prenom" value="<?= e($donnees['enseignant']['prenom'] ?? '') ?>" required>

            <label for="email">Email</label>
            <input id="email" name="email" type="email" value="<?= e($donnees['enseignant']['email'] ?? '') ?>" required>

            <label for="date_naissance">Date de naissance</label>
            <input id="date_naissance" name="date_naissance" type="date" value="<?= e($donnees['enseignant']['date_naissance'] ?? '') ?>" required>

            <label for="matricule">Matricule</label>
            <input id="matricule" name="matricule" value="<?= e($donnees['enseignant']['matricule'] ?? '') ?>" required>

            <label for="date_embauche">Date d'embauche</label>
            <input id="date_embauche" name="date_embauche" type="date" value="<?= e($donnees['enseignant']['date_embauche'] ?? '') ?>" required>

            <label for="statut_enseignant">Statut</label>
            <select id="statut_enseignant" name="statut_enseignant" required>
                <?php foreach (['actif' => 'Actif', 'en_conge' => 'En congé', 'sorti' => 'Sorti'] as $valeur => $libelle): ?>
                    <option value="<?= e($valeur) ?>" <?= ($donnees['enseignant']['statut'] ?? 'actif') === $valeur ? 'selected' : '' ?>><?= e($libelle) ?></option>
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
