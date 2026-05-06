<?php
    require_once __DIR__ . "/assets/extras/includes/management.inc.php";
    require_auth();

    $extraStyles = ['assets/css/user.css'];
    $activeUserPage = 'listings';
    $user = current_user();
    $listings = lister_listings((int) $user['id']);
    $messages = get_flash_messages();
?>
<?php include_once "assets/extras/header.php"; ?>

<section class="user-shell">
    <div class="user-layout">
        <?php include_once "assets/extras/user-sidebar.php"; ?>

        <div class="user-main">
            <section class="user-hero">
                <div>
                    <span class="user-kicker">My listings</span>
                    <h1>Manage your submitted houses and service listings.</h1>
                    <p>Paid submissions are reviewed by admin before they become public.</p>
                </div>
                <div class="user-hero-card"><strong><?php echo count($listings); ?></strong><span>Total submissions</span></div>
            </section>

            <?php foreach ($messages as $type => $items): ?>
                <?php foreach ($items as $message): ?>
                    <p class="user-alert user-alert-<?php echo h($type); ?>"><?php echo h($message); ?></p>
                <?php endforeach; ?>
            <?php endforeach; ?>

            <div class="user-section-head">
                <div><h2>Submitted listings</h2><p>Track payment and admin approval status.</p></div>
                <a class="user-btn" href="user-listing-new.php">Add listing</a>
            </div>

            <?php if (!$listings): ?>
                <div class="user-empty"><h3>No listings yet</h3><p>Create your first paid listing submission.</p><a class="user-btn" href="user-listing-new.php">Add listing</a></div>
            <?php else: ?>
                <table class="user-table">
                    <thead><tr><th>Listing</th><th>Category</th><th>Plan</th><th>Payment</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php foreach ($listings as $listing): ?>
                            <tr>
                                <td><?php echo h($listing['title']); ?></td>
                                <td><?php echo h($listing['category_name']); ?></td>
                                <td><?php echo h($listing['plan_name'] ?? ''); ?> · NGN <?php echo number_format((float) ($listing['amount'] ?? 0), 2); ?></td>
                                <td><span class="status-pill <?php echo $listing['payment_status'] === 'paid' ? '' : 'pending'; ?>"><?php echo h(ucfirst($listing['payment_status'])); ?></span></td>
                                <td><span class="status-pill <?php echo $listing['status'] === 'published' ? '' : 'pending'; ?>"><?php echo h(str_replace('_', ' ', ucfirst($listing['status']))); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include_once "assets/extras/footer.php"; ?>
