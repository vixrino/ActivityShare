<section class="section">
    <div class="container">
        <div class="auth-container">
            <div class="auth-card">
                <div class="auth-header">
                    <h1>Mot de passe oublié</h1>
                    <p>Entrez votre adresse e-mail pour recevoir un lien de réinitialisation</p>
                </div>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        Si un compte existe avec cette adresse e-mail, un lien de réinitialisation vous a été envoyé.
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

                <form method="POST" action="index.php?page=mot-de-passe-oublie">
                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Adresse e-mail</label>
                        <input type="email" id="email" name="email" class="form-control"
                               required placeholder="votre@email.com">
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-paper-plane"></i> Envoyer le lien
                    </button>
                </form>

                <div class="auth-footer">
                    <p><a href="index.php?page=connexion"><i class="fas fa-arrow-left"></i> Retour à la connexion</a></p>
                </div>
            </div>
        </div>
    </div>
</section>
