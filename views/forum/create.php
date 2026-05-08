<section class="section page-header-section">
    <div class="container">
        <nav class="breadcrumb" aria-label="Fil d'Ariane">
            <a href="index.php?page=forum">Forum</a> <span aria-hidden="true">›</span>
            <span>Nouveau sujet</span>
        </nav>
        <h1><i class="fas fa-pen-to-square" aria-hidden="true"></i> Créer un nouveau sujet</h1>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="form-container">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger" role="alert">
                    <ul><?php foreach ($errors as $e): ?><li><?= sanitize($e) ?></li><?php endforeach; ?></ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="index.php?page=forum-nouveau-sujet">
                <?= csrfField() ?>
                <div class="form-group">
                    <label for="categorie">Catégorie *</label>
                    <select id="categorie" name="forum_categorie_id" class="form-control" required>
                        <option value="">Choisir…</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= intval($c['id']) ?>" <?= $categorieId == $c['id'] ? 'selected' : '' ?>>
                                <?= sanitize($c['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="titre">Titre du sujet *</label>
                    <input type="text" id="titre" name="titre" class="form-control" required maxlength="255"
                           value="<?= sanitize($_POST['titre'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="contenu">Votre message *</label>
                    <textarea id="contenu" name="contenu" rows="8" class="form-control" required
                              placeholder="Décrivez votre sujet…"><?= sanitize($_POST['contenu'] ?? '') ?></textarea>
                </div>

                <div class="form-actions">
                    <a href="index.php?page=forum" class="btn btn-outline">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane" aria-hidden="true"></i> Publier
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
