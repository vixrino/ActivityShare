<section class="section page-header-section">
    <div class="container">
        <h1><i class="fas fa-pen-to-square" aria-hidden="true"></i> Nouveau message</h1>
        <p>Recherchez un membre pour démarrer une discussion</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="form-container">
            <form action="index.php" method="GET" class="search-bar">
                <input type="hidden" name="page" value="nouveau-message">
                <div class="search-input-group">
                    <i class="fas fa-search" aria-hidden="true"></i>
                    <label for="q" class="sr-only">Rechercher un utilisateur</label>
                    <input type="search" id="q" name="q" class="search-input"
                           placeholder="Nom, prénom ou e-mail…"
                           value="<?= sanitize($recherche) ?>"
                           minlength="2" autofocus>
                </div>
                <button class="btn btn-primary">Rechercher</button>
            </form>

            <div class="mt-2">
                <?php if (!empty($recherche) && strlen($recherche) < 2): ?>
                    <p class="text-muted">Saisissez au moins 2 caractères.</p>
                <?php elseif (!empty($recherche) && empty($resultats)): ?>
                    <div class="empty-state-sm">
                        <i class="fas fa-user-slash" aria-hidden="true"></i>
                        <p>Aucun membre trouvé pour « <?= sanitize($recherche) ?> ».</p>
                    </div>
                <?php elseif (!empty($resultats)): ?>
                    <ul class="conversation-list" role="list">
                        <?php foreach ($resultats as $u): ?>
                            <?php if ($u['id'] == $_SESSION['user_id']) continue; ?>
                            <li>
                                <a href="index.php?page=conversation&user=<?= intval($u['id']) ?>" class="conversation-item">
                                    <div class="conversation-avatar">
                                        <?php if ($u['photo_profil']): ?>
                                            <img src="<?= sanitize($u['photo_profil']) ?>" alt="">
                                        <?php else: ?>
                                            <span class="avatar-placeholder"><?= strtoupper(substr($u['prenom'], 0, 1) . substr($u['nom'], 0, 1)) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="conversation-content">
                                        <strong><?= sanitize($u['prenom'] . ' ' . $u['nom']) ?></strong>
                                        <small><?= sanitize($u['email']) ?> · <?= sanitize(ucfirst($u['role'])) ?></small>
                                    </div>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
