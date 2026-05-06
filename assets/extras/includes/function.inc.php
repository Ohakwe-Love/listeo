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

function require_guest(): void
{
    if (current_user() !== null) {
        redirect_to('../../../index.php');
    }
}

function require_auth(): void
{
    if (current_user() === null) {
        flash('error', 'Please sign in to continue.');
        redirect_to('login.php');
    }
}
