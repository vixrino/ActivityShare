<section class="section page-header-section">
    <div class="container">
        <h1><i class="fas fa-receipt" aria-hidden="true"></i> Paiements</h1>
        <p>Historique complet des paiements de la plateforme</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php include __DIR__ . '/../layout/admin-nav.php'; ?>

        <?php if (empty($paiements)): ?>
            <div class="empty-state">
                <i class="fas fa-receipt" aria-hidden="true"></i>
                <h3>Aucun paiement</h3>
                <p>Aucune transaction n'a encore été enregistrée.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Client</th>
                            <th>Date</th>
                            <th>Montant</th>
                            <th>Carte</th>
                            <th>Statut</th>
                            <th>Reçu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($paiements as $p): ?>
                            <tr>
                                <td><strong><?= sanitize($p['reference']) ?></strong></td>
                                <td><?= sanitize($p['prenom'] . ' ' . $p['nom']) ?><br><small class="text-muted"><?= sanitize($p['email']) ?></small></td>
                                <td><?= formatDateShort($p['date_paiement']) ?></td>
                                <td><strong><?= formatPrice($p['montant_total']) ?></strong></td>
                                <td>•••• <?= sanitize($p['derniers_chiffres']) ?></td>
                                <td><span class="status-badge status-active"><?= ucfirst($p['statut']) ?></span></td>
                                <td>
                                    <a href="index.php?page=recu-paiement&ref=<?= urlencode($p['reference']) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline">
                                        <i class="fas fa-file-invoice" aria-hidden="true"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>
