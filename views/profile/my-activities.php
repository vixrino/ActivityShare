<section class="section page-header-section">
    <div class="container">
        <div class="page-header-row">
            <div>
                <h1><i class="fas fa-list"></i> Mes activités</h1>
                <p>Gérez les activités que vous avez créées</p>
            </div>
            <a href="index.php?page=creer-activite" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouvelle activité
            </a>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if (empty($activites)): ?>
            <div class="empty-state">
                <i class="fas fa-calendar-plus"></i>
                <h3>Vous n'avez pas encore créé d'activité</h3>
                <p>Commencez par proposer votre première activité à la communauté !</p>
                <a href="index.php?page=creer-activite" class="btn btn-primary">Créer ma première activité</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Activité</th>
                            <th>Catégorie</th>
                            <th>Date</th>
                            <th>Inscrits</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($activites as $activite): ?>
                            <tr>
                                <td>
                                    <a href="index.php?page=activite&id=<?= $activite['id'] ?>">
                                        <strong><?= sanitize($activite['titre']) ?></strong>
                                    </a>
                                </td>
                                <td>
                                    <span class="badge badge-category">
                                        <i class="fas <?= sanitize($activite['categorie_icone']) ?>"></i>
                                        <?= sanitize($activite['categorie_nom']) ?>
                                    </span>
                                </td>
                                <td><?= formatDateShort($activite['date_debut']) ?></td>
                                <td><?= $activite['nb_inscrits'] ?>/<?= $activite['nb_max_participants'] ?></td>
                                <td>
                                    <span class="status-badge status-<?= $activite['statut'] ?>">
                                        <?= ucfirst($activite['statut']) ?>
                                    </span>
                                </td>
                                <td class="table-actions">
                                    <a href="index.php?page=activite&id=<?= $activite['id'] ?>" class="btn btn-sm btn-outline" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="index.php?page=modifier-activite&id=<?= $activite['id'] ?>" class="btn btn-sm btn-outline" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if ($activite['statut'] === 'active'): ?>
                                        <a href="index.php?page=supprimer-activite&id=<?= $activite['id'] ?>"
                                           class="btn btn-sm btn-danger" title="Annuler"
                                           onclick="return confirm('Annuler cette activité ?')">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>
