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
$redirect = safe_redirect_path($_POST['redirect'] ?? ($_SESSION['intended_url'] ?? ''), '');

remember_old_input(['email' => $email]);

if ($email === '' || $password === '') {
    flash('error', 'Email and password are required.');
    redirect_to('../../../login.php');
}

try {
    $pdo = db();
    $statement = $pdo->prepare('SELECT id, name, email, password_hash, role, account_status FROM users WHERE email = :email LIMIT 1');
    $statement->execute(['email' => $email]);
    $user = $statement->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        flash('error', 'Invalid email or password.');
        redirect_to('../../../login.php');
    }

    if (($user['account_status'] ?? 'active') !== 'active') {
        flash('error', 'This account is not active.');
        redirect_to('../../../login.php');
    }

    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id' => (int) $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'role' => $user['role'] ?? 'user',
    ];

    unset($_SESSION['old'], $_SESSION['intended_url']);

    if ($redirect !== '') {
        redirect_to('../../../' . $redirect);
    }

    redirect_to(($user['role'] ?? 'user') === 'admin' ? '../../../admin-dashboard.php' : '../../../user-dashboard.php');
} catch (PDOException $exception) {
    flash('error', 'Could not sign you in. Check the database setup and try again.');
    redirect_to('../../../login.php');
}
