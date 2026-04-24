<section class="section">
    <div class="container">
        <div class="auth-container">
            <div class="auth-card">
                <div class="auth-header">
                    <h1>Mot de passe oublié</h1>
                    <p>Entrez votre adresse e-mail pour recevoir un lien de réinitialisation</p>
                </div>

                <?php if ($success): ?>
                    <?php if (empty($demoLink)): ?>
                        <div class="alert alert-success" role="status">
                            <i class="fas fa-check-circle" aria-hidden="true"></i>
                            Si un compte existe avec cette adresse e-mail, un lien de réinitialisation vient de vous être envoyé. Pensez à vérifier vos courriers indésirables.
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info" role="status">
                            <strong><i class="fas fa-flask" aria-hidden="true"></i> Mode démonstration</strong>
                            <p>L'envoi d'e-mail n'est pas configuré sur ce serveur. Voici directement votre lien de réinitialisation (valable 1h) :</p>
                            <a href="<?= sanitize($demoLink) ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-key" aria-hidden="true"></i> Réinitialiser mon mot de passe
                            </a>
                            <p class="form-help" style="margin-top:10px;">Ou copiez ce lien : <code style="word-break:break-all;"><?= sanitize($demoLink) ?></code></p>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger" role="alert">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?= sanitize($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="index.php?page=mot-de-passe-oublie">
                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope" aria-hidden="true"></i> Adresse e-mail</label>
                        <input type="email" id="email" name="email" class="form-control"
                               required placeholder="votre@email.com" autocomplete="email">
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-paper-plane" aria-hidden="true"></i> Envoyer le lien
                    </button>
                </form>

                <div class="auth-footer">
                    <p><a href="index.php?page=connexion"><i class="fas fa-arrow-left" aria-hidden="true"></i> Retour à la connexion</a></p>
                </div>
            </div>
        </div>
    </div>
</section>
