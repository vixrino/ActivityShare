<section class="section page-header-section">
    <div class="container">
        <h1><i class="fas fa-plus-circle"></i> Créer une activité</h1>
        <p>Proposez une nouvelle activité à la communauté</p>
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

            <form method="POST" action="index.php?page=creer-activite" enctype="multipart/form-data">
                <div class="form-section">
                    <h2>Informations générales</h2>

                    <div class="form-group">
                        <label for="titre">Titre de l'activité *</label>
                        <?php
                        $titreValeur = '';
                        if (isset($_POST['titre'])) {
                            $titreValeur = sanitize($_POST['titre']);
                        }
                        ?>
                        <input type="text" id="titre" name="titre" class="form-control"
                               value="<?= $titreValeur ?>" required placeholder="Ex: Randonnée au Lac de Bordeaux">
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label for="categorie_id">Catégorie *</label>
                            <select id="categorie_id" name="categorie_id" class="form-control" required>
                                <option value="">Choisir une catégorie</option>
                                <?php foreach ($categories as $cat): ?>
                                    <?php
                                    $selected = '';
                                    if (isset($_POST['categorie_id']) && $_POST['categorie_id'] == $cat['id']) {
                                        $selected = 'selected';
                                    }
                                    ?>
                                    <option value="<?= $cat['id'] ?>" <?= $selected ?>>
                                        <?= sanitize($cat['nom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="type">Type *</label>
                            <select id="type" name="type" class="form-control" required>
                                <option value="public" <?php if (isset($_POST['type']) && $_POST['type'] === 'public') echo 'selected'; ?>>Public</option>
                                <option value="prive" <?php if (isset($_POST['type']) && $_POST['type'] === 'prive') echo 'selected'; ?>>Privé</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Description *</label>
                        <?php
                        $descriptionValeur = '';
                        if (isset($_POST['description'])) {
                            $descriptionValeur = sanitize($_POST['description']);
                        }
                        ?>
                        <textarea id="description" name="description" class="form-control" rows="5"
                                  required placeholder="Décrivez votre activité en détail..."><?= $descriptionValeur ?></textarea>
                    </div>
                </div>

                <div class="form-section">
                    <h2>Date, heure et lieu</h2>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label for="date_debut">Date et heure de début *</label>
                            <?php
                            $dateDebutValeur = '';
                            if (isset($_POST['date_debut'])) {
                                $dateDebutValeur = $_POST['date_debut'];
                            }
                            ?>
                            <input type="datetime-local" id="date_debut" name="date_debut" class="form-control"
                                   value="<?= $dateDebutValeur ?>"
                                   min="<?= date('Y-m-d\TH:i') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="date_fin">Date et heure de fin *</label>
                            <?php
                            $dateFinValeur = '';
                            if (isset($_POST['date_fin'])) {
                                $dateFinValeur = $_POST['date_fin'];
                            }
                            ?>
                            <input type="datetime-local" id="date_fin" name="date_fin" class="form-control"
                                   value="<?= $dateFinValeur ?>"
                                   min="<?= date('Y-m-d\TH:i') ?>" required>
                        </div>
                    </div>

                    <div class="form-row-2">
                        <div class="form-group">
                            <label for="lieu">Lieu *</label>
                            <?php
                            $lieuValeur = '';
                            if (isset($_POST['lieu'])) {
                                $lieuValeur = sanitize($_POST['lieu']);
                            }
                            ?>
                            <input type="text" id="lieu" name="lieu" class="form-control"
                                   value="<?= $lieuValeur ?>" required placeholder="Ex: Bordeaux">
                        </div>
                        <div class="form-group">
                            <label for="adresse">Adresse précise</label>
                            <?php
                            $adresseValeur = '';
                            if (isset($_POST['adresse'])) {
                                $adresseValeur = sanitize($_POST['adresse']);
                            }
                            ?>
                            <input type="text" id="adresse" name="adresse" class="form-control"
                                   value="<?= $adresseValeur ?>" placeholder="Ex: 12 Rue de la Paix">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h2>Participants et conditions</h2>

                    <div class="form-group">
                        <label for="nb_max_participants">Nombre maximum de participants *</label>
                        <?php
                        $nbMaxValeur = '';
                        if (isset($_POST['nb_max_participants'])) {
                            $nbMaxValeur = sanitize($_POST['nb_max_participants']);
                        }
                        ?>
                        <input type="number" id="nb_max_participants" name="nb_max_participants" class="form-control"
                               value="<?= $nbMaxValeur ?>" required min="1" placeholder="Ex: 20">
                    </div>

                    <div class="form-group">
                        <label for="conditions_participation">Conditions de participation</label>
                        <?php
                        $conditionsValeur = '';
                        if (isset($_POST['conditions_participation'])) {
                            $conditionsValeur = sanitize($_POST['conditions_participation']);
                        }
                        ?>
                        <textarea id="conditions_participation" name="conditions_participation" class="form-control" rows="3"
                                  placeholder="Ex: Apporter ses chaussures de randonnée, niveau intermédiaire requis..."><?= $conditionsValeur ?></textarea>
                    </div>
                </div>

                <div class="form-section">
                    <h2>Photo</h2>

                    <div class="form-group">
                        <label for="photo">Photo illustrative (optionnel)</label>
                        <input type="file" id="photo" name="photo" class="form-control-file" accept="image/*">
                        <small class="form-help">Formats acceptés : JPEG, PNG, GIF, WebP. Taille max : 5 Mo.</small>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="index.php?page=mes-activites" class="btn btn-outline">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check"></i> Créer l'activité
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
