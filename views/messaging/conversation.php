<section class="section">
    <div class="container">
        <div class="messaging-layout">
            <aside class="messaging-sidebar" aria-label="Mes conversations">
                <div class="messaging-sidebar-head">
                    <h2><i class="fas fa-comments" aria-hidden="true"></i> Conversations</h2>
                    <a href="index.php?page=nouveau-message" class="btn btn-sm btn-outline" aria-label="Nouveau message">
                        <i class="fas fa-pen-to-square" aria-hidden="true"></i>
                    </a>
                </div>
                <ul class="conversation-list compact" role="list">
                    <?php foreach ($conversations as $c): ?>
                        <li>
                            <a href="index.php?page=conversation&user=<?= intval($c['id']) ?>"
                               class="conversation-item <?= $c['id'] == $autre['id'] ? 'active' : '' ?>">
                                <div class="conversation-avatar">
                                    <?php if ($c['photo_profil']): ?>
                                        <img src="<?= sanitize($c['photo_profil']) ?>" alt="">
                                    <?php else: ?>
                                        <span class="avatar-placeholder"><?= strtoupper(substr($c['prenom'], 0, 1) . substr($c['nom'], 0, 1)) ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="conversation-content">
                                    <strong><?= sanitize($c['prenom']) ?></strong>
                                    <small class="conversation-preview"><?= sanitize(substr($c['dernier_message'], 0, 40)) ?></small>
                                </div>
                                <?php if ($c['non_lus'] > 0 && $c['id'] != $autre['id']): ?>
                                    <span class="notif-badge"><?= intval($c['non_lus']) ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </aside>

            <section class="messaging-thread" aria-label="Conversation">
                <header class="thread-header">
                    <div class="thread-avatar">
                        <?php if ($autre['photo_profil']): ?>
                            <img src="<?= sanitize($autre['photo_profil']) ?>" alt="">
                        <?php else: ?>
                            <span class="avatar-placeholder"><?= strtoupper(substr($autre['prenom'], 0, 1) . substr($autre['nom'], 0, 1)) ?></span>
                        <?php endif; ?>
                    </div>
                    <div>
                        <h2><?= sanitize($autre['prenom'] . ' ' . $autre['nom']) ?></h2>
                        <p class="text-muted"><?= sanitize(ucfirst($autre['role'])) ?></p>
                    </div>
                </header>

                <div class="thread-messages" id="thread-messages" aria-live="polite">
                    <?php if (empty($messages)): ?>
                        <p class="text-center text-muted">Aucun message pour le moment. Lancez la conversation !</p>
                    <?php else: ?>
                        <?php foreach ($messages as $m): ?>
                            <article class="message-bubble <?= $m['expediteur_id'] == $_SESSION['user_id'] ? 'mine' : 'theirs' ?>">
                                <p><?= nl2br(sanitize($m['contenu'])) ?></p>
                                <small><?= timeAgo($m['date_envoi']) ?></small>
                            </article>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <form method="POST" action="index.php?page=conversation&user=<?= intval($autre['id']) ?>" class="thread-form">
                    <label for="message-input" class="sr-only">Votre message</label>
                    <textarea id="message-input" name="contenu" rows="2" maxlength="2000"
                              placeholder="Écrivez votre message…" class="form-control" required></textarea>
                    <button type="submit" class="btn btn-primary" aria-label="Envoyer">
                        <i class="fas fa-paper-plane" aria-hidden="true"></i>
                    </button>
                </form>
            </section>
        </div>
    </div>
</section>
