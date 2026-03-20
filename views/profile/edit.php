<section class="section page-header-section">
    <div class="container">
        <h1><i class="fas fa-user-edit"></i> Modifier mon profil</h1>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="form-container">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= sanitize($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="index.php?page=modifier-profil" enctype="multipart/form-data">
                <div class="form-section">
                    <h2>Informations personnelles</h2>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label for="prenom">Prénom *</label>
                            <input type="text" id="prenom" name="prenom" class="form-control"
                                   value="<?= sanitize($user['prenom']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="nom">Nom *</label>
                            <input type="text" id="nom" name="nom" class="form-control"
                                   value="<?= sanitize($user['nom']) ?>" required>
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label for="telephone">Téléphone</label>
                            <input type="tel" id="telephone" name="telephone" class="form-control"
                                   value="<?= sanitize($user['telephone'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="ville">Ville</label>
                            <input type="text" id="ville" name="ville" class="form-control"
                                   value="<?= sanitize($user['ville'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="bio">Bio</label>
                        <textarea id="bio" name="bio" class="form-control" rows="3"
                                  placeholder="Parlez-nous de vous..."><?= sanitize($user['bio'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="photo_profil">Photo de profil</label>
                        <?php if ($user['photo_profil']): ?>
                            <div class="current-photo-sm">
                                <img src="<?= sanitize($user['photo_profil']) ?>" alt="Photo actuelle">
                            </div>
                        <?php endif; ?>
                        <input type="file" id="photo_profil" name="photo_profil" class="form-control-file" accept="image/*">
                    </div>
                </div>

                <div class="form-section">
                    <h2>Changer le mot de passe</h2>
                    <p class="form-help">Laissez vide pour conserver votre mot de passe actuel.</p>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label for="nouveau_mot_de_passe">Nouveau mot de passe</label>
                            <input type="password" id="nouveau_mot_de_passe" name="nouveau_mot_de_passe"
                                   class="form-control" minlength="8" placeholder="Min. 8 caractères">
                        </div>
                        <div class="form-group">
                            <label for="confirmer_mot_de_passe">Confirmer</label>
                            <input type="password" id="confirmer_mot_de_passe" name="confirmer_mot_de_passe"
                                   class="form-control" placeholder="Confirmer le mot de passe">
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="index.php?page=profil" class="btn btn-outline">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
