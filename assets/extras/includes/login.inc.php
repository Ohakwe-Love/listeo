<?php

declare(strict_types=1);

require_once __DIR__ . '/function.inc.php';
require_once __DIR__ . '/db.inc.php';

start_secure_session();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('../../../login.php');
}

$email = trim($_POST['email'] ?? '');
$password = (string) ($_POST['password'] ?? '');

remember_old_input(['email' => $email]);

if ($email === '' || $password === '') {
    flash('error', 'Email and password are required.');
    redirect_to('../../../login.php');
}

try {
    $pdo = db();
    $statement = $pdo->prepare('SELECT id, name, email, password_hash FROM users WHERE email = :email LIMIT 1');
    $statement->execute(['email' => $email]);
    $user = $statement->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        flash('error', 'Invalid email or password.');
        redirect_to('../../../login.php');
    }

    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id' => (int) $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
    ];

    unset($_SESSION['old']);
    redirect_to('../../../index.php');
} catch (PDOException $exception) {
    flash('error', 'Could not sign you in. Check the database setup and try again.');
    redirect_to('../../../login.php');
}
