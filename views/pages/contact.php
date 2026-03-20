<section class="section page-header-section">
    <div class="container">
        <h1><i class="fas fa-envelope"></i> Contact</h1>
        <p>Une question ? N'hésitez pas à nous écrire</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="contact-layout">
            <div class="contact-form-container">
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        Votre message a été envoyé avec succès. Nous vous répondrons dans les meilleurs délais.
                    </div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?= sanitize($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="index.php?page=contact">
                    <div class="form-row-2">
                        <div class="form-group">
                            <label for="nom">Nom *</label>
                            <input type="text" id="nom" name="nom" class="form-control"
                                   value="<?= sanitize($_POST['nom'] ?? ($_SESSION['user_nom'] ?? '')) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">E-mail *</label>
                            <input type="email" id="email" name="email" class="form-control"
                                   value="<?= sanitize($_POST['email'] ?? ($_SESSION['user_email'] ?? '')) ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="sujet">Sujet *</label>
                        <input type="text" id="sujet" name="sujet" class="form-control"
                               value="<?= sanitize($_POST['sujet'] ?? '') ?>" required placeholder="L'objet de votre message">
                    </div>

                    <div class="form-group">
                        <label for="message">Message *</label>
                        <textarea id="message" name="message" class="form-control" rows="6"
                                  required placeholder="Votre message..."><?= sanitize($_POST['message'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Envoyer le message
                    </button>
                </form>
            </div>

            <div class="contact-info">
                <div class="contact-info-card">
                    <h3>Nos coordonnées</h3>
                    <div class="contact-info-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <strong>Email</strong>
                            <p>contact@activityshare.com</p>
                        </div>
                    </div>
                    <div class="contact-info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <strong>Adresse</strong>
                            <p>Université de Bordeaux<br>351 Cours de la Libération<br>33400 Talence, France</p>
                        </div>
                    </div>
                    <div class="contact-info-item">
                        <i class="fas fa-clock"></i>
                        <div>
                            <strong>Disponibilité</strong>
                            <p>Lundi - Vendredi : 9h - 18h</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
