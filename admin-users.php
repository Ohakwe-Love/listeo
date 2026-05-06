<?php
    require_once __DIR__ . "/assets/extras/includes/management.inc.php";
    require_admin();

    $extraStyles = ['assets/css/admin.css'];
    $activeAdminPage = 'users';
    $users = admin_users();
    $messages = get_flash_messages();
?>
<?php include_once "assets/extras/header.php"; ?>

<section class="admin-shell">
    <div class="admin-layout">
        <?php include_once "assets/extras/admin-sidebar.php"; ?>
        <main>
            <section class="admin-hero"><h1>User oversight</h1><p>Review account roles and start strict support impersonation when absolutely necessary.</p></section>
            <?php foreach ($messages as $type => $items): ?><?php foreach ($items as $message): ?><p class="admin-alert admin-alert-<?php echo h($type); ?>"><?php echo h($message); ?></p><?php endforeach; ?><?php endforeach; ?>
            <div class="admin-section-head"><h2>All users</h2></div>
            <table class="admin-table">
                <thead><tr><th>User</th><th>Role</th><th>Status</th><th>Joined</th><th>Strict action</th></tr></thead>
                <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><strong><?php echo h($user['name']); ?></strong><br><?php echo h($user['email']); ?></td>
                        <td><?php echo h($user['role']); ?></td>
                        <td><?php echo h($user['account_status']); ?></td>
                        <td><?php echo h(date('M j, Y', strtotime($user['created_at']))); ?></td>
                        <td>
                            <?php if ($user['role'] !== 'admin'): ?>
                                <form class="admin-actions" action="admin-impersonate.php" method="post">
                                    <input type="hidden" name="user_id" value="<?php echo (int) $user['id']; ?>">
                                    <input class="admin-input" type="text" name="reason" placeholder="Required support/security reason" required>
                                    <button class="admin-btn dark" type="submit">Impersonate</button>
                                </form>
                            <?php else: ?>
                                <span>Not allowed</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>
</section>

<?php include_once "assets/extras/footer.php"; ?>
