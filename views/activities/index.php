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
                    <?php
                    $recherche = '';
                    if (isset($_GET['recherche'])) {
                        $recherche = sanitize($_GET['recherche']);
                    }
                    ?>
                    <input type="text" name="recherche" class="form-control" placeholder="Mot-clé..."
                           value="<?= $recherche ?>">
                </div>
                <div class="form-group">
                    <select name="categorie" class="form-control">
                        <option value="">Toutes les catégories</option>
                        <?php foreach ($categories as $cat): ?>
                            <?php
                            $selected = '';
                            if (isset($_GET['categorie']) && $_GET['categorie'] == $cat['id']) {
                                $selected = 'selected';
                            }
                            ?>
                            <option value="<?= $cat['id'] ?>" <?= $selected ?>>
                                <?= sanitize($cat['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <select name="type" class="form-control">
                        <option value="">Tous les types</option>
                        <option value="public" <?php if (isset($_GET['type']) && $_GET['type'] === 'public') echo 'selected'; ?>>Public</option>
                        <option value="prive" <?php if (isset($_GET['type']) && $_GET['type'] === 'prive') echo 'selected'; ?>>Privé</option>
                    </select>
                </div>
                <div class="form-group">
                    <?php
                    $ville = '';
                    if (isset($_GET['ville'])) {
                        $ville = sanitize($_GET['ville']);
                    }
                    ?>
                    <input type="text" name="ville" class="form-control" placeholder="Ville..."
                           value="<?= $ville ?>">
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filtrer</button>
                <a href="index.php?page=activites" class="btn btn-outline">Réinitialiser</a>
            </div>
        </form>

        <p class="results-count">
            <?= $total ?> activité<?php if ($total > 1) echo 's'; ?> trouvée<?php if ($total > 1) echo 's'; ?>
        </p>

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
                                <?php
                                $placesRestantes = $activite['nb_max_participants'] - $activite['nb_inscrits'];
                                $classePlaces = '';
                                if ($placesRestantes <= 0) {
                                    $classePlaces = 'full';
                                }
                                ?>
                                <span class="activity-card-places <?= $classePlaces ?>">
                                    <i class="fas fa-users"></i>
                                    <?= $activite['nb_inscrits'] ?>/<?= $activite['nb_max_participants'] ?> places
                                </span>
                                <span class="activity-card-type <?= $activite['type'] ?>">
                                    <?php if ($activite['type'] === 'public'): ?>
                                        Public
                                    <?php else: ?>
                                        Privé
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php
                        $lien = 'index.php?page=activites&p=' . $i;
                        if (isset($_GET['recherche']) && $_GET['recherche'] !== '') {
                            $lien = $lien . '&recherche=' . urlencode($_GET['recherche']);
                        }
                        if (isset($_GET['categorie']) && $_GET['categorie'] !== '') {
                            $lien = $lien . '&categorie=' . urlencode($_GET['categorie']);
                        }
                        if (isset($_GET['type']) && $_GET['type'] !== '') {
                            $lien = $lien . '&type=' . urlencode($_GET['type']);
                        }
                        if (isset($_GET['ville']) && $_GET['ville'] !== '') {
                            $lien = $lien . '&ville=' . urlencode($_GET['ville']);
                        }

                        $classeActive = 'pagination-link';
                        if ($i == $page) {
                            $classeActive = 'pagination-link active';
                        }
                        ?>
                        <a href="<?= $lien ?>" class="<?= $classeActive ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>
