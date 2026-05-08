<section class="section page-header-section">
    <div class="container">
        <h1><i class="fas fa-file-contract" aria-hidden="true"></i> CGU & Mentions légales</h1>
        <p>Modifiez le contenu affiché sur les pages publiques.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php include __DIR__ . '/../layout/admin-nav.php'; ?>

        <?php foreach ($contenus as $c): ?>
            <div class="editorial-edit-card">
                <form method="POST" action="index.php?page=admin-editorial">
                    <?= csrfField() ?>
                    <input type="hidden" name="cle" value="<?= sanitize($c['cle']) ?>">

                    <div class="editorial-head">
                        <div>
                            <span class="editorial-tag"><?= sanitize($c['cle']) ?></span>
                            <small class="text-muted">Mis à jour <?= timeAgo($c['date_maj']) ?></small>
                        </div>
                        <a href="index.php?page=<?= sanitize($c['cle']) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline">
                            <i class="fas fa-external-link-alt" aria-hidden="true"></i> Voir la page
                        </a>
                    </div>

                    <div class="form-group">
                        <label for="titre-<?= sanitize($c['cle']) ?>">Titre *</label>
                        <input type="text" id="titre-<?= sanitize($c['cle']) ?>" name="titre" class="form-control"
                               value="<?= sanitize($c['titre']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="contenu-<?= sanitize($c['cle']) ?>">Contenu (HTML autorisé : h1-h4, p, ul, li, strong, em, a) *</label>
                        <textarea id="contenu-<?= sanitize($c['cle']) ?>" name="contenu" class="form-control"
                                  rows="14" required><?= htmlspecialchars($c['contenu'], ENT_QUOTES, 'UTF-8') ?></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save" aria-hidden="true"></i> Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</section>
