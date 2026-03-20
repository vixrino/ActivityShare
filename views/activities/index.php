<section class="section page-header-section">
    <div class="container">
        <h1><i class="fas fa-calendar-alt"></i> Activités</h1>
        <p>Découvrez toutes les activités proposées par la communauté</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <form action="index.php" method="GET" class="filters-bar">
            <input type="hidden" name="page" value="activites">
            <div class="filters-row">
                <div class="form-group">
                    <input type="text" name="recherche" class="form-control" placeholder="Mot-clé..."
                           value="<?= sanitize($_GET['recherche'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <select name="categorie" class="form-control">
                        <option value="">Toutes les catégories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($_GET['categorie'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                <?= sanitize($cat['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <select name="type" class="form-control">
                        <option value="">Tous les types</option>
                        <option value="public" <?= ($_GET['type'] ?? '') === 'public' ? 'selected' : '' ?>>Public</option>
                        <option value="prive" <?= ($_GET['type'] ?? '') === 'prive' ? 'selected' : '' ?>>Privé</option>
                    </select>
                </div>
                <div class="form-group">
                    <input type="text" name="ville" class="form-control" placeholder="Ville..."
                           value="<?= sanitize($_GET['ville'] ?? '') ?>">
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filtrer</button>
                <a href="index.php?page=activites" class="btn btn-outline">Réinitialiser</a>
            </div>
        </form>

        <p class="results-count"><?= $total ?> activité<?= $total > 1 ? 's' : '' ?> trouvée<?= $total > 1 ? 's' : '' ?></p>

        <?php if (empty($activites)): ?>
            <div class="empty-state">
                <i class="fas fa-search"></i>
                <h3>Aucune activité trouvée</h3>
                <p>Essayez de modifier vos critères de recherche.</p>
            </div>
        <?php else: ?>
            <div class="activities-grid">
                <?php foreach ($activites as $activite): ?>
                    <a href="index.php?page=activite&id=<?= $activite['id'] ?>" class="activity-card">
                        <div class="activity-card-img">
                            <?php if ($activite['photo']): ?>
                                <img src="<?= sanitize($activite['photo']) ?>" alt="<?= sanitize($activite['titre']) ?>">
                            <?php else: ?>
                                <div class="activity-card-placeholder">
                                    <i class="fas <?= sanitize($activite['categorie_icone']) ?>"></i>
                                </div>
                            <?php endif; ?>
                            <span class="activity-card-badge"><?= sanitize($activite['categorie_nom']) ?></span>
                        </div>
                        <div class="activity-card-body">
                            <h3><?= sanitize($activite['titre']) ?></h3>
                            <div class="activity-card-meta">
                                <span><i class="fas fa-calendar"></i> <?= formatDateShort($activite['date_debut']) ?></span>
                                <span><i class="fas fa-clock"></i> <?= formatTime($activite['date_debut']) ?></span>
                            </div>
                            <div class="activity-card-meta">
                                <span><i class="fas fa-map-marker-alt"></i> <?= sanitize($activite['lieu']) ?></span>
                            </div>
                            <div class="activity-card-footer">
                                <span class="activity-card-places <?= ($activite['nb_max_participants'] - $activite['nb_inscrits']) <= 0 ? 'full' : '' ?>">
                                    <i class="fas fa-users"></i>
                                    <?= $activite['nb_inscrits'] ?>/<?= $activite['nb_max_participants'] ?> places
                                </span>
                                <span class="activity-card-type <?= $activite['type'] ?>">
                                    <?= $activite['type'] === 'public' ? 'Public' : 'Privé' ?>
                                </span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="index.php?page=activites&p=<?= $i ?>&<?= http_build_query(array_filter([
                            'recherche' => $_GET['recherche'] ?? '',
                            'categorie' => $_GET['categorie'] ?? '',
                            'type' => $_GET['type'] ?? '',
                            'ville' => $_GET['ville'] ?? '',
                        ])) ?>" class="pagination-link <?= $i == ($page ?? 1) ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>
