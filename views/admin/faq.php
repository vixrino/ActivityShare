<section class="section page-header-section">
    <div class="container">
        <h1><i class="fas fa-question-circle"></i> Gestion de la FAQ</h1>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="admin-nav">
            <a href="index.php?page=admin" class="admin-nav-link"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a>
            <a href="index.php?page=admin-utilisateurs" class="admin-nav-link"><i class="fas fa-users"></i> Utilisateurs</a>
            <a href="index.php?page=admin-activites" class="admin-nav-link"><i class="fas fa-calendar"></i> Activités</a>
            <a href="index.php?page=admin-faq" class="admin-nav-link active"><i class="fas fa-question-circle"></i> FAQ</a>
            <a href="index.php?page=admin-messages" class="admin-nav-link"><i class="fas fa-envelope"></i> Messages</a>
        </div>

        <div class="form-container mb-2">
            <h2>Ajouter une question</h2>
            <form method="POST" action="index.php?page=admin-faq">
                <input type="hidden" name="faq_action" value="create">
                <div class="form-group">
                    <label for="question">Question *</label>
                    <input type="text" id="question" name="question" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="reponse">Réponse *</label>
                    <textarea id="reponse" name="reponse" class="form-control" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label for="ordre">Ordre d'affichage</label>
                    <input type="number" id="ordre" name="ordre" class="form-control" value="0">
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Ajouter</button>
            </form>
        </div>

        <div class="faq-admin-list">
            <?php foreach ($faqs as $f): ?>
                <div class="faq-admin-item">
                    <form method="POST" action="index.php?page=admin-faq" class="faq-edit-form">
                        <input type="hidden" name="faq_action" value="update">
                        <input type="hidden" name="faq_id" value="<?= $f['id'] ?>">
                        <div class="form-group">
                            <label>Question</label>
                            <input type="text" name="question" class="form-control" value="<?= sanitize($f['question']) ?>">
                        </div>
                        <div class="form-group">
                            <label>Réponse</label>
                            <textarea name="reponse" class="form-control" rows="2"><?= sanitize($f['reponse']) ?></textarea>
                        </div>
                        <div class="faq-admin-actions">
                            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
                        </div>
                    </form>
                    <form method="POST" action="index.php?page=admin-faq" class="inline-form">
                        <input type="hidden" name="faq_action" value="delete">
                        <input type="hidden" name="faq_id" value="<?= $f['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger"
                                onclick="return confirm('Supprimer cette question ?')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
