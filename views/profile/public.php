<section class="section page-header-section">
    <div class="container">
        <h1><i class="fas fa-id-badge"></i> Profil de <?= sanitize($profile['prenom']) ?></h1>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="back-link-wrapper">
            <a href="javascript:history.back()" class="forum-back-btn">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
        <div class="profile-layout">
            <div class="profile-sidebar">
                <div class="profile-card">
                    <div class="profile-avatar">
                        <?php if ($profile['photo_profil']): ?>
                            <img src="<?= sanitize($profile['photo_profil']) ?>" alt="Photo de profil">
                        <?php else: ?>
                            <div class="avatar-placeholder">
                                <?= strtoupper(substr($profile['prenom'], 0, 1) . substr($profile['nom'], 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <h2><?= sanitize($profile['prenom'] . ' ' . $profile['nom']) ?></h2>
                    <span class="role-badge role-<?= sanitize($profile['role']) ?>">
                        <?= ucfirst(sanitize($profile['role'])) ?>
                    </span>
                    <p class="profile-since">Membre depuis <?= formatDateShort($profile['date_inscription']) ?></p>

                    <?php if ($noteOrganisateur['total'] > 0): ?>
                        <div class="profile-rating">
                            <?= renderStars($noteOrganisateur['moyenne'], $noteOrganisateur['total']) ?>
                        </div>
                    <?php endif; ?>

                    <div class="profile-stats">
                        <a href="index.php?page=abonnes&id=<?= intval($profile['id']) ?>" class="profile-stat">
                            <span class="profile-stat-value"><?= intval($nbFollowers) ?></span>
                            <span class="profile-stat-label">Abonnés</span>
                        </a>
                        <a href="index.php?page=abonnements&id=<?= intval($profile['id']) ?>" class="profile-stat">
                            <span class="profile-stat-value"><?= intval($nbFollowing) ?></span>
                            <span class="profile-stat-label">Abonnements</span>
                        </a>
                    </div>

                    <?php if (isLoggedIn()): ?>
                        <div class="profile-actions">
                            <?php if ($isFollowing): ?>
                                <a href="index.php?page=ne-plus-suivre&id=<?= intval($profile['id']) ?>" class="btn btn-outline btn-block">
                                    <i class="fas fa-user-check"></i> Abonné
                                </a>
                            <?php else: ?>
                                <a href="index.php?page=suivre&id=<?= intval($profile['id']) ?>" class="btn btn-primary btn-block">
                                    <i class="fas fa-user-plus"></i> Suivre
                                </a>
                            <?php endif; ?>
                            <a href="index.php?page=conversation&user=<?= intval($profile['id']) ?>" class="btn btn-outline btn-block">
                                <i class="fas fa-envelope"></i> Envoyer un message
                            </a>
                        </div>
                    <?php else: ?>
                        <a href="index.php?page=connexion" class="btn btn-primary btn-block">
                            <i class="fas fa-sign-in-alt"></i> Se connecter pour interagir
                        </a>
                    <?php endif; ?>

                    <div class="profile-info-list">
                        <?php if (!empty($profile['ville'])): ?>
                            <div class="profile-info-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?= sanitize($profile['ville']) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="profile-main">
                <?php if (!empty($profile['bio'])): ?>
                    <div class="content-card">
                        <h2><i class="fas fa-quote-left"></i> À propos</h2>
                        <p class="profile-bio-text"><?= nl2br(sanitize($profile['bio'])) ?></p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($activites)): ?>
                    <div class="content-card">
                        <h2><i class="fas fa-calendar-alt"></i> Activités proposées</h2>
                        <div class="activity-grid">
                            <?php foreach ($activites as $activite): ?>
                                <a href="index.php?page=activite&id=<?= intval($activite['id']) ?>" class="activity-card-mini">
                                    <?php if (!empty($activite['photo'])): ?>
                                        <div class="activity-card-mini-img" style="background-image:url('<?= sanitize($activite['photo']) ?>');"></div>
                                    <?php else: ?>
                                        <div class="activity-card-mini-img activity-card-mini-img-placeholder">
                                            <i class="fas <?= sanitize($activite['categorie_icone'] ?? 'fa-calendar') ?>"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="activity-card-mini-body">
                                        <h3><?= sanitize($activite['titre']) ?></h3>
                                        <p class="text-muted">
                                            <i class="fas fa-calendar"></i> <?= formatDateShort($activite['date_debut']) ?>
                                            <?php if (!empty($activite['lieu'])): ?>
                                                &middot; <i class="fas fa-map-marker-alt"></i> <?= sanitize($activite['lieu']) ?>
                                            <?php endif; ?>
                                        </p>
                                        <p class="activity-card-mini-meta">
                                            <span class="badge badge-category"><?= sanitize($activite['categorie_nom']) ?></span>
                                            <?php if ($activite['statut'] === 'annulee'): ?>
                                                <span class="badge badge-danger">Annulée</span>
                                            <?php elseif ($activite['statut'] === 'terminee'): ?>
                                                <span class="badge badge-muted">Terminée</span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($avisOrganisateur)): ?>
                    <div class="content-card">
                        <h2><i class="fas fa-star"></i> Avis reçus en tant qu'organisateur (<?= count($avisOrganisateur) ?>)</h2>
                        <ul class="reviews-list">
                            <?php foreach ($avisOrganisateur as $avis): ?>
                                <li class="review-item">
                                    <a href="index.php?page=utilisateur&id=<?= intval($avis['evaluateur_id']) ?>" class="review-avatar">
                                        <?php if (!empty($avis['photo_profil'])): ?>
                                            <img src="<?= sanitize($avis['photo_profil']) ?>" alt="">
                                        <?php else: ?>
                                            <span class="avatar-placeholder"><?= strtoupper(substr($avis['prenom'], 0, 1) . substr($avis['nom'], 0, 1)) ?></span>
                                        <?php endif; ?>
                                    </a>
                                    <div class="review-body">
                                        <div class="review-head">
                                            <a href="index.php?page=utilisateur&id=<?= intval($avis['evaluateur_id']) ?>" class="user-link">
                                                <strong><?= sanitize($avis['prenom'] . ' ' . $avis['nom']) ?></strong>
                                            </a>
                                            <?= renderStars(intval($avis['note'])) ?>
                                            <small class="text-muted"><?= timeAgo($avis['date_creation']) ?></small>
                                        </div>
                                        <small class="text-muted">Activité : <em><?= sanitize($avis['activite_titre']) ?></em></small>
                                        <?php if (!empty($avis['commentaire'])): ?>
                                            <p><?= nl2br(sanitize($avis['commentaire'])) ?></p>
                                        <?php endif; ?>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (empty($profile['bio']) && empty($activites) && empty($avisOrganisateur)): ?>
                    <div class="empty-state-sm">
                        <i class="fas fa-user"></i>
                        <p>Ce membre n'a pas encore complété son profil.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
