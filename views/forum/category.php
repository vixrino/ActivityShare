<section class="section page-header-section">
    <div class="container">
        <a href="index.php?page=forum" class="btn btn-outline btn-sm forum-back-btn">
            <i class="fas fa-arrow-left" aria-hidden="true"></i> Retour au forum
        </a>
        <div class="page-header-row">
            <div>
                <nav class="breadcrumb" aria-label="Fil d'Ariane">
                    <a href="index.php?page=forum">Forum</a> <span aria-hidden="true">›</span>
                    <span><?= sanitize($categorie['nom']) ?></span>
                </nav>
                <h1><i class="fas <?= sanitize($categorie['icone']) ?>" aria-hidden="true"></i> <?= sanitize($categorie['nom']) ?></h1>
                <p><?= sanitize($categorie['description']) ?></p>
            </div>
            <?php if (isLoggedIn()): ?>
                <a href="index.php?page=forum-nouveau-sujet&categorie=<?= intval($categorie['id']) ?>" class="btn btn-primary">
                    <i class="fas fa-plus" aria-hidden="true"></i> Nouveau sujet
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if (empty($topics)): ?>
            <div class="empty-state">
                <i class="fas fa-comment-slash" aria-hidden="true"></i>
                <h3>Aucun sujet</h3>
                <p>Soyez le premier à lancer une discussion.</p>
                <?php if (isLoggedIn()): ?>
                    <a href="index.php?page=forum-nouveau-sujet&categorie=<?= intval($categorie['id']) ?>" class="btn btn-primary">
                        Créer le premier sujet
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <ul class="topic-list" role="list">
                <?php foreach ($topics as $t): ?>
                    <li>
                        <a href="index.php?page=forum-topic&id=<?= intval($t['id']) ?>" class="topic-item">
                            <div class="topic-avatar">
                                <?php if ($t['photo_profil']): ?>
                                    <img src="<?= sanitize($t['photo_profil']) ?>" alt="">
                                <?php else: ?>
                                    <span class="avatar-placeholder"><?= strtoupper(substr($t['prenom'], 0, 1) . substr($t['nom'], 0, 1)) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="topic-body">
                                <h3>
                                    <?php if ($t['epingle']): ?>
                                        <span class="badge badge-category" style="font-size:10px;"><i class="fas fa-thumbtack" aria-hidden="true"></i> Épinglé</span>
                                    <?php endif; ?>
                                    <?php if ($t['ferme']): ?>
                                        <span class="badge badge-danger" style="font-size:10px;"><i class="fas fa-lock" aria-hidden="true"></i> Fermé</span>
                                    <?php endif; ?>
                                    <?= sanitize($t['titre']) ?>
                                </h3>
                                <p class="topic-meta">
                                    par <strong><?= sanitize($t['prenom'] . ' ' . $t['nom']) ?></strong>
                                    · <?= timeAgo($t['date_creation']) ?>
                                </p>
                            </div>
                            <div class="topic-stats">
                                <div><strong><?= intval($t['nb_reponses']) ?></strong><span>réponses</span></div>
                                <div><strong><?= intval($t['nb_vues']) ?></strong><span>vues</span></div>
                            </div>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</section>
