<section class="section">
    <div class="container">
        <a href="index.php?page=activites" class="btn btn-outline btn-sm mb-1">
            <i class="fas fa-arrow-left"></i> Retour aux activités
        </a>

        <div class="activity-detail">
            <div class="activity-detail-main">
                <div class="activity-detail-image">
                    <?php if ($activite['photo']): ?>
                        <img src="<?= sanitize($activite['photo']) ?>" alt="<?= sanitize($activite['titre']) ?>">
                    <?php else: ?>
                        <div class="activity-detail-placeholder">
                            <i class="fas <?= sanitize($activite['categorie_icone']) ?>"></i>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="activity-detail-badges">
                    <span class="badge badge-category">
                        <i class="fas <?= sanitize($activite['categorie_icone']) ?>"></i>
                        <?= sanitize($activite['categorie_nom']) ?>
                    </span>
                    <span class="badge badge-<?= $activite['type'] ?>">
                        <?= $activite['type'] === 'public' ? 'Public' : 'Privé' ?>
                    </span>
                    <?php if ($activite['statut'] === 'annulee'): ?>
                        <span class="badge badge-danger">Annulée</span>
                    <?php endif; ?>
                </div>

                <h1><?= sanitize($activite['titre']) ?></h1>

                <div class="activity-detail-info">
                    <div class="info-item">
                        <i class="fas fa-calendar"></i>
                        <div>
                            <strong>Date</strong>
                            <span><?= formatDateShort($activite['date_debut']) ?></span>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-clock"></i>
                        <div>
                            <strong>Horaires</strong>
                            <span><?= formatTime($activite['date_debut']) ?> - <?= formatTime($activite['date_fin']) ?></span>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <strong>Lieu</strong>
                            <span><?= sanitize($activite['lieu']) ?></span>
                            <?php if ($activite['adresse']): ?>
                                <small><?= sanitize($activite['adresse']) ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-user"></i>
                        <div>
                            <strong>Organisateur</strong>
                            <span><?= sanitize($activite['organisateur_prenom'] . ' ' . $activite['organisateur_nom']) ?></span>
                        </div>
                    </div>
                </div>

                <div class="activity-detail-description">
                    <h2>Description</h2>
                    <p><?= nl2br(sanitize($activite['description'])) ?></p>
                </div>

                <?php if ($activite['conditions_participation']): ?>
                    <div class="activity-detail-conditions">
                        <h2>Conditions de participation</h2>
                        <p><?= nl2br(sanitize($activite['conditions_participation'])) ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="activity-detail-sidebar">
                <div class="sidebar-card">
                    <h3>Inscription</h3>
                    <div class="places-counter">
                        <div class="places-bar">
                            <div class="places-fill" style="width: <?= min(100, ($nbInscrits / max(1, $activite['nb_max_participants'])) * 100) ?>%"></div>
                        </div>
                        <div class="places-text">
                            <span class="<?= $placesRestantes <= 0 ? 'text-danger' : 'text-green' ?>">
                                <?php if ($placesRestantes <= 0): ?>
                                    Complet
                                <?php else: ?>
                                    <?= $placesRestantes ?> place<?= $placesRestantes > 1 ? 's' : '' ?> restante<?= $placesRestantes > 1 ? 's' : '' ?>
                                <?php endif; ?>
                            </span>
                            <span><?= $nbInscrits ?>/<?= $activite['nb_max_participants'] ?> inscrits</span>
                        </div>
                    </div>

                    <?php if ($activite['statut'] === 'annulee'): ?>
                        <div class="alert alert-danger">Cette activité a été annulée.</div>
                    <?php elseif (!isLoggedIn()): ?>
                        <a href="index.php?page=connexion" class="btn btn-primary btn-block">
                            <i class="fas fa-sign-in-alt"></i> Connectez-vous pour vous inscrire
                        </a>
                    <?php elseif ($_SESSION['user_id'] == $activite['organisateur_id']): ?>
                        <div class="sidebar-actions">
                            <a href="index.php?page=modifier-activite&id=<?= $activite['id'] ?>" class="btn btn-outline btn-block">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <a href="index.php?page=supprimer-activite&id=<?= $activite['id'] ?>" class="btn btn-danger btn-block"
                               onclick="return confirm('Êtes-vous sûr de vouloir annuler cette activité ?')">
                                <i class="fas fa-trash"></i> Annuler l'activité
                            </a>
                        </div>
                    <?php elseif ($isRegistered): ?>
                        <a href="index.php?page=desinscription-activite&id=<?= $activite['id'] ?>" class="btn btn-danger btn-block"
                           onclick="return confirm('Voulez-vous vraiment vous désinscrire ?')">
                            <i class="fas fa-times"></i> Se désinscrire
                        </a>
                        <p class="text-center text-success mt-1"><i class="fas fa-check-circle"></i> Vous êtes inscrit</p>
                    <?php elseif ($isOnWaitingList): ?>
                        <a href="index.php?page=desinscription-activite&id=<?= $activite['id'] ?>" class="btn btn-outline btn-block"
                           onclick="return confirm('Voulez-vous quitter la liste d\'attente ?')">
                            <i class="fas fa-times"></i> Quitter la liste d'attente
                        </a>
                        <p class="text-center mt-1"><i class="fas fa-hourglass-half"></i> Position <?= $waitingPosition ?> en liste d'attente</p>
                    <?php else: ?>
                        <a href="index.php?page=inscription-activite&id=<?= $activite['id'] ?>" class="btn btn-primary btn-block">
                            <?php if ($placesRestantes > 0): ?>
                                <i class="fas fa-check"></i> S'inscrire
                            <?php else: ?>
                                <i class="fas fa-hourglass-half"></i> Rejoindre la liste d'attente
                            <?php endif; ?>
                        </a>
                    <?php endif; ?>
                </div>

                <?php if (isLoggedIn() && ($_SESSION['user_id'] == $activite['organisateur_id'] || isAdmin())): ?>
                    <div class="sidebar-card">
                        <h3>Participants (<?= $nbInscrits ?>)</h3>
                        <?php if (empty($inscrits)): ?>
                            <p class="text-muted">Aucun inscrit pour le moment.</p>
                        <?php else: ?>
                            <ul class="participants-list">
                                <?php foreach ($inscrits as $inscrit): ?>
                                    <li>
                                        <i class="fas fa-user-circle"></i>
                                        <?= sanitize($inscrit['prenom'] . ' ' . $inscrit['nom']) ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                        <?php if (!empty($listeAttente)): ?>
                            <h4 class="mt-1">Liste d'attente (<?= count($listeAttente) ?>)</h4>
                            <ul class="participants-list waiting">
                                <?php foreach ($listeAttente as $attente): ?>
                                    <li>
                                        <i class="fas fa-hourglass-half"></i>
                                        <?= sanitize($attente['prenom'] . ' ' . $attente['nom']) ?>
                                        <small>#<?= $attente['position'] ?></small>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
