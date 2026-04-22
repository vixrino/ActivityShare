<section class="section page-header-section">
    <div class="container">
        <div class="page-header-row">
            <div>
                <h1><i class="fas fa-comments" aria-hidden="true"></i> Forum de la communauté</h1>
                <p>Échangez, partagez, posez vos questions</p>
            </div>
            <?php if (isLoggedIn()): ?>
                <a href="index.php?page=forum-nouveau-sujet" class="btn btn-primary">
                    <i class="fas fa-plus" aria-hidden="true"></i> Nouveau sujet
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="forum-categories">
            <?php foreach ($categories as $cat): ?>
                <a href="index.php?page=forum-categorie&id=<?= intval($cat['id']) ?>" class="forum-category-card">
                    <div class="forum-category-icon">
                        <i class="fas <?= sanitize($cat['icone']) ?>" aria-hidden="true"></i>
                    </div>
                    <div class="forum-category-body">
                        <h3><?= sanitize($cat['nom']) ?></h3>
                        <p><?= sanitize($cat['description']) ?></p>
                    </div>
                    <div class="forum-category-stats">
                        <div><strong><?= intval($cat['nb_topics']) ?></strong><span>sujets</span></div>
                        <div><strong><?= intval($cat['nb_messages']) ?></strong><span>réponses</span></div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
