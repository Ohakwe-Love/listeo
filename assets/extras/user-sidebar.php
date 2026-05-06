<?php
    $sidebarUser = current_user();
    $initial = strtoupper(substr($sidebarUser['name'] ?? 'U', 0, 1));
    $activeUserPage = $activeUserPage ?? '';
?>

<aside class="user-sidebar">
    <div class="user-profile-mini">
        <div class="user-avatar"><?php echo h($initial); ?></div>
        <h3><?php echo h($sidebarUser['name'] ?? 'User'); ?></h3>
        <p><?php echo h($sidebarUser['email'] ?? ''); ?></p>
    </div>

    <nav class="user-nav">
        <a class="<?php echo $activeUserPage === 'dashboard' ? 'active' : ''; ?>" href="user-dashboard.php"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
        <a class="<?php echo $activeUserPage === 'profile' ? 'active' : ''; ?>" href="user-profile.php"><i class="fa-solid fa-user"></i> Profile</a>
        <a class="<?php echo $activeUserPage === 'bookmarks' ? 'active' : ''; ?>" href="user-bookmarks.php"><i class="fa-solid fa-heart"></i> Bookmarks</a>
        <a class="<?php echo $activeUserPage === 'bookings' ? 'active' : ''; ?>" href="user-bookings.php"><i class="fa-solid fa-calendar-check"></i> Bookings</a>
        <a class="<?php echo $activeUserPage === 'reviews' ? 'active' : ''; ?>" href="user-reviews.php"><i class="fa-solid fa-star"></i> Reviews</a>
        <a class="<?php echo $activeUserPage === 'listings' ? 'active' : ''; ?>" href="user-listings.php"><i class="fa-solid fa-building"></i> My listings</a>
        <?php if (is_admin()): ?>
            <a href="admin-dashboard.php"><i class="fa-solid fa-shield-halved"></i> Admin</a>
        <?php endif; ?>
        <a href="listings.php"><i class="fa-solid fa-magnifying-glass"></i> Browse</a>
        <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </nav>
</aside>
