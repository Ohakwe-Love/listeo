<?php

declare(strict_types=1);

require_once __DIR__ . '/assets/extras/includes/account.inc.php';

require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('listings.php');
}

$user = current_user();
$listingId = filter_input(INPUT_POST, 'listing_id', FILTER_VALIDATE_INT);
$bookingDate = trim($_POST['booking_date'] ?? '');
$guests = max(1, (int) ($_POST['guests'] ?? 1));
$note = trim($_POST['note'] ?? '');
$redirect = $listingId ? "listing.php?id={$listingId}" : 'listings.php';

if (!$listingId || !find_listing((int) $listingId)) {
    flash('error', 'Listing not found.');
    redirect_to($redirect);
}

if ($bookingDate === '') {
    flash('error', 'Please choose a booking date.');
    redirect_to($redirect);
}

try {
    create_booking((int) $user['id'], (int) $listingId, $bookingDate, $guests, $note);
    flash('success', 'Booking request created.');
} catch (Throwable $exception) {
    flash('error', 'Could not create booking. Import the latest database.sql and try again.');
}

redirect_to('user-bookings.php');
