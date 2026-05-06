<?php

declare(strict_types=1);

require_once __DIR__ . '/assets/extras/includes/account.inc.php';

require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('listings.php');
}

$user = current_user();
$listingId = filter_input(INPUT_POST, 'listing_id', FILTER_VALIDATE_INT);
$action = (string) ($_POST['action'] ?? 'toggle');
$redirect = safe_redirect_path($_POST['redirect'] ?? ($listingId ? "listing.php?id={$listingId}" : 'listings.php'), 'listings.php');

if (!$listingId || !find_listing((int) $listingId)) {
    flash('error', 'Listing not found.');
    redirect_to($redirect);
}

try {
    $isSaved = is_bookmarked((int) $user['id'], (int) $listingId);

    if ($action === 'remove' || ($action === 'toggle' && $isSaved)) {
        remove_bookmark((int) $user['id'], (int) $listingId);
        flash('success', 'Bookmark removed.');
    } else {
        add_bookmark((int) $user['id'], (int) $listingId);
        flash('success', 'Listing saved to bookmarks.');
    }
} catch (Throwable $exception) {
    flash('error', 'Could not update bookmarks. Import database-upgrade.sql and try again.');
}

redirect_to($redirect);
