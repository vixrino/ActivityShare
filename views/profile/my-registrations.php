<section class="section page-header-section">
    <div class="container">
        <h1><i class="fas fa-calendar-check"></i> Mes inscriptions</h1>
        <p>Activités auxquelles vous êtes inscrit</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if (empty($inscriptions)): ?>
            <div class="empty-state">
                <i class="fas fa-calendar-times"></i>
                <h3>Aucune inscription</h3>
                <p>Vous n'êtes inscrit à aucune activité pour le moment.</p>
                <a href="index.php?page=activites" class="btn btn-primary">Découvrir les activités</a>
            </div>
        <?php else: ?>
            <div class="activities-grid">
                <?php foreach ($inscriptions as $inscription): ?>
                    <a href="index.php?page=activite&id=<?= $inscription['activite_id'] ?>" class="activity-card">
                        <div class="activity-card-img">
                            <?php if ($inscription['photo']): ?>
                                <img src="<?= sanitize($inscription['photo']) ?>" alt="<?= sanitize($inscription['titre']) ?>">
                            <?php else: ?>
                                <div class="activity-card-placeholder">
                                    <i class="fas <?= sanitize($inscription['categorie_icone']) ?>"></i>
                                </div>
                            <?php endif; ?>
                            <span class="activity-card-badge"><?= sanitize($inscription['categorie_nom']) ?></span>
                            <?php if ($inscription['activite_statut'] === 'annulee'): ?>
                                <span class="activity-card-badge badge-danger">Annulée</span>
                            <?php endif; ?>
                        </div>
                        <div class="activity-card-body">
                            <h3><?= sanitize($inscription['titre']) ?></h3>
                            <div class="activity-card-meta">
                                <span><i class="fas fa-calendar"></i> <?= formatDateShort($inscription['date_debut']) ?></span>
                                <span><i class="fas fa-clock"></i> <?= formatTime($inscription['date_debut']) ?></span>
                            </div>
                            <div class="activity-card-meta">
                                <span><i class="fas fa-map-marker-alt"></i> <?= sanitize($inscription['lieu']) ?></span>
                            </div>
                            <div class="activity-card-footer">
                                <span class="text-green"><i class="fas fa-check-circle"></i> Inscrit</span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
