<?php
    require_once __DIR__ . "/assets/extras/includes/account.inc.php";
    require_auth();

    $extraStyles = ['assets/css/user.css'];
    $activeUserPage = 'reviews';
    $user = current_user();
    $reviews = user_reviews((int) $user['id']);
    $messages = get_flash_messages();
?>
<?php include_once "assets/extras/header.php"; ?>

<section class="user-shell">
    <div class="user-layout">
        <?php include_once "assets/extras/user-sidebar.php"; ?>

        <div class="user-main">
            <section class="user-hero">
                <div>
                    <span class="user-kicker">Reviews</span>
                    <h1>Build trust by sharing useful feedback.</h1>
                    <p>Your reviews are saved to the database and can later be moderated from admin.</p>
                </div>
                <div class="user-hero-card"><strong><?php echo count($reviews); ?></strong><span>Reviews written</span></div>
            </section>

            <?php foreach ($messages as $type => $items): ?>
                <?php foreach ($items as $message): ?>
                    <p class="user-alert user-alert-<?php echo h($type); ?>"><?php echo h($message); ?></p>
                <?php endforeach; ?>
            <?php endforeach; ?>

            <div class="user-section-head">
                <div><h2>Your reviews</h2><p>Helpful feedback improves listing quality and customer confidence.</p></div>
                <a class="user-btn" href="listings.php">Review a listing</a>
            </div>

            <?php if (!$reviews): ?>
                <div class="user-empty"><h3>No reviews yet</h3><p>Open a listing and leave your first review.</p><a class="user-btn" href="listings.php">Browse listings</a></div>
            <?php else: ?>
                <div class="activity-list">
                    <?php foreach ($reviews as $review): ?>
                        <article class="activity-item">
                            <span class="activity-icon"><i class="fa-solid fa-star"></i></span>
                            <div>
                                <h3><?php echo h($review['title']); ?></h3>
                                <p><?php echo h($review['comment']); ?></p>
                            </div>
                            <time><?php echo (int) $review['rating']; ?>.0</time>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include_once "assets/extras/footer.php"; ?>
