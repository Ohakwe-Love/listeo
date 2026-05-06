<?php
    require_once __DIR__ . "/assets/extras/includes/listing.inc.php";

    $listingId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $listing = $listingId ? find_listing((int) $listingId) : null;
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
            </article>

            <aside class="listing-detail-panel">
                <span class="panel-label">Start here</span>
                <h2><?php echo h($listing['price_label']); ?></h2>
                <p>Contact the provider, save this listing, or start a booking request.</p>
                <a class="page-btn page-btn-primary" href="contact.php">contact provider</a>
                <a class="page-btn page-btn-soft" href="bookmarks.php">save listing</a>
                <div class="panel-mini-list">
                    <span><i class="fa-solid fa-location-dot"></i> <?php echo h($listing['location']); ?></span>
                    <span><i class="fa-solid fa-star"></i> <?php echo h((string) $listing['rating']); ?> average rating</span>
                    <span><i class="fa-solid fa-comment"></i> <?php echo (int) $listing['review_count']; ?> reviews</span>
                </div>
            </aside>
        </div>
    </section>
<?php endif; ?>

<?php include_once "assets/extras/footer.php"; ?>
