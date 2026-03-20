<section class="section page-header-section">
    <div class="container">
        <h1><i class="fas fa-users"></i> Gestion des utilisateurs</h1>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="admin-nav">
            <a href="index.php?page=admin" class="admin-nav-link"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a>
            <a href="index.php?page=admin-utilisateurs" class="admin-nav-link active"><i class="fas fa-users"></i> Utilisateurs</a>
            <a href="index.php?page=admin-activites" class="admin-nav-link"><i class="fas fa-calendar"></i> Activités</a>
            <a href="index.php?page=admin-faq" class="admin-nav-link"><i class="fas fa-question-circle"></i> FAQ</a>
            <a href="index.php?page=admin-messages" class="admin-nav-link"><i class="fas fa-envelope"></i> Messages</a>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Inscription</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($utilisateurs as $u): ?>
                        <tr>
                            <td><?= $u['id'] ?></td>
                            <td><?= sanitize($u['prenom'] . ' ' . $u['nom']) ?></td>
                            <td><?= sanitize($u['email']) ?></td>
                            <td>
                                <span class="role-badge role-<?= $u['role'] ?>"><?= ucfirst($u['role']) ?></span>
                            </td>
                            <td><?= formatDateShort($u['date_inscription']) ?></td>
                            <td>
                                <span class="status-badge status-<?= $u['actif'] ? 'active' : 'annulee' ?>">
                                    <?= $u['actif'] ? 'Actif' : 'Inactif' ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                    <a href="index.php?page=admin-toggle-user&id=<?= $u['id'] ?>"
                                       class="btn btn-sm <?= $u['actif'] ? 'btn-danger' : 'btn-primary' ?>"
                                       onclick="return confirm('<?= $u['actif'] ? 'Désactiver' : 'Activer' ?> cet utilisateur ?')">
                                        <?= $u['actif'] ? 'Désactiver' : 'Activer' ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">Vous</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
