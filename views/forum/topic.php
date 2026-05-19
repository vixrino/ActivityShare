<section class="section page-header-section">
    <div class="container">
        <nav class="breadcrumb" aria-label="Fil d'Ariane">
            <a href="index.php?page=forum">Forum</a> <span aria-hidden="true">›</span>
            <a href="index.php?page=forum-categorie&id=<?= intval($topic['categorie_id']) ?>"><?= sanitize($topic['categorie_nom']) ?></a> <span aria-hidden="true">›</span>
            <span><?= sanitize($topic['titre']) ?></span>
        </nav>
        <h1><?= sanitize($topic['titre']) ?></h1>
    </div>
</section>

<section class="section">
    <div class="container">
        <article class="forum-post forum-post-first">
            <header class="forum-post-head">
                <div class="topic-avatar">
                    <?php if ($topic['photo_profil']): ?>
                        <img src="<?= sanitize($topic['photo_profil']) ?>" alt="">
                    <?php else: ?>
                        <span class="avatar-placeholder"><?= strtoupper(substr($topic['prenom'], 0, 1) . substr($topic['nom'], 0, 1)) ?></span>
                    <?php endif; ?>
                </div>
                <div>
                    <strong><?= sanitize($topic['prenom'] . ' ' . $topic['nom']) ?></strong>
                    <span class="role-badge role-<?= sanitize($topic['role']) ?>" style="margin-left:8px;"><?= sanitize(ucfirst($topic['role'])) ?></span>
                    <small class="text-muted"> · <?= timeAgo($topic['date_creation']) ?></small>
                </div>
                <?php if (isAdmin()): ?>
                    <div style="margin-left:auto; display:flex; gap:6px;">
                        <a href="index.php?page=forum-epingler-sujet&id=<?= intval($topic['id']) ?>" class="btn btn-sm btn-outline" aria-label="Épingler ou désépingler">
                            <i class="fas fa-thumbtack" aria-hidden="true"></i>
                        </a>
                        <a href="index.php?page=forum-supprimer-sujet&id=<?= intval($topic['id']) ?>" class="btn btn-sm btn-danger"
                           onclick="return confirm('Supprimer ce sujet et ses réponses ?');" aria-label="Supprimer le sujet">
                            <i class="fas fa-trash" aria-hidden="true"></i>
                        </a>
                    </div>
                <?php endif; ?>
            </header>
            <div class="forum-post-body">
                <p><?= nl2br(sanitize($topic['contenu'])) ?></p>
            </div>
        </article>

        <?php foreach ($messages as $m): ?>
            <article class="forum-post">
                <header class="forum-post-head">
                    <div class="topic-avatar">
                        <?php if ($m['photo_profil']): ?>
                            <img src="<?= sanitize($m['photo_profil']) ?>" alt="">
                        <?php else: ?>
                            <span class="avatar-placeholder"><?= strtoupper(substr($m['prenom'], 0, 1) . substr($m['nom'], 0, 1)) ?></span>
                        <?php endif; ?>
                    </div>
                    <div>
                        <strong><?= sanitize($m['prenom'] . ' ' . $m['nom']) ?></strong>
                        <small class="text-muted"> · <?= timeAgo($m['date_envoi']) ?></small>
                    </div>
                </header>
                <div class="forum-post-body">
                    <p><?= nl2br(sanitize($m['contenu'])) ?></p>
                </div>
            </article>
        <?php endforeach; ?>

        <?php if (isLoggedIn() && !$topic['ferme']): ?>
            <form method="POST" action="index.php?page=forum-topic&id=<?= intval($topic['id']) ?>" class="forum-reply-form">
                <h2><i class="fas fa-reply" aria-hidden="true"></i> Répondre</h2>
                <div class="form-group">
                    <label for="reply" class="sr-only">Votre réponse</label>
                    <textarea id="reply" name="contenu" rows="4" maxlength="5000"
                              class="form-control" placeholder="Votre réponse…" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane" aria-hidden="true"></i> Publier
                </button>
            </form>
        <?php elseif ($topic['ferme']): ?>
            <div class="alert alert-warning">Ce sujet est fermé. Plus aucune réponse n'est acceptée.</div>
        <?php else: ?>
            <p class="text-center text-muted">
                <a href="index.php?page=connexion">Connectez-vous</a> pour participer à la discussion.
            </p>
        <?php endif; ?>
    </div>
</section>
