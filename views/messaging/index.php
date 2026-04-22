<section class="section page-header-section">
    <div class="container">
        <div class="page-header-row">
            <div>
                <h1><i class="fas fa-comments" aria-hidden="true"></i> Messagerie</h1>
                <p>Échangez avec les membres de la communauté</p>
            </div>
            <a href="index.php?page=nouveau-message" class="btn btn-primary">
                <i class="fas fa-pen-to-square" aria-hidden="true"></i> Nouveau message
            </a>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if (empty($conversations)): ?>
            <div class="empty-state">
                <i class="fas fa-comment-dots" aria-hidden="true"></i>
                <h3>Aucune conversation</h3>
                <p>Commencez une discussion avec un autre membre.</p>
                <a href="index.php?page=nouveau-message" class="btn btn-primary">
                    <i class="fas fa-plus" aria-hidden="true"></i> Démarrer
                </a>
            </div>
        <?php else: ?>
            <ul class="conversation-list" role="list">
                <?php foreach ($conversations as $c): ?>
                    <li>
                        <a href="index.php?page=conversation&user=<?= intval($c['id']) ?>" class="conversation-item">
                            <div class="conversation-avatar">
                                <?php if ($c['photo_profil']): ?>
                                    <img src="<?= sanitize($c['photo_profil']) ?>" alt="">
                                <?php else: ?>
                                    <span class="avatar-placeholder"><?= strtoupper(substr($c['prenom'], 0, 1) . substr($c['nom'], 0, 1)) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="conversation-content">
                                <div class="conversation-head">
                                    <strong><?= sanitize($c['prenom'] . ' ' . $c['nom']) ?></strong>
                                    <small><?= timeAgo($c['derniere_date']) ?></small>
                                </div>
                                <p class="conversation-preview"><?= sanitize(substr($c['dernier_message'], 0, 100)) ?><?= strlen($c['dernier_message']) > 100 ? '…' : '' ?></p>
                            </div>
                            <?php if ($c['non_lus'] > 0): ?>
                                <span class="notif-badge" aria-label="<?= intval($c['non_lus']) ?> nouveaux messages"><?= intval($c['non_lus']) ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</section>
