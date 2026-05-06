<?php
    require_once __DIR__ . "/assets/extras/includes/management.inc.php";
    require_admin();

    $extraStyles = ['assets/css/admin.css'];
    $activeAdminPage = 'dashboard';
    $counts = admin_counts();
    $pending = array_slice(admin_pending_listings(), 0, 5);
?>
<?php include_once "assets/extras/header.php"; ?>

<section class="admin-shell">
    <div class="admin-layout">
        <?php include_once "assets/extras/admin-sidebar.php"; ?>
        <main>
            <section class="admin-hero"><h1>Admin dashboard</h1><p>Oversee listers, paid submissions, listing approvals, and sensitive support actions.</p></section>
            <div class="admin-stats">
                <article class="admin-card"><span>Users</span><strong><?php echo $counts['users']; ?></strong></article>
                <article class="admin-card"><span>Listers</span><strong><?php echo $counts['listers']; ?></strong></article>
                <article class="admin-card"><span>Pending</span><strong><?php echo $counts['pending']; ?></strong></article>
                <article class="admin-card"><span>Published</span><strong><?php echo $counts['published']; ?></strong></article>
            </div>
            <div class="admin-section-head"><h2>Pending listing reviews</h2><a class="admin-btn soft" href="admin-listings.php">View all</a></div>
            <table class="admin-table">
                <thead><tr><th>Listing</th><th>Owner</th><th>Payment</th><th>Status</th></tr></thead>
                <tbody>
                <?php foreach ($pending as $listing): ?>
                    <tr><td><?php echo h($listing['title']); ?></td><td><?php echo h($listing['owner_name'] ?? 'Unknown'); ?></td><td><?php echo h($listing['payment_review_status'] ?? 'pending'); ?></td><td><?php echo h($listing['status']); ?></td></tr>
                <?php endforeach; ?>
                <?php if (!$pending): ?><tr><td colspan="4">No pending listings.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
</section>

<?php include_once "assets/extras/footer.php"; ?>
