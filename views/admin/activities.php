<section class="section page-header-section">
    <div class="container">
        <h1><i class="fas fa-calendar"></i> Gestion des activités</h1>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="admin-nav">
            <a href="index.php?page=admin" class="admin-nav-link"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a>
            <a href="index.php?page=admin-utilisateurs" class="admin-nav-link"><i class="fas fa-users"></i> Utilisateurs</a>
            <a href="index.php?page=admin-activites" class="admin-nav-link active"><i class="fas fa-calendar"></i> Activités</a>
            <a href="index.php?page=admin-faq" class="admin-nav-link"><i class="fas fa-question-circle"></i> FAQ</a>
            <a href="index.php?page=admin-messages" class="admin-nav-link"><i class="fas fa-envelope"></i> Messages</a>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Titre</th>
                        <th>Organisateur</th>
                        <th>Catégorie</th>
                        <th>Date</th>
                        <th>Inscrits</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($activites as $a): ?>
                        <tr>
                            <td><?= $a['id'] ?></td>
                            <td>
                                <a href="index.php?page=activite&id=<?= $a['id'] ?>">
                                    <?= sanitize($a['titre']) ?>
                                </a>
                            </td>
                            <td><?= sanitize($a['organisateur_prenom'] . ' ' . $a['organisateur_nom']) ?></td>
                            <td><?= sanitize($a['categorie_nom']) ?></td>
                            <td><?= formatDateShort($a['date_debut']) ?></td>
                            <td><?= $a['nb_inscrits'] ?>/<?= $a['nb_max_participants'] ?></td>
                            <td>
                                <span class="status-badge status-<?= $a['statut'] ?>"><?= ucfirst($a['statut']) ?></span>
                            </td>
                            <td class="table-actions">
                                <a href="index.php?page=activite&id=<?= $a['id'] ?>" class="btn btn-sm btn-outline" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if ($a['statut'] === 'active'): ?>
                                    <a href="index.php?page=admin-delete-activity&id=<?= $a['id'] ?>"
                                       class="btn btn-sm btn-danger" title="Supprimer"
                                       onclick="return confirm('Supprimer cette activité ?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
