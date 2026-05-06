<?php
    require_once __DIR__ . "/assets/extras/includes/account.inc.php";
    require_auth();

    $extraStyles = ['assets/css/user.css'];
    $activeUserPage = 'dashboard';
    $user = current_user();
    $counts = account_counts((int) $user['id']);
    $bookmarks = array_slice(user_bookmarks((int) $user['id']), 0, 3);
    $bookings = user_bookings((int) $user['id']);
    $reviews = user_reviews((int) $user['id']);
    $activity = recent_account_activity((int) $user['id']);
?>
<?php include_once "assets/extras/header.php"; ?>

<section class="user-shell">
    <div class="user-layout">
        <?php include_once "assets/extras/user-sidebar.php"; ?>

        <div class="user-main">
            <section class="user-hero">
                <div>
                    <span class="user-kicker">User dashboard</span>
                    <h1>Welcome back, <?php echo h($user['name']); ?>.</h1>
                    <p>Track saved listings, upcoming bookings, and recent account activity from one clean workspace.</p>
                </div>
                <div class="user-hero-card">
                    <strong><?php echo $counts['bookmarks']; ?></strong>
                    <span>Saved listings</span>
                </div>
            </section>

            <div class="user-stats">
                <article class="user-card user-stat"><span><i class="fa-solid fa-heart"></i></span><strong><?php echo $counts['bookmarks']; ?></strong><p>Saved listings</p></article>
                <article class="user-card user-stat"><span><i class="fa-solid fa-calendar-check"></i></span><strong><?php echo $counts['bookings']; ?></strong><p>Total bookings</p></article>
                <article class="user-card user-stat"><span><i class="fa-solid fa-star"></i></span><strong><?php echo $counts['reviews']; ?></strong><p>Reviews written</p></article>
                <article class="user-card user-stat"><span><i class="fa-solid fa-bell"></i></span><strong><?php echo count($activity); ?></strong><p>Recent updates</p></article>
            </div>

            <div class="user-section-head">
                <div>
                    <h2>Saved picks</h2>
                    <p>Your recent saved listings are ready for comparison and booking.</p>
                </div>
                <a class="user-btn soft" href="user-bookmarks.php">View all</a>
            </div>

            <?php if (!$bookmarks): ?>
                <div class="user-empty"><h3>No saved listings yet</h3><p>Browse listings and save places you want to revisit.</p><a class="user-btn" href="listings.php">Browse listings</a></div>
            <?php else: ?>
                <div class="user-grid">
                    <?php foreach ($bookmarks as $listing): ?>
                        <article class="user-list-card">
                            <img src="<?php echo h($listing['image_path']); ?>" alt="<?php echo h($listing['title']); ?>">
                            <div class="user-list-body">
                                <span class="user-tag"><?php echo h($listing['category_name']); ?></span>
                                <h3><?php echo h($listing['title']); ?></h3>
                                <p><?php echo h($listing['short_description']); ?></p>
                                <div class="user-actions">
                                    <a class="user-btn" href="listing.php?id=<?php echo (int) $listing['id']; ?>">Open</a>
                                    <a class="user-btn soft" href="user-bookings.php">Book</a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="user-section-head">
                <div>
                    <h2>Recent activity</h2>
                    <p>A lightweight feed for account updates, saved places, booking changes, and review prompts.</p>
                </div>
            </div>

            <?php if (!$activity): ?>
                <div class="user-empty"><h3>No activity yet</h3><p>Your saved listings, bookings, and reviews will appear here.</p></div>
            <?php else: ?>
                <div class="activity-list">
                    <?php foreach ($activity as $item): ?>
                        <article class="activity-item">
                            <span class="activity-icon"><i class="fa-solid <?php echo h($item['icon']); ?>"></i></span>
                            <div><h3><?php echo h($item['title']); ?></h3><p><?php echo h($item['body']); ?></p></div>
                            <time><?php echo h(date('M j', strtotime((string) $item['time']))); ?></time>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include_once "assets/extras/footer.php"; ?>
