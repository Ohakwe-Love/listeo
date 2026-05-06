<?php
    require_once __DIR__ . "/assets/extras/includes/management.inc.php";
    require_admin();

    $extraStyles = ['assets/css/admin.css'];
    $activeAdminPage = 'audit';
    $auditLogs = db()->query(
        'SELECT admin_audit_logs.*, users.name AS admin_name, users.email AS admin_email
         FROM admin_audit_logs
         INNER JOIN users ON users.id = admin_audit_logs.admin_id
         ORDER BY admin_audit_logs.created_at DESC
         LIMIT 100'
    )->fetchAll();
    $impersonationLogs = db()->query(
        'SELECT logs.*, admin.email AS admin_email, target.email AS target_email
         FROM admin_impersonation_logs logs
         INNER JOIN users admin ON admin.id = logs.admin_id
         INNER JOIN users target ON target.id = logs.user_id
         ORDER BY logs.started_at DESC
         LIMIT 100'
    )->fetchAll();
?>
<?php include_once "assets/extras/header.php"; ?>

<section class="admin-shell">
    <div class="admin-layout">
        <?php include_once "assets/extras/admin-sidebar.php"; ?>
        <main>
            <section class="admin-hero"><h1>Security audit</h1><p>Review strict admin actions and every impersonation session.</p></section>
            <div class="admin-section-head"><h2>Admin actions</h2></div>
            <table class="admin-table">
                <thead><tr><th>Admin</th><th>Action</th><th>Target</th><th>Details</th><th>Time</th></tr></thead>
                <tbody>
                <?php foreach ($auditLogs as $log): ?>
                    <tr><td><?php echo h($log['admin_email']); ?></td><td><?php echo h($log['action']); ?></td><td><?php echo h(($log['target_type'] ?? '') . ' #' . ($log['target_id'] ?? '')); ?></td><td><?php echo h($log['details']); ?></td><td><?php echo h($log['created_at']); ?></td></tr>
                <?php endforeach; ?>
                <?php if (!$auditLogs): ?><tr><td colspan="5">No audit logs yet.</td></tr><?php endif; ?>
                </tbody>
            </table>

            <div class="admin-section-head"><h2>Impersonation sessions</h2></div>
            <table class="admin-table">
                <thead><tr><th>Admin</th><th>User</th><th>Reason</th><th>Started</th><th>Ended</th></tr></thead>
                <tbody>
                <?php foreach ($impersonationLogs as $log): ?>
                    <tr><td><?php echo h($log['admin_email']); ?></td><td><?php echo h($log['target_email']); ?></td><td><?php echo h($log['reason']); ?></td><td><?php echo h($log['started_at']); ?></td><td><?php echo h($log['ended_at'] ?? 'Active/unknown'); ?></td></tr>
                <?php endforeach; ?>
                <?php if (!$impersonationLogs): ?><tr><td colspan="5">No impersonation logs yet.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
</section>

<?php include_once "assets/extras/footer.php"; ?>
