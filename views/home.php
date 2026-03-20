<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1>Découvrez & partagez des <span class="text-green">activités</span> près de chez vous</h1>
            <p class="hero-subtitle">ActivityShare connecte les passionnés d'activités locales. Sport, cuisine, randonnée, lecture... Proposez ou rejoignez des expériences uniques dans votre ville.</p>
            <div class="hero-actions">
                <a href="index.php?page=activites" class="btn btn-primary btn-lg">
                    <i class="fas fa-search"></i> Explorer les activités
                </a>
                <?php if (!isLoggedIn()): ?>
                    <a href="index.php?page=inscription" class="btn btn-outline-white btn-lg">
                        <i class="fas fa-user-plus"></i> Rejoindre la communauté
                    </a>
                <?php elseif (isOrganisateur()): ?>
                    <a href="index.php?page=creer-activite" class="btn btn-outline-white btn-lg">
                        <i class="fas fa-plus"></i> Créer une activité
                    </a>
                <?php endif; ?>
            </div>
            <div class="hero-stats">
                <div class="hero-stat">
                    <span class="hero-stat-number"><?= $totalActivites ?></span>
                    <span class="hero-stat-label">Activités</span>
                </div>
                <div class="hero-stat">
                    <span class="hero-stat-number"><?= $totalUtilisateurs ?></span>
                    <span class="hero-stat-label">Membres</span>
                </div>
                <div class="hero-stat">
                    <span class="hero-stat-number"><?= count($categories) ?></span>
                    <span class="hero-stat-label">Catégories</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section search-section">
    <div class="container">
        <form action="index.php" method="GET" class="search-bar">
            <input type="hidden" name="page" value="activites">
            <div class="search-input-group">
                <i class="fas fa-search"></i>
                <input type="text" name="recherche" placeholder="Rechercher une activité..." class="search-input">
            </div>
            <select name="categorie" class="search-select">
                <option value="">Toutes les catégories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>"><?= sanitize($cat['nom']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary">Rechercher</button>
        </form>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>Catégories populaires</h2>
            <p>Explorez les activités par thème</p>
        </div>
        <div class="categories-grid">
            <?php foreach ($categories as $cat): ?>
                <a href="index.php?page=activites&categorie=<?= $cat['id'] ?>" class="category-card">
                    <div class="category-icon">
                        <i class="fas <?= sanitize($cat['icone']) ?>"></i>
                    </div>
                    <span><?= sanitize($cat['nom']) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section-gray">
    <div class="container">
        <div class="section-header">
            <h2>Activités récentes</h2>
            <p>Découvrez les dernières activités proposées par la communauté</p>
        </div>

        <?php if (empty($activitesRecentes)): ?>
            <div class="empty-state">
                <i class="fas fa-calendar-plus"></i>
                <h3>Aucune activité pour le moment</h3>
                <p>Soyez le premier à proposer une activité !</p>
                <?php if (isOrganisateur()): ?>
                    <a href="index.php?page=creer-activite" class="btn btn-primary">Créer une activité</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="activities-grid">
                <?php foreach ($activitesRecentes as $activite): ?>
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
            <div class="text-center mt-2">
                <a href="index.php?page=activites" class="btn btn-outline">Voir toutes les activités <i class="fas fa-arrow-right"></i></a>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>Comment ça marche ?</h2>
            <p>Rejoindre ActivityShare, c'est simple !</p>
        </div>
        <div class="steps-grid">
            <div class="step-card">
                <div class="step-number">1</div>
                <div class="step-icon"><i class="fas fa-user-plus"></i></div>
                <h3>Créez votre compte</h3>
                <p>Inscrivez-vous gratuitement en tant que participant ou organisateur.</p>
            </div>
            <div class="step-card">
                <div class="step-number">2</div>
                <div class="step-icon"><i class="fas fa-search"></i></div>
                <h3>Explorez les activités</h3>
                <p>Parcourez les activités disponibles près de chez vous par catégorie.</p>
            </div>
            <div class="step-card">
                <div class="step-number">3</div>
                <div class="step-icon"><i class="fas fa-handshake"></i></div>
                <h3>Participez !</h3>
                <p>Inscrivez-vous et vivez des expériences enrichissantes avec d'autres passionnés.</p>
            </div>
        </div>
    </div>
</section>
