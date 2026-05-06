<?php

declare(strict_types=1);

function start_secure_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();
}

function redirect_to(string $path): never
{
    header("Location: {$path}");
    exit;
}

function safe_redirect_path(?string $path, string $fallback = 'index.php'): string
{
    $path = trim((string) $path);

    if ($path === '' || str_starts_with($path, '//') || preg_match('/^[a-z][a-z0-9+.-]*:/i', $path)) {
        return $fallback;
    }

    if (str_contains($path, "\r") || str_contains($path, "\n")) {
        return $fallback;
    }

    return ltrim($path, '/');
}

function h(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function flash(string $type, string $message): void
{
    start_secure_session();
    $_SESSION['flash'][$type][] = $message;
}

function get_flash_messages(): array
{
    start_secure_session();
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);

    return $messages;
}

function remember_old_input(array $input): void
{
    start_secure_session();
    $_SESSION['old'] = array_intersect_key($input, array_flip(['name', 'email']));
}

function old_input(string $key): string
{
    start_secure_session();
    $value = $_SESSION['old'][$key] ?? '';
    unset($_SESSION['old'][$key]);

    if (empty($_SESSION['old'])) {
        unset($_SESSION['old']);
    }

    return (string) $value;
}

function current_user(): ?array
{
    start_secure_session();
    return $_SESSION['user'] ?? null;
}

function original_admin(): ?array
{
    start_secure_session();
    return $_SESSION['impersonator_admin'] ?? null;
}

function is_impersonating(): bool
{
    return original_admin() !== null;
}

function user_role(): string
{
    return current_user()['role'] ?? 'user';
}

function is_admin(): bool
{
    return user_role() === 'admin';
}

function is_lister(): bool
{
    return in_array(user_role(), ['lister', 'admin'], true);
}

function require_guest(): void
{
    if (current_user() !== null) {
        redirect_to('../../../index.php');
    }
}

function require_auth(): void
{
    if (current_user() === null) {
        $currentPath = basename((string) ($_SERVER['SCRIPT_NAME'] ?? 'index.php'));
        $queryString = (string) ($_SERVER['QUERY_STRING'] ?? '');

        if ($currentPath !== 'login.php') {
            $_SESSION['intended_url'] = $currentPath . ($queryString !== '' ? '?' . $queryString : '');
        }

        flash('error', 'Please sign in to continue.');
        redirect_to('login.php' . (isset($_SESSION['intended_url']) ? '?redirect=' . urlencode((string) $_SESSION['intended_url']) : ''));
    }
}

function require_admin(): void
{
    require_auth();

    if (!is_admin()) {
        flash('error', 'Admin access only.');
        redirect_to('user-dashboard.php');
    }
}

function require_lister(): void
{
    require_auth();

    if (!is_lister()) {
        flash('error', 'Please use a lister account to manage listings.');
        redirect_to('user-dashboard.php');
    }
}
