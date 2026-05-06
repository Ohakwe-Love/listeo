<?php
    require_once __DIR__ . "/assets/extras/includes/listing.inc.php";
    $categories = all_categories();
?>
<?php include_once "assets/extras/header.php"; ?>

<section class="page-hero services-hero">
    <div class="page-inner">
        <span class="page-kicker">Services</span>
        <h1>Everything users need, arranged into beautiful service categories.</h1>
        <p>From stays and meals to events and fitness, Listeo gives each service a clean lane for browsing, comparing, and booking.</p>
        <div class="page-actions">
            <a class="page-btn page-btn-primary" href="listings.php">explore listings</a>
            <a class="page-btn page-btn-light" href="contact.php">partner with us</a>
        </div>
        <div class="hero-stats">
            <span><strong><?php echo count($categories); ?>+</strong> categories</span>
            <span><strong>24/7</strong> discovery</span>
            <span><strong>Local</strong> providers</span>
        </div>
    </div>
</section>

<section class="page-section">
    <div class="page-inner">
        <div class="section-heading-row">
            <div>
                <span class="page-kicker">Popular services</span>
                <h1>Choose a lane and start exploring.</h1>
            </div>
            <p>Each category can hold live listings, location filters, ratings, and booking actions as the product grows.</p>
        </div>

        <div class="service-grid">
            <?php foreach ($categories as $category): ?>
                <article class="page-card service-card">
                    <img src="<?php echo h($category['image_path']); ?>" alt="<?php echo h($category['name']); ?>">
                    <div class="page-card-body">
                        <span class="service-chip">Explore</span>
                        <h3><?php echo h($category['name']); ?></h3>
                        <p><?php echo h($category['description']); ?></p>
                        <a class="page-card-link" href="listings.php?category=<?php echo h($category['slug']); ?>">View listings</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="page-section page-muted-band">
    <div class="page-inner split-feature">
        <div>
            <span class="page-kicker">How it works</span>
            <h1>Simple enough for users, structured enough for growth.</h1>
            <p>Listeo can move from category browsing to real booking flows without changing the visual direction of the product.</p>
        </div>
        <div class="process-list">
            <article><span>01</span><div><h3>Pick a category</h3><p>Users start with the service they need and see matching providers.</p></div></article>
            <article><span>02</span><div><h3>Compare listings</h3><p>Cards show price, rating, location, and a short value summary.</p></div></article>
            <article><span>03</span><div><h3>Take action</h3><p>Visitors can contact, save, review, or book as the app features mature.</p></div></article>
        </div>
    </div>
</section>

<?php include_once "assets/extras/footer.php"; ?>
