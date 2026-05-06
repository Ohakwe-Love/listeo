<?php

declare(strict_types=1);

require_once __DIR__ . '/assets/extras/includes/management.inc.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('admin-listings.php');
}

$admin = current_user();
$listingId = filter_input(INPUT_POST, 'listing_id', FILTER_VALIDATE_INT);
$action = $_POST['action'] ?? '';

if (!$listingId) {
    flash('error', 'Invalid listing.');
    redirect_to('admin-listings.php');
}

try {
    if ($action === 'approve') {
        admin_set_listing_status((int) $admin['id'], (int) $listingId, 'published');
        flash('success', 'Listing approved and published.');
    } elseif ($action === 'reject') {
        admin_set_listing_status((int) $admin['id'], (int) $listingId, 'rejected', trim($_POST['reason'] ?? 'Rejected by admin.'));
        flash('success', 'Listing rejected.');
    }
} catch (Throwable $exception) {
    flash('error', 'Could not update listing. Import the latest database.sql and try again.');
}

redirect_to('admin-listings.php');
