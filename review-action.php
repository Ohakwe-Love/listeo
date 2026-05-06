<?php

declare(strict_types=1);

require_once __DIR__ . '/assets/extras/includes/account.inc.php';

require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('listings.php');
}

$user = current_user();
$listingId = filter_input(INPUT_POST, 'listing_id', FILTER_VALIDATE_INT);
$rating = (int) ($_POST['rating'] ?? 0);
$comment = trim($_POST['comment'] ?? '');
$redirect = $listingId ? "listing.php?id={$listingId}" : 'listings.php';

if (!$listingId || !find_listing((int) $listingId)) {
    flash('error', 'Listing not found.');
    redirect_to($redirect);
}

if ($rating < 1 || $rating > 5 || $comment === '') {
    flash('error', 'Please add a rating and review comment.');
    redirect_to($redirect);
}

try {
    create_or_update_review((int) $user['id'], (int) $listingId, $rating, $comment);
    flash('success', 'Review saved.');
} catch (Throwable $exception) {
    flash('error', 'Could not save review. Import the latest database.sql and try again.');
}

redirect_to('user-reviews.php');
