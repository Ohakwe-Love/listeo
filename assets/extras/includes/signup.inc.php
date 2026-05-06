<?php

declare(strict_types=1);

require_once __DIR__ . '/function.inc.php';
require_once __DIR__ . '/db.inc.php';

start_secure_session();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('../../../signup.php');
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = (string) ($_POST['password'] ?? '');
$role = ($_POST['account_type'] ?? 'user') === 'lister' ? 'lister' : 'user';

remember_old_input(['name' => $name, 'email' => $email]);

if ($name === '' || $email === '' || $password === '') {
    flash('error', 'All fields are required.');
    redirect_to('../../../signup.php');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    flash('error', 'Please enter a valid email address.');
    redirect_to('../../../signup.php');
}

if (strlen($password) < 8) {
    flash('error', 'Password must be at least 8 characters.');
    redirect_to('../../../signup.php');
}

try {
    $pdo = db();

    $checkUser = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
    $checkUser->execute(['email' => $email]);

    if ($checkUser->fetch()) {
        flash('error', 'An account with that email already exists.');
        redirect_to('../../../signup.php');
    }

    $createUser = $pdo->prepare(
        'INSERT INTO users (name, email, password_hash, role) VALUES (:name, :email, :password_hash, :role)'
    );

    $createUser->execute([
        'name' => $name,
        'email' => $email,
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        'role' => $role,
    ]);

    unset($_SESSION['old']);
    flash('success', 'Account created. You can sign in now.');
    redirect_to('../../../login.php');
} catch (PDOException $exception) {
    flash('error', 'Could not create your account. Check the database setup and try again.');
    redirect_to('../../../signup.php');
}
