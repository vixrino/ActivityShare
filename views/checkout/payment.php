<section class="section page-header-section">
    <div class="container">
        <h1><i class="fas fa-lock" aria-hidden="true"></i> Paiement sécurisé</h1>
        <p>Saisissez vos informations de paiement</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= sanitize($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="checkout-layout">
            <form method="POST" action="index.php?page=paiement" class="checkout-form" autocomplete="on">
                <div class="checkout-stepper" aria-hidden="true">
                    <span class="step done"><span>1</span> Panier</span>
                    <span class="step current"><span>2</span> Paiement</span>
                    <span class="step"><span>3</span> Confirmation</span>
                </div>

                <fieldset class="form-section">
                    <legend><i class="fas fa-credit-card" aria-hidden="true"></i> Carte bancaire</legend>

                    <div class="payment-methods" role="radiogroup" aria-label="Choisir une méthode de paiement">
                        <label class="payment-method active">
                            <input type="radio" name="methode_carte" value="visa" checked>
                            <span><i class="fab fa-cc-visa" aria-hidden="true"></i> Visa</span>
                        </label>
                        <label class="payment-method">
                            <input type="radio" name="methode_carte" value="mastercard">
                            <span><i class="fab fa-cc-mastercard" aria-hidden="true"></i> Mastercard</span>
                        </label>
                        <label class="payment-method">
                            <input type="radio" name="methode_carte" value="amex">
                            <span><i class="fab fa-cc-amex" aria-hidden="true"></i> American Express</span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label for="titulaire">Titulaire de la carte *</label>
                        <input type="text" id="titulaire" name="titulaire" class="form-control"
                               required minlength="3" maxlength="100"
                               value="<?= sanitize($_POST['titulaire'] ?? ($_SESSION['user_prenom'] . ' ' . $_SESSION['user_nom'])) ?>"
                               autocomplete="cc-name">
                    </div>

                    <div class="form-group">
                        <label for="numero_carte">Numéro de carte *</label>
                        <input type="text" id="numero_carte" name="numero_carte" class="form-control"
                               required inputmode="numeric" autocomplete="cc-number"
                               placeholder="4242 4242 4242 4242"
                               data-card-input maxlength="19"
                               value="<?= sanitize($_POST['numero_carte'] ?? '') ?>">
                        <small class="form-help">Carte de test : <code>4242 4242 4242 4242</code></small>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label for="expiration">Expiration *</label>
                            <input type="text" id="expiration" name="expiration" class="form-control"
                                   required placeholder="MM/AA" maxlength="5"
                                   inputmode="numeric" autocomplete="cc-exp"
                                   data-exp-input
                                   value="<?= sanitize($_POST['expiration'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="cvv">Cryptogramme (CVV) *</label>
                            <input type="password" id="cvv" name="cvv" class="form-control"
                                   required inputmode="numeric" autocomplete="cc-csc"
                                   pattern="\d{3,4}" maxlength="4" placeholder="123">
                        </div>
                    </div>
                </fieldset>

                <fieldset class="form-section">
                    <legend><i class="fas fa-map-marker-alt" aria-hidden="true"></i> Adresse de facturation</legend>

                    <div class="form-group">
                        <label for="adresse">Adresse *</label>
                        <input type="text" id="adresse" name="adresse" class="form-control" required
                               autocomplete="billing street-address"
                               value="<?= sanitize($_POST['adresse'] ?? '') ?>">
                    </div>
                    <div class="form-row-2">
                        <div class="form-group">
                            <label for="code_postal">Code postal *</label>
                            <input type="text" id="code_postal" name="code_postal" class="form-control" required
                                   autocomplete="billing postal-code"
                                   value="<?= sanitize($_POST['code_postal'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="ville_facturation">Ville *</label>
                            <input type="text" id="ville_facturation" name="ville_facturation" class="form-control" required
                                   autocomplete="billing address-level2"
                                   value="<?= sanitize($_POST['ville_facturation'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="pays">Pays *</label>
                        <select id="pays" name="pays" class="form-control" required autocomplete="billing country-name">
                            <?php $pays = ['France', 'Belgique', 'Suisse', 'Luxembourg', 'Canada']; ?>
                            <?php foreach ($pays as $p): ?>
                                <option value="<?= $p ?>" <?= ($_POST['pays'] ?? 'France') === $p ? 'selected' : '' ?>><?= $p ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </fieldset>

                <div class="checkout-actions">
                    <a href="index.php?page=panier" class="btn btn-outline">
                        <i class="fas fa-arrow-left" aria-hidden="true"></i> Retour au panier
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-lock" aria-hidden="true"></i> Payer <?= formatPrice($total) ?>
                    </button>
                </div>

                <p class="checkout-secure-note">
                    <i class="fas fa-shield-alt" aria-hidden="true"></i>
                    Démonstration : aucune transaction bancaire réelle. Vos informations ne sont pas enregistrées et n'ont aucun effet.
                </p>
            </form>

            <aside class="checkout-summary" aria-label="Détail de la commande">
                <h2>Votre commande</h2>
                <ul class="checkout-summary-items">
                    <?php foreach ($items as $item): ?>
                        <li>
                            <span><?= sanitize($item['titre']) ?></span>
                            <strong><?= formatPrice($item['prix']) ?></strong>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <hr>
                <div class="cart-summary-row cart-summary-total">
                    <span>Total</span>
                    <strong><?= formatPrice($total) ?></strong>
                </div>
                <div class="checkout-trust">
                    <span><i class="fas fa-shield-alt" aria-hidden="true"></i> SSL 256 bits</span>
                    <span><i class="fas fa-undo" aria-hidden="true"></i> Annulation jusqu'à J-7</span>
                    <span><i class="fas fa-headset" aria-hidden="true"></i> Support 7j/7</span>
                </div>
            </aside>
        </div>
    </div>
</section>
