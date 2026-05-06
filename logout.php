<?php

declare(strict_types=1);

require_once __DIR__ . '/assets/extras/includes/function.inc.php';

start_secure_session();
$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'] ?? '', (bool) $params['secure'], (bool) $params['httponly']);
}

session_destroy();
redirect_to('index.php');
