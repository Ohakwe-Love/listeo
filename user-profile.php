<?php
    require_once __DIR__ . "/assets/extras/includes/function.inc.php";
    require_auth();

    $extraStyles = ['assets/css/user.css'];
    $activeUserPage = 'profile';
    $user = current_user();
    $messages = get_flash_messages();
?>
<?php include_once "assets/extras/header.php"; ?>

<section class="user-shell">
    <div class="user-layout">
        <?php include_once "assets/extras/user-sidebar.php"; ?>

        <div class="user-main">
            <section class="user-hero">
                <div>
                    <span class="user-kicker">Profile</span>
                    <h1>Your account details and preferences.</h1>
                    <p>Keep your personal information ready for bookings, support, and provider communication.</p>
                </div>
                <div class="user-hero-card"><strong>Verified</strong><span>Email account ready</span></div>
            </section>

            <?php foreach ($messages as $type => $items): ?>
                <?php foreach ($items as $message): ?>
                    <p class="user-alert user-alert-<?php echo h($type); ?>"><?php echo h($message); ?></p>
                <?php endforeach; ?>
            <?php endforeach; ?>

            <div class="profile-layout" style="margin-top: 24px;">
                <article class="profile-card">
                    <h2>Account summary</h2>
                    <div class="profile-row"><span>Name</span><strong><?php echo h($user['name']); ?></strong></div>
                    <div class="profile-row"><span>Email</span><strong><?php echo h($user['email']); ?></strong></div>
                    <div class="profile-row"><span>Role</span><strong>Customer</strong></div>
                    <div class="profile-row"><span>Status</span><strong>Active</strong></div>
                </article>

                <article class="profile-card">
                    <h2>Edit profile</h2>
                    <form class="user-form" action="profile-action.php" method="post">
                        <input type="text" name="name" value="<?php echo h($user['name']); ?>" placeholder="Full name">
                        <input type="email" name="email" value="<?php echo h($user['email']); ?>" placeholder="Email address">
                        <input type="tel" name="phone" placeholder="Phone number">
                        <textarea name="bio" placeholder="Short note for providers"></textarea>
                        <button class="user-btn" type="submit">Save changes</button>
                    </form>
                </article>
            </div>
        </div>
    </div>
</section>

<?php include_once "assets/extras/footer.php"; ?>
