<section class="section page-header-section">
    <div class="container">
        <h1><i class="fas fa-envelope"></i> Messages de contact</h1>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="admin-nav">
            <a href="index.php?page=admin" class="admin-nav-link"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a>
            <a href="index.php?page=admin-utilisateurs" class="admin-nav-link"><i class="fas fa-users"></i> Utilisateurs</a>
            <a href="index.php?page=admin-activites" class="admin-nav-link"><i class="fas fa-calendar"></i> Activités</a>
            <a href="index.php?page=admin-faq" class="admin-nav-link"><i class="fas fa-question-circle"></i> FAQ</a>
            <a href="index.php?page=admin-messages" class="admin-nav-link active"><i class="fas fa-envelope"></i> Messages</a>
        </div>

        <?php if (empty($messages)): ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>Aucun message</h3>
            </div>
        <?php else: ?>
            <div class="messages-list">
                <?php foreach ($messages as $msg): ?>
                    <div class="message-card <?= !$msg['lu'] ? 'unread' : '' ?>">
                        <div class="message-header">
                            <div>
                                <strong><?= sanitize($msg['nom']) ?></strong>
                                <span class="text-muted">&lt;<?= sanitize($msg['email']) ?>&gt;</span>
                            </div>
                            <div>
                                <small><?= timeAgo($msg['date_envoi']) ?></small>
                                <?php if (!$msg['lu']): ?>
                                    <a href="index.php?page=admin-messages&mark_read=<?= $msg['id'] ?>" class="btn btn-sm btn-outline">
                                        Marquer comme lu
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="message-subject"><strong>Sujet :</strong> <?= sanitize($msg['sujet']) ?></div>
                        <div class="message-body"><?= nl2br(sanitize($msg['message'])) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
