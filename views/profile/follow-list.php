<section class="section page-header-section">
    <div class="container">
        <h1>
            <i class="fas <?= $mode === 'followers' ? 'fa-users' : 'fa-user-friends' ?>"></i>
            <?= $mode === 'followers' ? 'Abonnés' : 'Abonnements' ?> de
            <a href="index.php?page=utilisateur&id=<?= intval($profile['id']) ?>" class="text-light">
                <?= sanitize($profile['prenom'] . ' ' . $profile['nom']) ?>
            </a>
        </h1>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="back-link-wrapper">
            <a href="javascript:history.back()" class="forum-back-btn">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>

        <?php if (empty($users)): ?>
            <div class="empty-state-sm">
                <i class="fas fa-user-slash"></i>
                <p><?= $mode === 'followers' ? 'Aucun abonné pour le moment.' : 'Aucun abonnement pour le moment.' ?></p>
            </div>
        <?php else: ?>
            <div class="members-grid">
                <?php foreach ($users as $u): ?>
                    <a href="index.php?page=utilisateur&id=<?= intval($u['id']) ?>" class="member-card">
                        <div class="member-avatar">
                            <?php if (!empty($u['photo_profil'])): ?>
                                <img src="<?= sanitize($u['photo_profil']) ?>" alt="">
                            <?php else: ?>
                                <div class="avatar-placeholder">
                                    <?= strtoupper(substr($u['prenom'], 0, 1) . substr($u['nom'], 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="member-info">
                            <strong><?= sanitize($u['prenom'] . ' ' . $u['nom']) ?></strong>
                            <span class="role-badge role-<?= sanitize($u['role']) ?>"><?= ucfirst(sanitize($u['role'])) ?></span>
                            <?php if (!empty($u['ville'])): ?>
                                <small><i class="fas fa-map-marker-alt"></i> <?= sanitize($u['ville']) ?></small>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
