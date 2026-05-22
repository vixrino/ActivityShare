<section class="section">
    <div class="container">
        <div class="back-link-wrapper">
            <a href="index.php?page=activites" class="forum-back-btn"><i class="fas fa-arrow-left"></i> Toutes les activités</a>
        </div>

        <header class="page-header">
            <h1><i class="fas fa-hashtag"></i> Tag : <?= sanitize($tag['nom']) ?></h1>
            <p class="muted"><?= count($activites) ?> activité<?= count($activites) > 1 ? 's' : '' ?> avec ce tag.</p>
        </header>

        <?php if (empty($activites)): ?>
            <div class="empty-state">
                <i class="fas fa-search"></i>
                <p>Aucune activité ne correspond à ce tag pour le moment.</p>
            </div>
        <?php else: ?>
            <div class="activity-grid">
                <?php foreach ($activites as $a): ?>
                    <article class="activity-card-mini">
                        <a href="index.php?page=activite&id=<?= intval($a['id']) ?>" class="activity-card-link">
                            <?php if (!empty($a['photo'])): ?>
                                <div class="activity-card-photo">
                                    <img src="<?= sanitize($a['photo']) ?>" alt="">
                                </div>
                            <?php else: ?>
                                <div class="activity-card-photo placeholder">
                                    <i class="fas <?= sanitize($a['categorie_icone'] ?: 'fa-calendar') ?>"></i>
                                </div>
                            <?php endif; ?>
                            <div class="activity-card-body">
                                <span class="badge badge-category">
                                    <i class="fas <?= sanitize($a['categorie_icone']) ?>"></i>
                                    <?= sanitize($a['categorie_nom']) ?>
                                </span>
                                <h3><?= sanitize($a['titre']) ?></h3>
                                <p class="muted">
                                    <i class="fas fa-calendar"></i> <?= formatDateShort($a['date_debut']) ?>
                                    • <i class="fas fa-map-marker-alt"></i> <?= sanitize($a['lieu']) ?>
                                </p>
                                <p class="muted">
                                    <i class="fas fa-users"></i> <?= intval($a['nb_inscrits']) ?>/<?= intval($a['nb_max_participants']) ?> inscrits
                                </p>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
