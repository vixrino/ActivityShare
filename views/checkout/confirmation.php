<section class="section">
    <div class="container">
        <div class="checkout-confirmation">
            <div class="confirmation-icon" aria-hidden="true">
                <i class="fas fa-check"></i>
            </div>
            <h1>Paiement confirmé !</h1>
            <p class="lead">Merci <?= sanitize($_SESSION['user_prenom']) ?>, votre inscription est validée.</p>

            <div class="confirmation-card">
                <div class="confirmation-row">
                    <span>Référence</span>
                    <strong><?= sanitize($paiement['reference']) ?></strong>
                </div>
                <div class="confirmation-row">
                    <span>Date</span>
                    <strong><?= formatDate($paiement['date_paiement']) ?></strong>
                </div>
                <div class="confirmation-row">
                    <span>Méthode de paiement</span>
                    <strong>Carte •••• <?= sanitize($paiement['derniers_chiffres']) ?></strong>
                </div>
                <div class="confirmation-row">
                    <span>Titulaire</span>
                    <strong><?= sanitize($paiement['titulaire_carte']) ?></strong>
                </div>
                <hr>
                <h2>Détail</h2>
                <ul class="checkout-summary-items">
                    <?php foreach ($lignes as $ligne): ?>
                        <li>
                            <span>
                                <?= sanitize($ligne['titre']) ?>
                                <small>×<?= intval($ligne['quantite']) ?></small>
                            </span>
                            <strong><?= formatPrice($ligne['prix_unitaire'] * $ligne['quantite']) ?></strong>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <hr>
                <div class="confirmation-row confirmation-total">
                    <span>Total réglé</span>
                    <strong><?= formatPrice($paiement['montant_total']) ?></strong>
                </div>
            </div>

            <div class="confirmation-actions">
                <a href="index.php?page=recu-paiement&ref=<?= urlencode($paiement['reference']) ?>" class="btn btn-outline" target="_blank" rel="noopener">
                    <i class="fas fa-file-invoice" aria-hidden="true"></i> Télécharger le reçu
                </a>
                <a href="index.php?page=mes-inscriptions" class="btn btn-primary">
                    <i class="fas fa-calendar-check" aria-hidden="true"></i> Voir mes inscriptions
                </a>
            </div>
        </div>
    </div>
</section>
