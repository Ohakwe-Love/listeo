<?php
    require_once __DIR__ . "/assets/extras/includes/account.inc.php";

    $listingId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $listing = $listingId ? find_listing((int) $listingId) : null;
    $authUser = current_user();
    $isSaved = $authUser && $listing ? is_bookmarked((int) $authUser['id'], (int) $listing['id']) : false;
    $messages = get_flash_messages();
?>
<?php include_once "assets/extras/header.php"; ?>

<?php if (!$listing): ?>
    <section class="page-section">
        <div class="page-inner empty-state">
            <span class="page-kicker">Listing</span>
            <h1>Listing not found.</h1>
            <p>The listing may have been removed or the link may be incorrect.</p>
            <a class="page-btn page-btn-primary" href="listings.php">Back to listings</a>
        </div>
    </section>
<?php else: ?>
    <section class="listing-detail-hero" style="background-image: linear-gradient(90deg, rgba(17,17,17,.84), rgba(17,17,17,.35)), url('<?php echo h($listing['image_path']); ?>');">
        <div class="page-inner">
            <span class="page-kicker"><?php echo h($listing['category_name']); ?></span>
            <h1><?php echo h($listing['title']); ?></h1>
            <p><?php echo h($listing['short_description']); ?></p>
            <div class="listing-detail-meta">
                <span><?php echo h($listing['location']); ?></span>
                <span><?php echo h($listing['price_label']); ?></span>
                <span><?php echo h((string) $listing['rating']); ?> rating &middot; <?php echo (int) $listing['review_count']; ?> reviews</span>
            </div>
        </div>
    </section>

    <section class="page-section">
        <div class="page-inner listing-detail-layout">
            <article class="listing-detail-main">
                <?php foreach ($messages as $type => $items): ?>
                    <?php foreach ($items as $message): ?>
                        <p class="site-alert site-alert-<?php echo h($type); ?>"><?php echo h($message); ?></p>
                    <?php endforeach; ?>
                <?php endforeach; ?>

                <span class="page-kicker">Overview</span>
                <h1><?php echo h($listing['title']); ?></h1>
                <p><?php echo h($listing['description']); ?></p>

                <div class="page-grid compact-grid detail-feature-grid">
                    <article class="page-card"><div class="page-card-body"><h3>Category</h3><p><?php echo h($listing['category_name']); ?></p></div></article>
                    <article class="page-card"><div class="page-card-body"><h3>Location</h3><p><?php echo h($listing['location']); ?></p></div></article>
                    <article class="page-card"><div class="page-card-body"><h3>Rating</h3><p><?php echo h((string) $listing['rating']); ?> from <?php echo (int) $listing['review_count']; ?> reviews</p></div></article>
                </div>

                <div class="detail-note">
                    <h2>What to expect</h2>
                    <p>This profile is prepared for richer production details such as amenities, availability, gallery images, policies, map position, and verified provider information.</p>
                </div>

                <div class="detail-note">
                    <h2>Leave a review</h2>
                    <?php if ($authUser): ?>
                        <form class="inline-action-form" action="review-action.php" method="post">
                            <input type="hidden" name="listing_id" value="<?php echo (int) $listing['id']; ?>">
                            <select name="rating" required>
                                <option value="">Choose rating</option>
                                <option value="5">5 stars</option>
                                <option value="4">4 stars</option>
                                <option value="3">3 stars</option>
                                <option value="2">2 stars</option>
                                <option value="1">1 star</option>
                            </select>
                            <textarea name="comment" placeholder="Share your experience" required></textarea>
                            <button class="page-btn page-btn-primary" type="submit">submit review</button>
                        </form>
                    <?php else: ?>
                        <p><a href="login.php">Sign in</a> to review this listing.</p>
                    <?php endif; ?>
                </div>
            </article>

            <aside class="listing-detail-panel">
                <span class="panel-label">Start here</span>
                <h2><?php echo h($listing['price_label']); ?></h2>
                <p>Contact the provider, save this listing, or start a booking request.</p>
                <a class="page-btn page-btn-primary" href="contact.php">contact provider</a>
                <?php if ($authUser): ?>
                    <form action="bookmark-action.php" method="post">
                        <input type="hidden" name="listing_id" value="<?php echo (int) $listing['id']; ?>">
                        <input type="hidden" name="redirect" value="listing.php?id=<?php echo (int) $listing['id']; ?>">
                        <input type="hidden" name="action" value="toggle">
                        <button class="page-btn page-btn-soft bookmark-toggle <?php echo $isSaved ? 'is-saved' : ''; ?>" type="submit">
                            <i class="fa-solid fa-heart"></i>
                            <?php echo $isSaved ? 'saved' : 'save listing'; ?>
                        </button>
                    </form>
                <?php else: ?>
                    <a class="page-btn page-btn-soft" href="login.php?redirect=<?php echo urlencode('listing.php?id=' . (int) $listing['id']); ?>">sign in to save</a>
                <?php endif; ?>
                <div class="panel-mini-list">
                    <span><i class="fa-solid fa-location-dot"></i> <?php echo h($listing['location']); ?></span>
                    <span><i class="fa-solid fa-star"></i> <?php echo h((string) $listing['rating']); ?> average rating</span>
                    <span><i class="fa-solid fa-comment"></i> <?php echo (int) $listing['review_count']; ?> reviews</span>
                </div>

                <div class="booking-box">
                    <h3>Request booking</h3>
                    <?php if ($authUser): ?>
                        <form action="booking-action.php" method="post">
                            <input type="hidden" name="listing_id" value="<?php echo (int) $listing['id']; ?>">
                            <input type="date" name="booking_date" required>
                            <input type="number" name="guests" min="1" value="1" required>
                            <textarea name="note" placeholder="Add a note"></textarea>
                            <button class="page-btn page-btn-primary" type="submit">request booking</button>
                        </form>
                    <?php else: ?>
                        <p><a href="login.php">Sign in</a> to request a booking.</p>
                    <?php endif; ?>
                </div>
            </aside>
        </div>
    </section>
<?php endif; ?>

<?php include_once "assets/extras/footer.php"; ?>
