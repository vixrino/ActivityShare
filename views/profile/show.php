<section class="section page-header-section">
    <div class="container">
        <h1><i class="fas fa-user"></i> Mon Profil</h1>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="profile-layout">
            <div class="profile-sidebar">
                <div class="profile-card">
                    <div class="profile-avatar">
                        <?php if ($user['photo_profil']): ?>
                            <img src="<?= sanitize($user['photo_profil']) ?>" alt="Photo de profil">
                        <?php else: ?>
                            <div class="avatar-placeholder">
                                <?= strtoupper(substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <h2><?= sanitize($user['prenom'] . ' ' . $user['nom']) ?></h2>
                    <span class="role-badge role-<?= $user['role'] ?>">
                        <?= ucfirst($user['role']) ?>
                    </span>
                    <p class="profile-since">Membre depuis <?= formatDateShort($user['date_inscription']) ?></p>

                    <div class="profile-info-list">
                        <div class="profile-info-item">
                            <i class="fas fa-envelope"></i>
                            <span><?= sanitize($user['email']) ?></span>
                        </div>
                        <?php if ($user['telephone']): ?>
                            <div class="profile-info-item">
                                <i class="fas fa-phone"></i>
                                <span><?= sanitize($user['telephone']) ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($user['ville']): ?>
                            <div class="profile-info-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?= sanitize($user['ville']) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($user['bio']): ?>
                        <div class="profile-bio">
                            <p><?= nl2br(sanitize($user['bio'])) ?></p>
                        </div>
                    <?php endif; ?>

                    <a href="index.php?page=modifier-profil" class="btn btn-outline btn-block">
                        <i class="fas fa-edit"></i> Modifier mon profil
                    </a>
                </div>

                <div class="profile-nav">
                    <a href="index.php?page=mes-inscriptions" class="profile-nav-link">
                        <i class="fas fa-calendar-check"></i> Mes inscriptions
                    </a>
                    <?php if (isOrganisateur()): ?>
                        <a href="index.php?page=mes-activites" class="profile-nav-link">
                            <i class="fas fa-list"></i> Mes activités
                        </a>
                        <a href="index.php?page=creer-activite" class="profile-nav-link">
                            <i class="fas fa-plus"></i> Créer une activité
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="profile-main">
                <div class="notifications-section">
                    <div class="section-title-bar">
                        <h2><i class="fas fa-bell"></i> Notifications</h2>
                        <?php if ($unreadCount > 0): ?>
                            <span class="notif-badge"><?= $unreadCount ?></span>
                            <a href="index.php?page=profil&mark_read=1" class="btn btn-sm btn-outline">Tout marquer comme lu</a>
                        <?php endif; ?>
                    </div>

                    <?php if (empty($notifications)): ?>
                        <div class="empty-state-sm">
                            <i class="fas fa-bell-slash"></i>
                            <p>Aucune notification</p>
                        </div>
                    <?php else: ?>
                        <div class="notifications-list">
                            <?php foreach ($notifications as $notif): ?>
                                <div class="notification-item <?= !$notif['lue'] ? 'unread' : '' ?>">
                                    <div class="notification-icon">
                                        <?php
                                        $iconMap = [
                                            'confirmation_inscription' => 'fa-check-circle text-green',
                                            'rappel' => 'fa-clock text-orange',
                                            'annulation' => 'fa-times-circle text-danger',
                                            'place_disponible' => 'fa-gift text-green',
                                        ];
                                        $icon = $iconMap[$notif['type']] ?? 'fa-bell';
                                        ?>
                                        <i class="fas <?= $icon ?>"></i>
                                    </div>
                                    <div class="notification-content">
                                        <strong><?= sanitize($notif['titre']) ?></strong>
                                        <p><?= sanitize($notif['message']) ?></p>
                                        <small><?= timeAgo($notif['date_creation']) ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
