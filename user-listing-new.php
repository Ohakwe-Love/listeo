<?php
    require_once __DIR__ . "/assets/extras/includes/management.inc.php";
    require_lister();

    $extraStyles = ['assets/css/user.css'];
    $activeUserPage = 'listings';
    $categories = all_categories();
    $plans = listing_plans();
    $messages = get_flash_messages();
?>
<?php include_once "assets/extras/header.php"; ?>

<section class="user-shell">
    <div class="user-layout">
        <?php include_once "assets/extras/user-sidebar.php"; ?>

        <div class="user-main">
            <section class="user-hero">
                <div>
                    <span class="user-kicker">List a property</span>
                    <h1>Submit your house after choosing a paid listing plan.</h1>
                    <p>Add the listing details, upload a cover image, and include a payment reference. Admin verifies everything before publishing.</p>
                </div>
                <div class="user-hero-card"><strong>Paid</strong><span>Admin-reviewed listing</span></div>
            </section>

            <?php foreach ($messages as $type => $items): ?>
                <?php foreach ($items as $message): ?>
                    <p class="user-alert user-alert-<?php echo h($type); ?>"><?php echo h($message); ?></p>
                <?php endforeach; ?>
            <?php endforeach; ?>

            <div class="profile-card" style="margin-top: 24px;">
                <h2>Listing details</h2>
                <form class="user-form" action="lister-submit-action.php" method="post" enctype="multipart/form-data">
                    <select name="category_id" required>
                        <option value="">Choose category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo (int) $category['id']; ?>"><?php echo h($category['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="text" name="title" placeholder="Property/listing title" required>
                    <input type="text" name="location" placeholder="Location" required>
                    <input type="text" name="price_label" placeholder="Price label, e.g. From NGN 80,000/night" required>
                    <label class="user-file-field">
                        <span>Cover image</span>
                        <input type="file" name="listing_image" accept="image/jpeg,image/png,image/webp,image/gif">
                    </label>
                    <textarea name="short_description" placeholder="Short description" required></textarea>
                    <textarea name="description" placeholder="Full description" required></textarea>
                    <select name="plan" required>
                        <?php foreach ($plans as $key => $plan): ?>
                            <option value="<?php echo h($key); ?>"><?php echo h($plan['name']); ?> - NGN <?php echo number_format((float) $plan['amount'], 2); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="text" name="payment_reference" placeholder="Payment reference/proof code">
                    <button class="user-btn" type="submit">Submit for review</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include_once "assets/extras/footer.php"; ?>
