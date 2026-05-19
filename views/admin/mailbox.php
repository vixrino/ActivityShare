<section class="section page-header-section">
    <div class="container">
        <h1><i class="fas fa-inbox" aria-hidden="true"></i> Boîte mail (démo)</h1>
        <p>Affiche les liens de réinitialisation de mot de passe — utile pour la démonstration.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php include __DIR__ . '/../layout/admin-nav.php'; ?>

        <div class="alert alert-info" role="status">
            <i class="fas fa-info-circle" aria-hidden="true"></i>
            En production, ces liens sont envoyés par e-mail au client. Pour la démo, ils sont consultables ici.
        </div>

        <?php if (empty($resets)): ?>
            <div class="empty-state">
                <i class="fas fa-envelope-open" aria-hidden="true"></i>
                <h3>Boîte vide</h3>
                <p>Aucune demande de réinitialisation pour le moment.</p>
            </div>
        <?php else: ?>
            <div class="mailbox-list">
                <?php foreach ($resets as $r):
                    $expired = strtotime($r['date_expiration']) < time();
                    $used = (bool) $r['utilise'];
                    $resetUrl = 'index.php?page=reinitialiser-mot-de-passe&token=' . $r['token'];
                ?>
                    <article class="mailbox-item <?= ($expired || $used) ? 'is-used' : '' ?>">
                        <div class="mailbox-meta">
                            <strong><?= sanitize($r['prenom'] . ' ' . $r['nom']) ?></strong>
                            <small><?= sanitize($r['email']) ?></small>
                            <small>Envoyé <?= timeAgo($r['date_creation']) ?></small>
                            <?php if ($used): ?>
                                <span class="status-badge status-terminee">Utilisé</span>
                            <?php elseif ($expired): ?>
                                <span class="status-badge status-annulee">Expiré</span>
                            <?php else: ?>
                                <span class="status-badge status-active">Actif</span>
                            <?php endif; ?>
                        </div>
                        <div class="mailbox-body">
                            <p>Bonjour <strong><?= sanitize($r['prenom']) ?></strong>, voici votre lien de réinitialisation :</p>
                            <a class="mailbox-link" href="<?= $resetUrl ?>"><?= sanitize($resetUrl) ?></a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
