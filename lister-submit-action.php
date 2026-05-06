<?php

declare(strict_types=1);

require_once __DIR__ . '/assets/extras/includes/management.inc.php';

require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('user-listing-new.php');
}

$user = current_user();
$required = ['category_id', 'title', 'location', 'price_label', 'short_description', 'description', 'plan'];

foreach ($required as $field) {
    if (trim((string) ($_POST[$field] ?? '')) === '') {
        flash('error', 'Please complete all required listing fields.');
        redirect_to('user-listing-new.php');
    }
}

try {
    $listingData = $_POST;
    $listingData['image_path'] = uploaded_listing_image_path($_FILES['listing_image'] ?? null);

    $listingId = create_lister_listing((int) $user['id'], $listingData);
    flash('success', 'Listing submitted. Admin will review payment and listing details.');
    redirect_to('user-listings.php');
} catch (InvalidArgumentException $exception) {
    flash('error', $exception->getMessage());
    redirect_to('user-listing-new.php');
} catch (Throwable $exception) {
    flash('error', 'Could not submit listing. Import database-upgrade.sql and try again.');
    redirect_to('user-listing-new.php');
}
