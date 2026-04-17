<section class="section page-header-section">
    <div class="container">
        <h1><i class="fas fa-receipt" aria-hidden="true"></i> Mes paiements</h1>
        <p>Historique de vos transactions</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if (empty($paiements)): ?>
            <div class="empty-state">
                <i class="fas fa-receipt" aria-hidden="true"></i>
                <h3>Aucun paiement</h3>
                <p>Vous n'avez pas encore réglé d'activité.</p>
                <a href="index.php?page=activites" class="btn btn-primary">Découvrir des activités</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Date</th>
                            <th>Articles</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($paiements as $p): ?>
                            <tr>
                                <td><strong><?= sanitize($p['reference']) ?></strong></td>
                                <td><?= formatDateShort($p['date_paiement']) ?></td>
                                <td>
                                    <?php foreach (($details[$p['id']] ?? []) as $l): ?>
                                        <div class="payment-line"><?= sanitize($l['titre']) ?></div>
                                    <?php endforeach; ?>
                                </td>
                                <td><strong><?= formatPrice($p['montant_total']) ?></strong></td>
                                <td>
                                    <span class="status-badge status-active"><?= ucfirst($p['statut']) ?></span>
                                </td>
                                <td>
                                    <a href="index.php?page=recu-paiement&ref=<?= urlencode($p['reference']) ?>"
                                       target="_blank" rel="noopener"
                                       class="btn btn-sm btn-outline" aria-label="Voir le reçu <?= sanitize($p['reference']) ?>">
                                        <i class="fas fa-file-invoice" aria-hidden="true"></i> Reçu
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
