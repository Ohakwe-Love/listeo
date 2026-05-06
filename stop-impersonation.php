<?php

declare(strict_types=1);

require_once __DIR__ . '/assets/extras/includes/management.inc.php';

require_auth();

try {
    stop_impersonation();
    flash('success', 'Impersonation ended.');
} catch (Throwable $exception) {
    flash('error', 'Could not end impersonation cleanly.');
}

redirect_to('admin-dashboard.php');
