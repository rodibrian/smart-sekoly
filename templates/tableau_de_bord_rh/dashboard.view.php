<?php
/**
 * Vue du tableau de bord RH.
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Tableau de bord RH</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        .page { max-width: 1080px; margin: 32px auto; padding: 0 20px; }
        .cartes { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px; }
        .carte { padding: 20px; background: #fff; border-radius: 14px; box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08); }
        .carte h2 { margin: 0 0 10px; font-size: 1rem; text-transform: uppercase; letter-spacing: .08em; color: #475569; }
        .carte p { margin: 0; font-size: 2.2rem; font-weight: 700; }
        section { margin-top: 28px; }
        table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 14px; overflow: hidden; }
        th, td { padding: 14px 16px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        th { background: #f8fafc; font-weight: 700; }
        thead tr { border-bottom: 2px solid #cbd5e1; }
        .statut-en attente, .statut-en_attente { color: #b45309; font-weight: 700; }
        .statut-validé, .statut-valide { color: #166534; font-weight: 700; }
        .statut-refusé { color: #7c2d12; font-weight: 700; }
    </style>
</head>
<body>
    <div class="page">
        <h1>Tableau de bord RH</h1>
        <div class="cartes">
            <div class="carte">
                <h2>Enseignants actifs</h2>
                <p><?= e($donnees['indicateurs']['enseignants_actifs']) ?></p>
            </div>
            <div class="carte">
                <h2>Contrats actifs</h2>
                <p><?= e($donnees['indicateurs']['contrats_actifs']) ?></p>
            </div>
            <div class="carte">
                <h2>Congés en attente</h2>
                <p><?= e($donnees['indicateurs']['conges_en_attente']) ?></p>
            </div>
            <div class="carte">
                <h2>Heures supp. en attente</h2>
                <p><?= e($donnees['indicateurs']['heures_en_attente']) ?></p>
            </div>
        </div>

        <section>
            <h2>Contrats récents</h2>
            <table>
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($donnees['contrats'] as $contrat): ?>
                        <tr>
                            <td><?= e(ucfirst($contrat['type'])) ?></td>
                            <td><?= e(ucfirst(str_replace('_', ' ', $contrat['statut']))) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section>
            <h2>Congés en attente</h2>
            <table>
                <thead>
                    <tr>
                        <th>Enseignant</th>
                        <th>Type</th>
                        <th>Période</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($donnees['conges'] as $conge): ?>
                        <tr>
                            <td><?= e($conge['enseignant']) ?></td>
                            <td><?= e(ucfirst($conge['type'])) ?></td>
                            <td><?= e($conge['periode']) ?></td>
                            <td class="statut-<?= e(str_replace(' ', '-', $conge['statut'])) ?>"><?= e(ucfirst($conge['statut'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section>
            <h2>Heures supplémentaires</h2>
            <table>
                <thead>
                    <tr>
                        <th>Enseignant</th>
                        <th>Heures</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($donnees['heures'] as $heure): ?>
                        <tr>
                            <td><?= e($heure['enseignant']) ?></td>
                            <td><?= e($heure['heures']) ?></td>
                            <td class="statut-<?= e(str_replace(' ', '-', $heure['statut'])) ?>"><?= e(ucfirst($heure['statut'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </div>
</body>
</html>
