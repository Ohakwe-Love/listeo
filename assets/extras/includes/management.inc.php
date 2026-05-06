<?php

declare(strict_types=1);

require_once __DIR__ . '/account.inc.php';

function listing_plans(): array
{
    return [
        'basic' => ['name' => 'Basic', 'amount' => 5000.00],
        'standard' => ['name' => 'Standard', 'amount' => 12000.00],
        'premium' => ['name' => 'Premium', 'amount' => 25000.00],
    ];
}

function make_slug(string $title): string
{
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $title), '-'));
    return $slug !== '' ? $slug : 'listing';
}

function unique_listing_slug(string $title): string
{
    $base = make_slug($title);
    $slug = $base;
    $count = 2;

    while (true) {
        $statement = db()->prepare('SELECT id FROM listings WHERE slug = :slug LIMIT 1');
        $statement->execute(['slug' => $slug]);

        if (!$statement->fetch()) {
            return $slug;
        }

        $slug = $base . '-' . $count;
        $count++;
    }
}

function uploaded_listing_image_path(?array $upload): string
{
    if (!$upload || (int) ($upload['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return 'assets/images/visited-places/visited-places-1.jpg';
    }

    if ((int) $upload['error'] !== UPLOAD_ERR_OK) {
        throw new InvalidArgumentException('The cover image could not be uploaded. Please try again.');
    }

    if ((int) $upload['size'] > 3 * 1024 * 1024) {
        throw new InvalidArgumentException('The cover image must be 3MB or smaller.');
    }

    $extension = strtolower(pathinfo((string) $upload['name'], PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    if (!in_array($extension, $allowedExtensions, true)) {
        throw new InvalidArgumentException('Please upload a JPG, PNG, WEBP, or GIF cover image.');
    }

    $directory = dirname(__DIR__, 2) . '/images/listings';

    if (!is_dir($directory) && !mkdir($directory, 0775, true)) {
        throw new RuntimeException('Could not prepare the listing image folder.');
    }

    $fileName = 'listing-' . bin2hex(random_bytes(8)) . '.' . $extension;
    $targetPath = $directory . '/' . $fileName;

    if (!move_uploaded_file((string) $upload['tmp_name'], $targetPath)) {
        throw new RuntimeException('The cover image could not be saved.');
    }

    return 'assets/images/listings/' . $fileName;
}

function create_lister_listing(int $userId, array $data): int
{
    $plans = listing_plans();
    $planKey = $data['plan'] ?? 'basic';
    $plan = $plans[$planKey] ?? $plans['basic'];
    $paymentReference = trim($data['payment_reference'] ?? '');
    $status = $paymentReference !== '' ? 'pending_review' : 'pending_payment';
    $paymentStatus = $paymentReference !== '' ? 'pending' : 'unpaid';

    $connection = db();
    $connection->beginTransaction();

    try {
        $statement = $connection->prepare(
            'INSERT INTO listings
            (owner_id, category_id, title, slug, location, price_label, image_path, short_description, description, status, payment_status)
            VALUES
            (:owner_id, :category_id, :title, :slug, :location, :price_label, :image_path, :short_description, :description, :status, :payment_status)'
        );
        $statement->execute([
            'owner_id' => $userId,
            'category_id' => (int) $data['category_id'],
            'title' => trim($data['title']),
            'slug' => unique_listing_slug(trim($data['title'])),
            'location' => trim($data['location']),
            'price_label' => trim($data['price_label']),
            'image_path' => trim($data['image_path']) ?: 'assets/images/visited-places/visited-places-1.jpg',
            'short_description' => trim($data['short_description']),
            'description' => trim($data['description']),
            'status' => $status,
            'payment_status' => $paymentStatus,
        ]);

        $listingId = (int) $connection->lastInsertId();
        $payment = $connection->prepare(
            'INSERT INTO listing_payments (listing_id, user_id, plan_name, amount, payment_reference, status)
             VALUES (:listing_id, :user_id, :plan_name, :amount, :payment_reference, :status)'
        );
        $payment->execute([
            'listing_id' => $listingId,
            'user_id' => $userId,
            'plan_name' => $plan['name'],
            'amount' => $plan['amount'],
            'payment_reference' => $paymentReference ?: null,
            'status' => 'pending',
        ]);

        $connection->prepare('UPDATE users SET role = "lister" WHERE id = :id AND role = "user"')->execute(['id' => $userId]);
        $connection->commit();
    } catch (Throwable $exception) {
        $connection->rollBack();
        throw $exception;
    }

    return $listingId;
}

function lister_listings(int $userId): array
{
    $statement = db()->prepare(
        'SELECT listings.*, categories.name AS category_name,
                listing_payments.plan_name, listing_payments.amount, listing_payments.status AS payment_review_status
         FROM listings
         INNER JOIN categories ON categories.id = listings.category_id
         LEFT JOIN listing_payments ON listing_payments.listing_id = listings.id
         WHERE listings.owner_id = :user_id
         ORDER BY listings.created_at DESC'
    );
    $statement->execute(['user_id' => $userId]);

    return $statement->fetchAll();
}

function admin_log(int $adminId, string $action, ?string $targetType = null, ?int $targetId = null, ?string $details = null): void
{
    $statement = db()->prepare(
        'INSERT INTO admin_audit_logs (admin_id, action, target_type, target_id, details)
         VALUES (:admin_id, :action, :target_type, :target_id, :details)'
    );
    $statement->execute([
        'admin_id' => $adminId,
        'action' => $action,
        'target_type' => $targetType,
        'target_id' => $targetId,
        'details' => $details,
    ]);
}

function admin_users(): array
{
    return db()->query('SELECT id, name, email, role, account_status, created_at FROM users ORDER BY created_at DESC')->fetchAll();
}

function admin_listings(): array
{
    return db()->query(
        'SELECT listings.*, users.name AS owner_name, users.email AS owner_email, categories.name AS category_name,
                listing_payments.plan_name, listing_payments.amount, listing_payments.payment_reference, listing_payments.status AS payment_review_status
         FROM listings
         INNER JOIN categories ON categories.id = listings.category_id
         LEFT JOIN users ON users.id = listings.owner_id
         LEFT JOIN listing_payments ON listing_payments.listing_id = listings.id
         ORDER BY listings.created_at DESC'
    )->fetchAll();
}

function admin_pending_listings(): array
{
    $statement = db()->prepare(
        'SELECT listings.*, users.name AS owner_name, categories.name AS category_name,
                listing_payments.plan_name, listing_payments.amount, listing_payments.payment_reference, listing_payments.status AS payment_review_status
         FROM listings
         INNER JOIN categories ON categories.id = listings.category_id
         LEFT JOIN users ON users.id = listings.owner_id
         LEFT JOIN listing_payments ON listing_payments.listing_id = listings.id
         WHERE listings.status IN ("pending_payment", "pending_review")
         ORDER BY listings.created_at DESC'
    );
    $statement->execute();

    return $statement->fetchAll();
}

function admin_counts(): array
{
    return [
        'users' => (int) db()->query('SELECT COUNT(*) FROM users')->fetchColumn(),
        'listers' => (int) db()->query('SELECT COUNT(*) FROM users WHERE role = "lister"')->fetchColumn(),
        'pending' => (int) db()->query('SELECT COUNT(*) FROM listings WHERE status IN ("pending_payment", "pending_review")')->fetchColumn(),
        'published' => (int) db()->query('SELECT COUNT(*) FROM listings WHERE status = "published"')->fetchColumn(),
    ];
}

function admin_set_listing_status(int $adminId, int $listingId, string $status, ?string $reason = null): void
{
    $paymentStatus = $status === 'published' ? 'paid' : null;
    $sql = 'UPDATE listings SET status = :status, rejection_reason = :reason';
    $params = ['status' => $status, 'reason' => $reason, 'id' => $listingId];

    if ($paymentStatus) {
        $sql .= ', payment_status = :payment_status';
        $params['payment_status'] = $paymentStatus;
    }

    $sql .= ' WHERE id = :id';
    $statement = db()->prepare($sql);
    $statement->execute($params);

    if ($status === 'published') {
        db()->prepare('UPDATE listing_payments SET status = "verified" WHERE listing_id = :listing_id')->execute(['listing_id' => $listingId]);
    }

    admin_log($adminId, 'listing_' . $status, 'listing', $listingId, $reason);
}

function find_user_by_id(int $userId): ?array
{
    $statement = db()->prepare('SELECT id, name, email, role, account_status FROM users WHERE id = :id LIMIT 1');
    $statement->execute(['id' => $userId]);
    $user = $statement->fetch();

    return $user ?: null;
}

function start_impersonation(int $adminId, int $userId, string $reason): void
{
    $target = find_user_by_id($userId);

    if (!$target || $target['role'] === 'admin') {
        throw new RuntimeException('Admins cannot impersonate missing users or other admins.');
    }

    if (trim($reason) === '') {
        throw new RuntimeException('A support/security reason is required.');
    }

    start_secure_session();
    $_SESSION['impersonator_admin'] = current_user();
    $_SESSION['user'] = [
        'id' => (int) $target['id'],
        'name' => $target['name'],
        'email' => $target['email'],
        'role' => $target['role'],
    ];

    $statement = db()->prepare(
        'INSERT INTO admin_impersonation_logs (admin_id, user_id, reason)
         VALUES (:admin_id, :user_id, :reason)'
    );
    $statement->execute(['admin_id' => $adminId, 'user_id' => $userId, 'reason' => $reason]);
    $_SESSION['impersonation_log_id'] = (int) db()->lastInsertId();

    admin_log($adminId, 'impersonation_started', 'user', $userId, $reason);
}

function stop_impersonation(): void
{
    start_secure_session();
    $admin = $_SESSION['impersonator_admin'] ?? null;
    $logId = $_SESSION['impersonation_log_id'] ?? null;
    $impersonated = $_SESSION['user'] ?? null;

    if (!$admin) {
        return;
    }

    if ($logId) {
        db()->prepare('UPDATE admin_impersonation_logs SET ended_at = CURRENT_TIMESTAMP WHERE id = :id')->execute(['id' => $logId]);
    }

    $_SESSION['user'] = $admin;
    unset($_SESSION['impersonator_admin'], $_SESSION['impersonation_log_id']);

    if ($impersonated) {
        admin_log((int) $admin['id'], 'impersonation_ended', 'user', (int) $impersonated['id']);
    }
}
