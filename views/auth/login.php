<section class="section">
    <div class="container">
        <div class="auth-container">
            <div class="auth-card">
                <div class="auth-header">
                    <h1>Connexion</h1>
                    <p>Accédez à votre compte ActivityShare</p>
                </div>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?= sanitize($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="index.php?page=connexion">
                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Adresse e-mail</label>
                        <input type="email" id="email" name="email" class="form-control"
                               value="<?= sanitize($_POST['email'] ?? '') ?>" required placeholder="votre@email.com">
                    </div>

                    <div class="form-group">
                        <label for="mot_de_passe"><i class="fas fa-lock"></i> Mot de passe</label>
                        <div class="password-wrapper">
                            <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control"
                                   required placeholder="Votre mot de passe">
                            <button type="button" class="password-toggle" onclick="togglePassword('mot_de_passe')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group form-row">
                        <a href="index.php?page=mot-de-passe-oublie" class="form-link">Mot de passe oublié ?</a>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-sign-in-alt"></i> Se connecter
                    </button>
                </form>

                <div class="auth-footer">
                    <p>Pas encore de compte ? <a href="index.php?page=inscription">Inscrivez-vous</a></p>
                </div>
            </div>
        </div>
    </div>
</section>
