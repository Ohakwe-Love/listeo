<?php
    require_once __DIR__ . "/assets/extras/includes/account.inc.php";
    require_auth();

    $extraStyles = ['assets/css/user.css'];
    $activeUserPage = 'bookmarks';
    $user = current_user();
    $listings = user_bookmarks((int) $user['id']);
    $messages = get_flash_messages();
?>
<?php include_once "assets/extras/header.php"; ?>

<section class="user-shell">
    <div class="user-layout">
        <?php include_once "assets/extras/user-sidebar.php"; ?>

        <div class="user-main">
            <section class="user-hero">
                <div>
                    <span class="user-kicker">Bookmarks</span>
                    <h1>Your saved places, grouped for quick decisions.</h1>
                    <p>Use bookmarks as a shortlist before contacting providers or making bookings.</p>
                </div>
                <div class="user-hero-card"><strong><?php echo count($listings); ?></strong><span>Saved listings</span></div>
            </section>

            <?php foreach ($messages as $type => $items): ?>
                <?php foreach ($items as $message): ?>
                    <p class="user-alert user-alert-<?php echo h($type); ?>"><?php echo h($message); ?></p>
                <?php endforeach; ?>
            <?php endforeach; ?>

            <div class="user-section-head">
                <div><h2>Saved listings</h2><p>Your shortlist is ready when you want to compare, book, or contact providers.</p></div>
                <a class="user-btn" href="listings.php">Find more</a>
            </div>

            <?php if (!$listings): ?>
                <div class="user-empty"><h3>No bookmarks yet</h3><p>Open a listing and use the save button to add it here.</p><a class="user-btn" href="listings.php">Browse listings</a></div>
            <?php else: ?>
                <div class="user-grid">
                    <?php foreach ($listings as $listing): ?>
                        <article class="user-list-card">
                            <img src="<?php echo h($listing['image_path']); ?>" alt="<?php echo h($listing['title']); ?>">
                            <div class="user-list-body">
                                <span class="user-tag"><?php echo h($listing['category_name']); ?></span>
                                <h3><?php echo h($listing['title']); ?></h3>
                                <p><?php echo h($listing['short_description']); ?></p>
                                <div class="user-actions">
                                    <a class="user-btn" href="listing.php?id=<?php echo (int) $listing['id']; ?>">Open</a>
                                    <form action="bookmark-action.php" method="post">
                                        <input type="hidden" name="listing_id" value="<?php echo (int) $listing['id']; ?>">
                                        <input type="hidden" name="redirect" value="user-bookmarks.php">
                                        <input type="hidden" name="action" value="remove">
                                        <button class="user-btn soft" type="submit">Remove</button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include_once "assets/extras/footer.php"; ?>
