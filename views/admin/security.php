<section class="section page-header-section">
    <div class="container">
        <h1><i class="fas fa-shield-alt"></i> Journal de sécurité</h1>
        <p class="page-header-subtitle">Trace des événements sensibles : connexions, échecs CSRF, actions admin.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php include __DIR__ . '/../layout/admin-nav.php'; ?>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-user-times"></i></div>
            <div>
                <div class="stat-value"><?= $resume['login_failed_24h'] ?></div>
                <div class="stat-label">Échecs login (24h)</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-lock"></i></div>
            <div>
                <div class="stat-value"><?= $resume['login_blocked_24h'] ?></div>
                <div class="stat-label">Blocages brute-force (24h)</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-shield-alt"></i></div>
            <div>
                <div class="stat-value"><?= $resume['csrf_failed_24h'] ?></div>
                <div class="stat-label">Échecs CSRF (24h)</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-user-shield"></i></div>
            <div>
                <div class="stat-value"><?= $resume['admin_actions_24h'] ?></div>
                <div class="stat-label">Actions admin (24h)</div>
            </div>
        </div>
    </div>

    <form method="get" class="security-filter">
        <input type="hidden" name="page" value="admin-securite">
        <label for="action_filter" class="visually-hidden">Filtrer par action</label>
        <select name="action_filter" id="action_filter" onchange="this.form.submit()">
            <option value="">— Tous les événements —</option>
            <?php foreach (['login_success', 'login_failed', 'login_blocked', 'logout', 'password_reset', 'csrf_failed', 'admin_toggle_user', 'admin_delete_user', 'admin_delete_activity'] as $a): ?>
                <option value="<?= $a ?>" <?= ($action === $a) ? 'selected' : '' ?>><?= $a ?></option>
            <?php endforeach; ?>
        </select>
    </form>

    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Action</th>
                    <th>Utilisateur</th>
                    <th>IP</th>
                    <th>Détails</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($logs)): ?>
                    <tr><td colspan="5" class="muted">Aucun événement enregistré.</td></tr>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?= formatDateShort($log['date_creation']) ?> <?= formatTime($log['date_creation']) ?></td>
                            <td><span class="badge badge-action"><?= sanitize($log['action']) ?></span></td>
                            <td>
                                <?php if ($log['utilisateur_id']): ?>
                                    <?= sanitize($log['prenom'] . ' ' . $log['nom']) ?>
                                    <br><small class="muted"><?= sanitize($log['email']) ?></small>
                                <?php else: ?>
                                    <span class="muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td><code><?= sanitize($log['ip']) ?></code></td>
                            <td><?= sanitize($log['details']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    </div>
</section>
