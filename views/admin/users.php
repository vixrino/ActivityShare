<section class="section page-header-section">
    <div class="container">
        <h1><i class="fas fa-users" aria-hidden="true"></i> Gestion des utilisateurs</h1>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php include __DIR__ . '/../layout/admin-nav.php'; ?>

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
                            <td class="table-actions">
                                <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                    <a href="index.php?page=admin-toggle-user&id=<?= $u['id'] ?>"
                                       class="btn btn-sm <?= $u['actif'] ? 'btn-outline' : 'btn-primary' ?>"
                                       aria-label="<?= $u['actif'] ? 'Désactiver' : 'Activer' ?> <?= sanitize($u['prenom']) ?>"
                                       onclick="return confirm('<?= $u['actif'] ? 'Désactiver' : 'Activer' ?> cet utilisateur ?')">
                                        <i class="fas <?= $u['actif'] ? 'fa-user-slash' : 'fa-user-check' ?>" aria-hidden="true"></i>
                                        <?= $u['actif'] ? 'Désactiver' : 'Activer' ?>
                                    </a>
                                    <a href="index.php?page=admin-supprimer-user&id=<?= $u['id'] ?>"
                                       class="btn btn-sm btn-danger"
                                       aria-label="Supprimer <?= sanitize($u['prenom']) ?>"
                                       onclick="return confirm('Supprimer définitivement cet utilisateur et toutes ses données ?\n\nCette action est irréversible.')">
                                        <i class="fas fa-trash" aria-hidden="true"></i> Supprimer
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
