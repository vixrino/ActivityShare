<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu <?= sanitize($paiement['reference']) ?> - ActivityShare</title>
    <style>
        body { font-family: Arial, sans-serif; color: #1a1a2e; max-width: 720px; margin: 40px auto; padding: 32px; }
        .receipt-header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #4CAF50; padding-bottom: 20px; margin-bottom: 30px; }
        .receipt-header h1 { color: #4CAF50; margin: 0 0 8px; font-size: 28px; }
        .meta { color: #666; font-size: 14px; }
        .block { margin-bottom: 28px; }
        .block h2 { font-size: 15px; text-transform: uppercase; letter-spacing: 0.5px; color: #4CAF50; margin: 0 0 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #F5F5F5; text-align: left; padding: 10px; font-size: 13px; }
        td { padding: 10px; border-bottom: 1px solid #eee; font-size: 14px; }
        .total-row td { border: none; font-size: 16px; font-weight: bold; background: #4CAF50; color: white; }
        .footer { text-align: center; font-size: 12px; color: #999; margin-top: 40px; border-top: 1px solid #eee; padding-top: 16px; }
        .btn-print { background: #4CAF50; color: white; border: none; padding: 10px 18px; border-radius: 6px; cursor: pointer; font-size: 14px; }
        @media print { .no-print { display: none !important; } body { margin: 0; padding: 0; } }
    </style>
</head>
<body>
    <div class="receipt-header">
        <div>
            <h1>ActivityShare</h1>
            <p class="meta">Plateforme d'activités entre particuliers</p>
            <p class="meta">contact@activityshare.com</p>
        </div>
        <div style="text-align:right;">
            <div style="font-size:20px; font-weight:bold;">REÇU</div>
            <div class="meta">N° <?= sanitize($paiement['reference']) ?></div>
            <div class="meta"><?= formatDateShort($paiement['date_paiement']) ?></div>
        </div>
    </div>

    <div class="block">
        <h2>Client</h2>
        <strong><?= sanitize($client['prenom'] . ' ' . $client['nom']) ?></strong><br>
        <span class="meta"><?= sanitize($client['email']) ?></span>
    </div>

    <div class="block">
        <h2>Adresse de facturation</h2>
        <?= sanitize($paiement['adresse_facturation']) ?><br>
        <?= sanitize($paiement['code_postal']) ?> <?= sanitize($paiement['ville_facturation']) ?><br>
        <?= sanitize($paiement['pays']) ?>
    </div>

    <div class="block">
        <h2>Détail de la commande</h2>
        <table>
            <thead>
                <tr>
                    <th>Activité</th>
                    <th style="text-align:center;">Qté</th>
                    <th style="text-align:right;">Prix unitaire</th>
                    <th style="text-align:right;">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lignes as $ligne): ?>
                    <tr>
                        <td><?= sanitize($ligne['titre']) ?></td>
                        <td style="text-align:center;"><?= intval($ligne['quantite']) ?></td>
                        <td style="text-align:right;"><?= formatPrice($ligne['prix_unitaire']) ?></td>
                        <td style="text-align:right;"><?= formatPrice($ligne['prix_unitaire'] * $ligne['quantite']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td colspan="3" style="text-align:right;">TOTAL TTC</td>
                    <td style="text-align:right;"><?= formatPrice($paiement['montant_total']) ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="block">
        <h2>Mode de paiement</h2>
        Carte bancaire •••• <?= sanitize($paiement['derniers_chiffres']) ?> — Titulaire : <?= sanitize($paiement['titulaire_carte']) ?>
    </div>

    <div class="no-print" style="text-align:center; margin-top:24px;">
        <button class="btn-print" onclick="window.print()">Imprimer / Enregistrer en PDF</button>
    </div>

    <div class="footer">
        Ce reçu est généré automatiquement par ActivityShare — Projet ISEP G8A (Équipe Webkit).
    </div>
</body>
</html>
