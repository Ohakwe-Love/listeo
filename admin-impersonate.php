<?php

declare(strict_types=1);

require_once __DIR__ . '/assets/extras/includes/management.inc.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('admin-users.php');
}

$admin = current_user();
$userId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
$reason = trim($_POST['reason'] ?? '');

if (!$userId || $reason === '') {
    flash('error', 'A target user and reason are required.');
    redirect_to('admin-users.php');
}

try {
    start_impersonation((int) $admin['id'], (int) $userId, $reason);
    flash('success', 'Impersonation started. Every action should be treated as sensitive.');
    redirect_to('user-dashboard.php');
} catch (Throwable $exception) {
    flash('error', $exception->getMessage());
    redirect_to('admin-users.php');
}
