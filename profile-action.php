<?php

declare(strict_types=1);

require_once __DIR__ . '/assets/extras/includes/account.inc.php';

require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('user-profile.php');
}

$user = current_user();
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');

if ($name === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    flash('error', 'Please provide a valid name and email.');
    redirect_to('user-profile.php');
}

try {
    $statement = db()->prepare('UPDATE users SET name = :name, email = :email WHERE id = :id');
    $statement->execute(['name' => $name, 'email' => $email, 'id' => (int) $user['id']]);

    $_SESSION['user']['name'] = $name;
    $_SESSION['user']['email'] = $email;
    flash('success', 'Profile updated.');
} catch (Throwable $exception) {
    flash('error', 'Could not update profile. The email may already be in use.');
}

redirect_to('user-profile.php');
