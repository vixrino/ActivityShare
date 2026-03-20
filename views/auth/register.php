<section class="section">
    <div class="container">
        <div class="auth-container">
            <div class="auth-card auth-card-wide">
                <div class="auth-header">
                    <h1>Inscription</h1>
                    <p>Rejoignez la communauté ActivityShare</p>
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

                <form method="POST" action="index.php?page=inscription">
                    <div class="form-row-2">
                        <div class="form-group">
                            <label for="prenom"><i class="fas fa-user"></i> Prénom *</label>
                            <input type="text" id="prenom" name="prenom" class="form-control"
                                   value="<?= sanitize($_POST['prenom'] ?? '') ?>" required placeholder="Votre prénom">
                        </div>
                        <div class="form-group">
                            <label for="nom"><i class="fas fa-user"></i> Nom *</label>
                            <input type="text" id="nom" name="nom" class="form-control"
                                   value="<?= sanitize($_POST['nom'] ?? '') ?>" required placeholder="Votre nom">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Adresse e-mail *</label>
                        <input type="email" id="email" name="email" class="form-control"
                               value="<?= sanitize($_POST['email'] ?? '') ?>" required placeholder="votre@email.com">
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label for="mot_de_passe"><i class="fas fa-lock"></i> Mot de passe *</label>
                            <div class="password-wrapper">
                                <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control"
                                       required placeholder="Min. 8 caractères" minlength="8">
                                <button type="button" class="password-toggle" onclick="togglePassword('mot_de_passe')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="mot_de_passe_confirm"><i class="fas fa-lock"></i> Confirmer *</label>
                            <div class="password-wrapper">
                                <input type="password" id="mot_de_passe_confirm" name="mot_de_passe_confirm" class="form-control"
                                       required placeholder="Confirmer le mot de passe">
                                <button type="button" class="password-toggle" onclick="togglePassword('mot_de_passe_confirm')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-user-tag"></i> Je souhaite être *</label>
                        <div class="role-selector">
                            <label class="role-option">
                                <input type="radio" name="role" value="participant"
                                       <?= ($_POST['role'] ?? 'participant') === 'participant' ? 'checked' : '' ?>>
                                <div class="role-card">
                                    <i class="fas fa-user"></i>
                                    <strong>Participant</strong>
                                    <small>Je souhaite découvrir et rejoindre des activités</small>
                                </div>
                            </label>
                            <label class="role-option">
                                <input type="radio" name="role" value="organisateur"
                                       <?= ($_POST['role'] ?? '') === 'organisateur' ? 'checked' : '' ?>>
                                <div class="role-card">
                                    <i class="fas fa-bullhorn"></i>
                                    <strong>Organisateur</strong>
                                    <small>Je souhaite proposer des activités à la communauté</small>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label for="telephone"><i class="fas fa-phone"></i> Téléphone</label>
                            <input type="tel" id="telephone" name="telephone" class="form-control"
                                   value="<?= sanitize($_POST['telephone'] ?? '') ?>" placeholder="06 12 34 56 78">
                        </div>
                        <div class="form-group">
                            <label for="ville"><i class="fas fa-map-marker-alt"></i> Ville</label>
                            <input type="text" id="ville" name="ville" class="form-control"
                                   value="<?= sanitize($_POST['ville'] ?? '') ?>" placeholder="Votre ville">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-user-plus"></i> S'inscrire
                    </button>
                </form>

                <div class="auth-footer">
                    <p>Déjà un compte ? <a href="index.php?page=connexion">Connectez-vous</a></p>
                </div>
            </div>
        </div>
    </div>
</section>
