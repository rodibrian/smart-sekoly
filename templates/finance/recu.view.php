<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= e(BASE_URL . '/assets/css/responsive.css') ?>">
    <title>Reçu de Paiement - Finance</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Courier New', Courier, monospace; background: #f5f5f5; }
        .container { max-width: 400px; margin: 20px auto; padding: 0; }
        .receipt { background: white; padding: 20px; border: 1px solid #ddd; }
        .header { text-align: center; border-bottom: 1px dashed #ddd; padding-bottom: 10px; margin-bottom: 10px; }
        .header h1 { font-size: 16px; }
        .section { margin-bottom: 10px; }
        .line { display: flex; justify-content: space-between; }
        .footer { border-top: 1px dashed #ddd; text-align: center; padding-top: 10px; margin-top: 10px; }
        .btn-container { text-align: center; margin-top: 20px; }
        .btn { padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; margin: 5px; display: inline-block; }
        .btn-print { background: #6c757d; }
        @media print {
            .btn-container { display: none; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="receipt">
            <div class="header">
                <h1>REÇU DE PAIEMENT</h1>
            </div>

            <div class="section">
                <div class="line">
                    <span>Réf. Paiement:</span>
                    <strong><?php echo htmlspecialchars($donnees['paiement']['id_paiement']); ?></strong>
                </div>
                <div class="line">
                    <span>Numéro reçu:</span>
                    <strong><?php echo htmlspecialchars($donnees['paiement']['numero_recu']); ?></strong>
                </div>
                <div class="line">
                    <span>Facture:</span>
                    <strong><?php echo htmlspecialchars($donnees['paiement']['id_facture']); ?></strong>
                </div>
                <div class="line">
                    <span>Échéance:</span>
                    <strong><?php echo htmlspecialchars($donnees['paiement']['id_echeance']); ?></strong>
                </div>
            </div>

            <div class="section">
                <div class="line">
                    <span>Montant Payé:</span>
                    <strong><?php echo number_format($donnees['paiement']['montant_paye'], 0, ',', ' '); ?> FCFA</strong>
                </div>
                <div class="line">
                    <span>Méthode:</span>
                    <span><?php echo htmlspecialchars($donnees['paiement']['methode_paiement']); ?></span>
                </div>
                <div class="line">
                    <span>Date:</span>
                    <span><?php echo htmlspecialchars($donnees['paiement']['date_paiement']); ?></span>
                </div>
            </div>

            <?php if (!empty($donnees['paiement']['reference'])): ?>
                <div class="section">
                    <div class="line">
                        <span>Référence:</span>
                        <span><?php echo htmlspecialchars($donnees['paiement']['reference']); ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <div class="footer">
                <p style="font-size: 12px;">Merci pour votre paiement</p>
                <p style="font-size: 10px;">Smart-Sekoly © 2026</p>
            </div>
        </div>

        <div class="btn-container">
            <button class="btn btn-print" onclick="window.print()">🖨️ Imprimer</button>
            <a href="?module=finance&action=index" class="btn">← Retour</a>
        </div>
    </div>
</body>
</html>
