<section class="section">
    <div class="container">
        <div class="auth-container">
            <div class="auth-card">
                <div class="auth-header">
                    <h1>Nouveau mot de passe</h1>
                    <p>Choisissez un nouveau mot de passe pour votre compte</p>
                </div>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger" role="alert">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?= sanitize($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (!empty($reset) && empty($errors)): ?>
                    <form method="POST" action="index.php?page=reinitialiser-mot-de-passe">
                        <input type="hidden" name="token" value="<?= sanitize($token) ?>">

                        <div class="form-group">
                            <label for="mot_de_passe"><i class="fas fa-lock" aria-hidden="true"></i> Nouveau mot de passe *</label>
                            <div class="password-wrapper">
                                <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control"
                                       required minlength="8" placeholder="Min. 8 caractères" autocomplete="new-password">
                                <button type="button" class="password-toggle" onclick="togglePassword('mot_de_passe')" aria-label="Afficher le mot de passe">
                                    <i class="fas fa-eye" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="confirmer_mot_de_passe"><i class="fas fa-lock" aria-hidden="true"></i> Confirmer *</label>
                            <input type="password" id="confirmer_mot_de_passe" name="confirmer_mot_de_passe" class="form-control"
                                   required minlength="8" placeholder="Confirmer le mot de passe" autocomplete="new-password">
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-key" aria-hidden="true"></i> Réinitialiser
                        </button>
                    </form>
                <?php else: ?>
                    <p><a href="index.php?page=mot-de-passe-oublie" class="btn btn-outline btn-block">Demander un nouveau lien</a></p>
                <?php endif; ?>

                <div class="auth-footer">
                    <p><a href="index.php?page=connexion"><i class="fas fa-arrow-left" aria-hidden="true"></i> Retour à la connexion</a></p>
                </div>
            </div>
        </div>
    </div>
</section>
