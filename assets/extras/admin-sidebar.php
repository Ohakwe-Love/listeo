<?php $activeAdminPage = $activeAdminPage ?? ''; ?>
<aside class="admin-sidebar">
    <div class="admin-brand">
        <h2>Listeo Admin</h2>
        <p>Strict operational controls</p>
    </div>
    <nav class="admin-nav">
        <a class="<?php echo $activeAdminPage === 'dashboard' ? 'active' : ''; ?>" href="admin-dashboard.php"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
        <a class="<?php echo $activeAdminPage === 'listings' ? 'active' : ''; ?>" href="admin-listings.php"><i class="fa-solid fa-building"></i> Listings</a>
        <a class="<?php echo $activeAdminPage === 'users' ? 'active' : ''; ?>" href="admin-users.php"><i class="fa-solid fa-users"></i> Users</a>
        <a class="<?php echo $activeAdminPage === 'audit' ? 'active' : ''; ?>" href="admin-audit.php"><i class="fa-solid fa-file-shield"></i> Audit logs</a>
        <a href="user-dashboard.php"><i class="fa-solid fa-user"></i> User area</a>
        <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </nav>
</aside>
