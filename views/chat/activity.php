<section class="section">
    <div class="container">
        <a href="index.php?page=activite&id=<?= intval($activite['id']) ?>" class="btn btn-outline btn-sm mb-1">
            <i class="fas fa-arrow-left" aria-hidden="true"></i> Retour à l'activité
        </a>

        <div class="chat-layout">
            <section class="chat-main" aria-label="Discussion de l'activité">
                <header class="chat-header">
                    <div>
                        <h1><i class="fas <?= sanitize($activite['categorie_icone']) ?>" aria-hidden="true"></i> Chat — <?= sanitize($activite['titre']) ?></h1>
                        <p class="text-muted">
                            <i class="fas fa-calendar" aria-hidden="true"></i> <?= formatDateShort($activite['date_debut']) ?>
                            · <i class="fas fa-map-marker-alt" aria-hidden="true"></i> <?= sanitize($activite['lieu']) ?>
                        </p>
                    </div>
                </header>

                <div class="chat-messages" id="chat-messages" aria-live="polite">
                    <?php if (empty($messages)): ?>
                        <p class="text-center text-muted">Aucun message pour le moment. Soyez le premier à écrire !</p>
                    <?php else: ?>
                        <?php foreach ($messages as $m): ?>
                            <article class="message-bubble <?= $m['utilisateur_id'] == $_SESSION['user_id'] ? 'mine' : 'theirs' ?>">
                                <div class="bubble-author">
                                    <strong><?= sanitize($m['prenom']) ?></strong>
                                    <?php if ($m['utilisateur_id'] == $activite['organisateur_id']): ?>
                                        <span class="badge badge-public" style="font-size:10px;">Organisateur</span>
                                    <?php endif; ?>
                                </div>
                                <p><?= nl2br(sanitize($m['contenu'])) ?></p>
                                <small><?= timeAgo($m['date_envoi']) ?></small>
                            </article>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <form method="POST" action="index.php?page=chat-activite&id=<?= intval($activite['id']) ?>" class="thread-form">
                    <label for="chat-input" class="sr-only">Votre message dans le chat</label>
                    <textarea id="chat-input" name="contenu" rows="2" maxlength="2000"
                              placeholder="Partagez une info ou posez une question…" class="form-control" required></textarea>
                    <button type="submit" class="btn btn-primary" aria-label="Envoyer le message">
                        <i class="fas fa-paper-plane" aria-hidden="true"></i>
                    </button>
                </form>
            </section>

            <aside class="chat-aside" aria-label="Participants">
                <h2><i class="fas fa-users" aria-hidden="true"></i> Participants (<?= count($inscrits) ?>)</h2>
                <ul class="participants-list">
                    <li>
                        <i class="fas fa-crown text-orange" aria-hidden="true"></i>
                        <?= sanitize($activite['organisateur_prenom'] . ' ' . $activite['organisateur_nom']) ?>
                        <small class="text-muted">Organisateur</small>
                    </li>
                    <?php foreach ($inscrits as $inscrit): ?>
                        <?php if ($inscrit['participant_id'] == $activite['organisateur_id']) continue; ?>
                        <li>
                            <i class="fas fa-user-circle" aria-hidden="true"></i>
                            <?= sanitize($inscrit['prenom'] . ' ' . $inscrit['nom']) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </aside>
        </div>
    </div>
</section>
