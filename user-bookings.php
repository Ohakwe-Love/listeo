<?php
    require_once __DIR__ . "/assets/extras/includes/account.inc.php";
    require_auth();

    $extraStyles = ['assets/css/user.css'];
    $activeUserPage = 'bookings';
    $user = current_user();
    $bookings = user_bookings((int) $user['id']);
    $messages = get_flash_messages();
?>
<?php include_once "assets/extras/header.php"; ?>

<section class="user-shell">
    <div class="user-layout">
        <?php include_once "assets/extras/user-sidebar.php"; ?>

        <div class="user-main">
            <section class="user-hero">
                <div>
                    <span class="user-kicker">Bookings</span>
                    <h1>Track requests, confirmations, and completed plans.</h1>
                    <p>Booking requests are stored for your account and ready for provider follow-up.</p>
                </div>
                <div class="user-hero-card"><strong><?php echo count($bookings); ?></strong><span>Total bookings</span></div>
            </section>

            <?php foreach ($messages as $type => $items): ?>
                <?php foreach ($items as $message): ?>
                    <p class="user-alert user-alert-<?php echo h($type); ?>"><?php echo h($message); ?></p>
                <?php endforeach; ?>
            <?php endforeach; ?>

            <div class="user-section-head">
                <div><h2>Booking activity</h2><p>Rows are loaded from the `bookings` table.</p></div>
                <a class="user-btn" href="listings.php">Find a place</a>
            </div>

            <?php if (!$bookings): ?>
                <div class="user-empty"><h3>No bookings yet</h3><p>Open any listing and submit a booking request.</p><a class="user-btn" href="listings.php">Browse listings</a></div>
            <?php else: ?>
                <table class="user-table">
                    <thead><tr><th>Listing</th><th>Category</th><th>Date</th><th>Guests</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><?php echo h($booking['title']); ?></td>
                                <td><?php echo h($booking['category_name']); ?></td>
                                <td><?php echo h(date('M j, Y', strtotime($booking['booking_date']))); ?></td>
                                <td><?php echo (int) $booking['guests']; ?></td>
                                <td><span class="status-pill <?php echo $booking['status'] === 'pending' ? 'pending' : ''; ?>"><?php echo h(ucfirst($booking['status'])); ?></span></td>
                                <td><a class="user-btn soft" href="listing.php?id=<?php echo (int) $booking['listing_id']; ?>">View</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include_once "assets/extras/footer.php"; ?>
