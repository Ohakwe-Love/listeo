<?php
    require_once __DIR__ . "/assets/extras/includes/listing.inc.php";
    $categorySlug = trim($_GET['category'] ?? '') ?: null;
    $categories = all_categories();
    $listings = all_listings($categorySlug);
?>
<?php include_once "assets/extras/header.php"; ?>

<section class="page-hero listings-hero">
    <div class="page-inner">
        <span class="page-kicker">Listings</span>
        <h1>Browse featured places and services with confidence.</h1>
        <p>These cards are now loaded through the listing data layer, with a database table ready behind them once `database.sql` is imported.</p>
        <div class="page-actions">
            <a class="page-btn page-btn-primary" href="signup.php">create account</a>
            <a class="page-btn page-btn-light" href="bookmarks.php">view bookmarks</a>
        </div>
        <div class="hero-stats">
            <span><strong><?php echo count($listings); ?></strong> listings shown</span>
            <span><strong><?php echo count($categories); ?></strong> categories</span>
            <span><strong>4.7</strong> avg rating</span>
        </div>
    </div>
</section>

<section class="page-section">
    <div class="page-inner">
        <div class="section-heading-row">
            <div>
                <span class="page-kicker">Featured picks</span>
                <h1><?php echo $categorySlug ? 'Filtered listings' : 'Places worth exploring first.'; ?></h1>
            </div>
            <p>Scan by category, location, rating, and offer. Each card now leads into a styled listing profile.</p>
        </div>

        <div class="listing-control-bar">
            <div class="listing-search-shell">
                <i class="fa-solid fa-magnifying-glass"></i>
                <span>Search by service, area, or provider</span>
            </div>
            <a href="contact.php">Add a listing</a>
        </div>

        <div class="filter-pills">
            <a class="<?php echo $categorySlug ? '' : 'active-filter'; ?>" href="listings.php">All</a>
            <?php foreach ($categories as $category): ?>
                <a class="<?php echo $categorySlug === $category['slug'] ? 'active-filter' : ''; ?>" href="listings.php?category=<?php echo h($category['slug']); ?>">
                    <?php echo h($category['name']); ?>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if (!$listings): ?>
            <div class="empty-state">
                <h2>No listings found</h2>
                <p>Try another category or add listings to the database.</p>
            </div>
        <?php else: ?>
            <div class="listing-grid">
                <?php foreach ($listings as $listing): ?>
                    <article class="listing-card">
                        <a class="listing-image-link" href="listing.php?id=<?php echo (int) $listing['id']; ?>">
                            <img src="<?php echo h($listing['image_path']); ?>" alt="<?php echo h($listing['title']); ?>">
                            <span><?php echo h($listing['price_label']); ?></span>
                        </a>
                        <div class="listing-card-content">
                            <span class="listing-tag"><?php echo h($listing['category_name']); ?></span>
                            <h3><a href="listing.php?id=<?php echo (int) $listing['id']; ?>"><?php echo h($listing['title']); ?></a></h3>
                            <p><?php echo h($listing['short_description']); ?></p>
                            <div class="listing-meta">
                                <span><?php echo h($listing['location']); ?></span>
                                <span><i class="fa-solid fa-star"></i> <?php echo h((string) $listing['rating']); ?></span>
                            </div>
                            <a class="listing-card-action" href="listing.php?id=<?php echo (int) $listing['id']; ?>">View details</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include_once "assets/extras/footer.php"; ?>
