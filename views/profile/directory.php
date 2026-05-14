<section class="section page-header-section">
    <div class="container">
        <h1><i class="fas fa-users"></i> Membres ActivityShare</h1>
        <p class="page-header-subtitle">Découvrez les autres membres, suivez-les et échangez avec eux.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="back-link-wrapper">
            <a href="javascript:history.back()" class="forum-back-btn">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
        <form method="get" action="index.php" class="members-filter">
            <input type="hidden" name="page" value="membres">
            <div class="form-row">
                <input type="search" name="q" placeholder="Rechercher un nom, une ville..." value="<?= sanitize($search) ?>" class="form-control">
                <select name="role" class="form-control">
                    <option value="">Tous les rôles</option>
                    <option value="participant" <?= $role === 'participant' ? 'selected' : '' ?>>Participants</option>
                    <option value="organisateur" <?= $role === 'organisateur' ? 'selected' : '' ?>>Organisateurs</option>
                </select>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Filtrer
                </button>
            </div>
        </form>

        <?php if (empty($members)): ?>
            <div class="empty-state-sm">
                <i class="fas fa-user-slash"></i>
                <p>Aucun membre ne correspond à votre recherche.</p>
            </div>
        <?php else: ?>
            <p class="results-count"><?= count($members) ?> membre(s)</p>
            <div class="members-grid">
                <?php foreach ($members as $u): ?>
                    <div class="member-card">
                        <a href="index.php?page=utilisateur&id=<?= intval($u['id']) ?>" class="member-card-link">
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
                                <?php if (!empty($u['bio'])): ?>
                                    <p class="member-bio"><?= sanitize(mb_strimwidth($u['bio'], 0, 90, '…')) ?></p>
                                <?php endif; ?>
                            </div>
                        </a>
                        <?php if (isLoggedIn() && intval($_SESSION['user_id']) !== intval($u['id'])): ?>
                            <div class="member-actions">
                                <?php if (isset($followingMap[intval($u['id'])])): ?>
                                    <a href="index.php?page=ne-plus-suivre&id=<?= intval($u['id']) ?>" class="btn btn-sm btn-outline">
                                        <i class="fas fa-user-check"></i> Abonné
                                    </a>
                                <?php else: ?>
                                    <a href="index.php?page=suivre&id=<?= intval($u['id']) ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-user-plus"></i> Suivre
                                    </a>
                                <?php endif; ?>
                                <a href="index.php?page=conversation&user=<?= intval($u['id']) ?>" class="btn btn-sm btn-outline">
                                    <i class="fas fa-envelope"></i> Message
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
