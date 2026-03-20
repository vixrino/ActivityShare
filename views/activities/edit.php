<section class="section page-header-section">
    <div class="container">
        <h1><i class="fas fa-edit"></i> Modifier l'activité</h1>
        <p>Modifiez les informations de votre activité</p>
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

            <form method="POST" action="index.php?page=modifier-activite&id=<?= $activite['id'] ?>" enctype="multipart/form-data">
                <div class="form-section">
                    <h2>Informations générales</h2>

                    <div class="form-group">
                        <label for="titre">Titre de l'activité *</label>
                        <input type="text" id="titre" name="titre" class="form-control"
                               value="<?= sanitize($activite['titre']) ?>" required>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label for="categorie_id">Catégorie *</label>
                            <select id="categorie_id" name="categorie_id" class="form-control" required>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= $activite['categorie_id'] == $cat['id'] ? 'selected' : '' ?>>
                                        <?= sanitize($cat['nom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="type">Type *</label>
                            <select id="type" name="type" class="form-control" required>
                                <option value="public" <?= $activite['type'] === 'public' ? 'selected' : '' ?>>Public</option>
                                <option value="prive" <?= $activite['type'] === 'prive' ? 'selected' : '' ?>>Privé</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Description *</label>
                        <textarea id="description" name="description" class="form-control" rows="5"
                                  required><?= sanitize($activite['description']) ?></textarea>
                    </div>
                </div>

                <div class="form-section">
                    <h2>Date, heure et lieu</h2>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label for="date_debut">Date et heure de début *</label>
                            <input type="datetime-local" id="date_debut" name="date_debut" class="form-control"
                                   value="<?= date('Y-m-d\TH:i', strtotime($activite['date_debut'])) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="date_fin">Date et heure de fin *</label>
                            <input type="datetime-local" id="date_fin" name="date_fin" class="form-control"
                                   value="<?= date('Y-m-d\TH:i', strtotime($activite['date_fin'])) ?>" required>
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label for="lieu">Lieu *</label>
                            <input type="text" id="lieu" name="lieu" class="form-control"
                                   value="<?= sanitize($activite['lieu']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="adresse">Adresse précise</label>
                            <input type="text" id="adresse" name="adresse" class="form-control"
                                   value="<?= sanitize($activite['adresse'] ?? '') ?>">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h2>Participants et conditions</h2>

                    <div class="form-group">
                        <label for="nb_max_participants">Nombre maximum de participants *</label>
                        <input type="number" id="nb_max_participants" name="nb_max_participants" class="form-control"
                               value="<?= $activite['nb_max_participants'] ?>" required min="1">
                    </div>

                    <div class="form-group">
                        <label for="conditions_participation">Conditions de participation</label>
                        <textarea id="conditions_participation" name="conditions_participation" class="form-control" rows="3"><?= sanitize($activite['conditions_participation'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="form-section">
                    <h2>Photo</h2>

                    <?php if ($activite['photo']): ?>
                        <div class="current-photo">
                            <img src="<?= sanitize($activite['photo']) ?>" alt="Photo actuelle">
                            <p>Photo actuelle</p>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="photo">Nouvelle photo (optionnel)</label>
                        <input type="file" id="photo" name="photo" class="form-control-file" accept="image/*">
                    </div>
                </div>

                <div class="form-actions">
                    <a href="index.php?page=activite&id=<?= $activite['id'] ?>" class="btn btn-outline">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
