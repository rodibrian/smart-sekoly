<?php
/**
 * Template : Gestion des types de frais paramétrables
 * View: templates/finance/types-frais.view.php
 */
?>
<div class="container mt-4">
    <h1>Paramétrage des types de frais</h1>

    <?php if (!empty($donnees['succes'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($donnees['succes']) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($donnees['erreur'])): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($donnees['erreur']) ?>
        </div>
    <?php endif; ?>

    <!-- Form de création -->
    <div class="card mb-4">
        <div class="card-header">
            <h3>Créer un nouveau type de frais</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="/smart-sekoly/finance/types-frais?action=creer_type">
                <div class="form-group mb-3">
                    <label for="libelle">Libellé</label>
                    <input type="text" name="libelle" id="libelle" class="form-control" placeholder="Ex: Frais de scolarité" required>
                </div>

                <div class="form-group mb-3">
                    <label for="montant_defaut">Montant par défaut (CFA)</label>
                    <input type="number" name="montant_defaut" id="montant_defaut" step="0.01" min="0" class="form-control" placeholder="Ex: 50000" required>
                </div>

                <button type="submit" class="btn btn-primary">Créer</button>
            </form>
        </div>
    </div>

    <!-- Liste des types -->
    <div class="card">
        <div class="card-header">
            <h3>Types de frais existants</h3>
        </div>
        <div class="card-body">
            <?php if (empty($donnees['types_frais'])): ?>
                <p class="text-muted">Aucun type de frais enregistré pour l'instant.</p>
            <?php else: ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Libellé</th>
                            <th>Montant par défaut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($donnees['types_frais'] as $type): ?>
                            <tr>
                                <td><?= (int) $type['id_type_frais'] ?></td>
                                <td><?= htmlspecialchars($type['libelle']) ?></td>
                                <td><?= number_format((float) $type['montant_defaut'], 2, ',', ' ') ?> CFA</td>
                                <td>
                                    <a href="/smart-sekoly/finance/types-frais?action=editer&id=<?= $type['id_type_frais'] ?>" class="btn btn-sm btn-warning">Modifier</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>
