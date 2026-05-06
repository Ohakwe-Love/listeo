<?php
    require_once __DIR__ . "/assets/extras/includes/management.inc.php";
    require_admin();

    $extraStyles = ['assets/css/admin.css'];
    $activeAdminPage = 'listings';
    $listings = admin_listings();
    $messages = get_flash_messages();
?>
<?php include_once "assets/extras/header.php"; ?>

<section class="admin-shell">
    <div class="admin-layout">
        <?php include_once "assets/extras/admin-sidebar.php"; ?>
        <main>
            <section class="admin-hero"><h1>Listing moderation</h1><p>Verify payment, approve paid listings, or reject submissions with a reason.</p></section>
            <?php foreach ($messages as $type => $items): ?><?php foreach ($items as $message): ?><p class="admin-alert admin-alert-<?php echo h($type); ?>"><?php echo h($message); ?></p><?php endforeach; ?><?php endforeach; ?>
            <div class="admin-section-head"><h2>All listings</h2></div>
            <table class="admin-table">
                <thead><tr><th>Listing</th><th>Owner</th><th>Plan/payment</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                <?php foreach ($listings as $listing): ?>
                    <tr>
                        <td><strong><?php echo h($listing['title']); ?></strong><br><?php echo h($listing['category_name']); ?></td>
                        <td><?php echo h($listing['owner_name'] ?? 'System'); ?><br><?php echo h($listing['owner_email'] ?? ''); ?></td>
                        <td><?php echo h($listing['plan_name'] ?? 'Seed'); ?> · <?php echo h($listing['payment_review_status'] ?? $listing['payment_status']); ?><br><?php echo h($listing['payment_reference'] ?? ''); ?></td>
                        <td><?php echo h($listing['status']); ?></td>
                        <td>
                            <div class="admin-actions">
                                <form action="admin-listing-action.php" method="post">
                                    <input type="hidden" name="listing_id" value="<?php echo (int) $listing['id']; ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <button class="admin-btn" type="submit">Approve</button>
                                </form>
                                <form action="admin-listing-action.php" method="post">
                                    <input type="hidden" name="listing_id" value="<?php echo (int) $listing['id']; ?>">
                                    <input type="hidden" name="action" value="reject">
                                    <input class="admin-input" type="text" name="reason" placeholder="Rejection reason" required>
                                    <button class="admin-btn dark" type="submit">Reject</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>
</section>

<?php include_once "assets/extras/footer.php"; ?>
