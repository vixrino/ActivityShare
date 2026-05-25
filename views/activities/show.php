<section class="section">
    <div class="container">
        <a href="javascript:history.back()" class="btn btn-outline btn-sm mb-1">
            <i class="fas fa-arrow-left"></i> Retour
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
                    <span class="badge badge-views"><i class="fas fa-eye"></i> <?= $nbVues ?> vues</span>
                </div>

                <h1><?= sanitize($activite['titre']) ?></h1>

                <?php if ($activityRating['total'] > 0): ?>
                    <div class="activity-rating-header">
                        <?= renderStars($activityRating['moyenne'], $activityRating['total']) ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($tags)): ?>
                    <div class="activity-tags">
                        <?php foreach ($tags as $t): ?>
                            <a href="index.php?page=tag&slug=<?= sanitize($t['slug']) ?>" class="tag-chip">#<?= sanitize($t['nom']) ?></a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

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
                        <i class="fas fa-user" aria-hidden="true"></i>
                        <div>
                            <strong>Organisateur</strong>
                            <span>
                                <a href="index.php?page=utilisateur&id=<?= intval($activite['organisateur_id']) ?>" class="user-link">
                                    <?= sanitize($activite['organisateur_prenom'] . ' ' . $activite['organisateur_nom']) ?>
                                </a>
                            </span>
                            <?php if ($organizerRating['total'] > 0): ?>
                                <small><?= renderStars($organizerRating['moyenne']) ?> (<?= $organizerRating['total'] ?> avis)</small>
                            <?php endif; ?>
                            <?php if (isLoggedIn() && $_SESSION['user_id'] != $activite['organisateur_id']): ?>
                                <small><a href="index.php?page=conversation&user=<?= intval($activite['organisateur_id']) ?>">
                                    <i class="fas fa-comment" aria-hidden="true"></i> Contacter
                                </a></small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-euro-sign" aria-hidden="true"></i>
                        <div>
                            <strong>Tarif</strong>
                            <span class="<?= floatval($activite['prix']) > 0 ? 'text-orange' : 'text-green' ?>">
                                <strong><?= formatPrice($activite['prix'] ?? 0) ?></strong>
                            </span>
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

                <!-- Notation : avis publiés -->
                <?php if (!empty($activityReviews)): ?>
                    <section class="reviews-section">
                        <h2><i class="fas fa-star"></i> Avis des participants (<?= $activityRating['total'] ?>)</h2>
                        <div class="reviews-list">
                            <?php foreach ($activityReviews as $review): ?>
                                <article class="review-card">
                                    <header class="review-header">
                                        <a href="index.php?page=utilisateur&id=<?= intval($review['utilisateur_id']) ?>" class="review-author user-link">
                                            <?php if (!empty($review['photo_profil'])): ?>
                                                <img src="<?= sanitize($review['photo_profil']) ?>" alt="" class="review-avatar">
                                            <?php else: ?>
                                                <span class="review-avatar review-avatar-default"><i class="fas fa-user"></i></span>
                                            <?php endif; ?>
                                            <span><?= sanitize($review['prenom'] . ' ' . $review['nom']) ?></span>
                                        </a>
                                        <?= renderStars($review['note']) ?>
                                        <time class="muted"><?= timeAgo($review['date_creation']) ?></time>
                                    </header>
                                    <?php if (!empty($review['commentaire'])): ?>
                                        <p><?= nl2br(sanitize($review['commentaire'])) ?></p>
                                    <?php endif; ?>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>

                <!-- Notation : formulaires (si éligible) -->
                <?php if ($canRateActivity): ?>
                    <section class="rating-block">
                        <h2><i class="fas fa-star"></i> Donnez votre avis sur l'activité</h2>
                        <form method="POST" action="index.php?page=noter-activite" class="rating-form">
                            <?= csrfField() ?>
                            <input type="hidden" name="activite_id" value="<?= intval($activite['id']) ?>">
                            <div class="rating-stars-input" role="radiogroup" aria-label="Note de 1 à 5">
                                <?php for ($n = 5; $n >= 1; $n--): ?>
                                    <input type="radio" id="act-star-<?= $n ?>" name="note" value="<?= $n ?>"
                                        <?= ($userActivityRating && intval($userActivityRating['note']) === $n) ? 'checked' : '' ?> required>
                                    <label for="act-star-<?= $n ?>" title="<?= $n ?> étoile(s)"><i class="fas fa-star"></i></label>
                                <?php endfor; ?>
                            </div>
                            <textarea name="commentaire" class="form-control" rows="3" maxlength="2000"
                                      placeholder="Votre retour (optionnel)…"><?= $userActivityRating ? sanitize($userActivityRating['commentaire']) : '' ?></textarea>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i>
                                <?= $userActivityRating ? 'Mettre à jour mon avis' : 'Publier mon avis' ?>
                            </button>
                        </form>
                    </section>

                    <?php if ($canRateOrganizer): ?>
                        <section class="rating-block">
                            <h2><i class="fas fa-user-tie"></i> Notez l'organisateur</h2>
                            <form method="POST" action="index.php?page=noter-organisateur" class="rating-form">
                                <?= csrfField() ?>
                                <input type="hidden" name="activite_id" value="<?= intval($activite['id']) ?>">
                                <input type="hidden" name="organisateur_id" value="<?= intval($activite['organisateur_id']) ?>">
                                <div class="rating-stars-input" role="radiogroup" aria-label="Note de 1 à 5 pour l'organisateur">
                                    <?php for ($n = 5; $n >= 1; $n--): ?>
                                        <input type="radio" id="orga-star-<?= $n ?>" name="note" value="<?= $n ?>"
                                            <?= ($userOrganizerRating && intval($userOrganizerRating['note']) === $n) ? 'checked' : '' ?> required>
                                        <label for="orga-star-<?= $n ?>" title="<?= $n ?> étoile(s)"><i class="fas fa-star"></i></label>
                                    <?php endfor; ?>
                                </div>
                                <textarea name="commentaire" class="form-control" rows="3" maxlength="2000"
                                          placeholder="Comment décririez-vous l'organisation ?"><?= $userOrganizerRating ? sanitize($userOrganizerRating['commentaire']) : '' ?></textarea>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i>
                                    <?= $userOrganizerRating ? 'Mettre à jour' : 'Publier' ?>
                                </button>
                            </form>
                        </section>
                    <?php endif; ?>
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
                            <a href="index.php?page=activite-stats&id=<?= $activite['id'] ?>" class="btn btn-outline btn-block">
                                <i class="fas fa-chart-line"></i> Statistiques
                            </a>
                            <a href="index.php?page=modifier-activite&id=<?= $activite['id'] ?>" class="btn btn-outline btn-block">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <a href="index.php?page=supprimer-activite&id=<?= $activite['id'] ?>" class="btn btn-danger btn-block"
                               onclick="return confirm('Êtes-vous sûr de vouloir annuler cette activité ?')">
                                <i class="fas fa-trash"></i> Annuler l'activité
                            </a>
                        </div>
                    <?php elseif ($isRegistered): ?>
                        <a href="index.php?page=chat-activite&id=<?= $activite['id'] ?>" class="btn btn-primary btn-block">
                            <i class="fas fa-comments" aria-hidden="true"></i> Accéder au chat
                        </a>
                        <a href="index.php?page=desinscription-activite&id=<?= $activite['id'] ?>" class="btn btn-outline btn-block"
                           onclick="return confirm('Voulez-vous vraiment vous désinscrire ?')">
                            <i class="fas fa-times" aria-hidden="true"></i> Se désinscrire
                        </a>
                        <p class="text-center text-success mt-1"><i class="fas fa-check-circle" aria-hidden="true"></i> Vous êtes inscrit</p>
                    <?php elseif ($isOnWaitingList): ?>
                        <a href="index.php?page=desinscription-activite&id=<?= $activite['id'] ?>" class="btn btn-outline btn-block"
                           onclick="return confirm('Voulez-vous quitter la liste d\'attente ?')">
                            <i class="fas fa-times" aria-hidden="true"></i> Quitter la liste d'attente
                        </a>
                        <p class="text-center mt-1"><i class="fas fa-hourglass-half" aria-hidden="true"></i> Position <?= $waitingPosition ?> en liste d'attente</p>
                    <?php elseif (floatval($activite['prix']) > 0): ?>
                        <a href="index.php?page=panier-ajouter&id=<?= $activite['id'] ?>" class="btn btn-primary btn-block">
                            <i class="fas fa-cart-plus" aria-hidden="true"></i> Ajouter au panier — <?= formatPrice($activite['prix']) ?>
                        </a>
                    <?php else: ?>
                        <a href="index.php?page=inscription-activite&id=<?= $activite['id'] ?>" class="btn btn-primary btn-block">
                            <?php if ($placesRestantes > 0): ?>
                                <i class="fas fa-check" aria-hidden="true"></i> S'inscrire (gratuit)
                            <?php else: ?>
                                <i class="fas fa-hourglass-half" aria-hidden="true"></i> Rejoindre la liste d'attente
                            <?php endif; ?>
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Partage : lien, QR code, export .ics -->
                <div class="sidebar-card share-card">
                    <h3><i class="fas fa-share-alt"></i> Partager</h3>
                    <div class="share-link-row">
                        <input type="text" id="shareUrl" class="form-control share-input" value="<?= sanitize($shareUrl) ?>" readonly>
                        <button type="button" class="btn btn-outline btn-sm" id="copyShareBtn" data-target="shareUrl">
                            <i class="fas fa-copy"></i> Copier
                        </button>
                    </div>
                    <div class="share-actions">
                        <a href="index.php?page=activite-ics&id=<?= intval($activite['id']) ?>" class="btn btn-outline btn-sm">
                            <i class="fas fa-calendar-plus"></i> Ajouter à mon calendrier (.ics)
                        </a>
                        <button type="button" class="btn btn-outline btn-sm" id="toggleQrBtn"
                                aria-expanded="false" aria-controls="qrPanel">
                            <i class="fas fa-qrcode"></i> Voir le QR Code
                        </button>
                    </div>
                    <div id="qrPanel" class="qr-panel" hidden>
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&margin=10&data=<?= urlencode($shareUrl) ?>"
                             alt="QR code de l'activité" class="qr-image">
                        <p class="muted">Scannez pour ouvrir l'activité sur mobile.</p>
                    </div>
                </div>

                <?php if (isLoggedIn() && ($isRegistered || $_SESSION['user_id'] == $activite['organisateur_id'] || isAdmin())): ?>
                    <div class="sidebar-card">
                        <h3><i class="fas fa-comments" aria-hidden="true"></i> Chat de l'activité</h3>
                        <p class="text-muted">Échangez avec l'organisateur et les autres participants.</p>
                        <a href="index.php?page=chat-activite&id=<?= $activite['id'] ?>" class="btn btn-outline btn-block">
                            <i class="fas fa-arrow-right" aria-hidden="true"></i> Ouvrir le chat
                        </a>
                    </div>
                <?php endif; ?>

                <?php if (isLoggedIn() && ($_SESSION['user_id'] == $activite['organisateur_id'] || isAdmin())): ?>
                    <div class="sidebar-card">
                        <h3>Participants (<?= $nbInscrits ?>)</h3>
                        <?php if (empty($inscrits)): ?>
                            <p class="text-muted">Aucun inscrit pour le moment.</p>
                        <?php else: ?>
                            <ul class="participants-list">
                                <?php foreach ($inscrits as $inscrit): ?>
                                    <li>
                                        <a href="index.php?page=utilisateur&id=<?= intval($inscrit['participant_id']) ?>" class="user-link">
                                            <i class="fas fa-user-circle"></i>
                                            <?= sanitize($inscrit['prenom'] . ' ' . $inscrit['nom']) ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>

                        <?php if (!empty($listeAttente)): ?>
                            <h4 class="mt-1">Liste d'attente (<?= count($listeAttente) ?>)</h4>
                            <ul class="participants-list waiting">
                                <?php foreach ($listeAttente as $attente): ?>
                                    <li>
                                        <a href="index.php?page=utilisateur&id=<?= intval($attente['participant_id']) ?>" class="user-link">
                                            <i class="fas fa-hourglass-half"></i>
                                            <?= sanitize($attente['prenom'] . ' ' . $attente['nom']) ?>
                                            <small>#<?= $attente['position'] ?></small>
                                        </a>
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
