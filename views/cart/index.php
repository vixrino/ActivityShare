<section class="section page-header-section">
    <div class="container">
        <h1><i class="fas fa-shopping-cart" aria-hidden="true"></i> Mon panier</h1>
        <p>Finalisez votre inscription aux activités payantes</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if (empty($items)): ?>
            <div class="empty-state">
                <i class="fas fa-shopping-basket" aria-hidden="true"></i>
                <h3>Votre panier est vide</h3>
                <p>Découvrez les activités proposées par la communauté.</p>
                <a href="index.php?page=activites" class="btn btn-primary">
                    <i class="fas fa-search" aria-hidden="true"></i> Explorer les activités
                </a>
            </div>
        <?php else: ?>
            <div class="cart-layout">
                <div class="cart-items">
                    <form method="POST" action="index.php?page=panier-modifier">
                        <?php foreach ($items as $item):
                            $places = intval($item['nb_max_participants']) - intval($item['nb_inscrits']); ?>
                            <article class="cart-item">
                                <div class="cart-item-img">
                                    <?php if ($item['photo']): ?>
                                        <img src="<?= sanitize($item['photo']) ?>" alt="">
                                    <?php else: ?>
                                        <div class="cart-item-placeholder">
                                            <i class="fas <?= sanitize($item['categorie_icone']) ?>" aria-hidden="true"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="cart-item-body">
                                    <a href="index.php?page=activite&id=<?= $item['activite_id'] ?>" class="cart-item-title">
                                        <?= sanitize($item['titre']) ?>
                                    </a>
                                    <p class="cart-item-meta">
                                        <i class="fas fa-calendar" aria-hidden="true"></i> <?= formatDateShort($item['date_debut']) ?>
                                        <i class="fas fa-map-marker-alt" aria-hidden="true"></i> <?= sanitize($item['lieu']) ?>
                                    </p>
                                    <p class="cart-item-meta">
                                        <span class="cart-item-places <?= $places <= 0 ? 'full' : '' ?>">
                                            <i class="fas fa-users" aria-hidden="true"></i>
                                            <?= $places > 0 ? $places . ' places restantes' : 'Liste d\'attente' ?>
                                        </span>
                                    </p>
                                </div>
                                <div class="cart-item-price">
                                    <span class="cart-item-amount"><?= formatPrice($item['prix']) ?></span>
                                </div>
                                <div class="cart-item-actions">
                                    <a href="index.php?page=panier-retirer&id=<?= $item['activite_id'] ?>"
                                       class="cart-item-remove"
                                       aria-label="Retirer <?= sanitize($item['titre']) ?> du panier"
                                       onclick="return confirm('Retirer cette activité du panier ?');">
                                        <i class="fas fa-trash" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </form>
                </div>

                <aside class="cart-summary" aria-label="Récapitulatif">
                    <h2>Récapitulatif</h2>
                    <div class="cart-summary-row">
                        <span>Sous-total</span>
                        <strong><?= formatPrice($total) ?></strong>
                    </div>
                    <div class="cart-summary-row">
                        <span>Frais de service</span>
                        <strong>Offerts</strong>
                    </div>
                    <hr>
                    <div class="cart-summary-row cart-summary-total">
                        <span>Total à payer</span>
                        <strong><?= formatPrice($total) ?></strong>
                    </div>
                    <a href="index.php?page=paiement" class="btn btn-primary btn-block btn-lg">
                        <i class="fas fa-lock" aria-hidden="true"></i> Procéder au paiement
                    </a>
                    <p class="cart-summary-secure">
                        <i class="fas fa-shield-alt" aria-hidden="true"></i>
                        Paiement 100 % sécurisé
                    </p>
                </aside>
            </div>
        <?php endif; ?>
    </div>
</section>
