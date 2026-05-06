<?php

declare(strict_types=1);

require_once __DIR__ . '/listing.inc.php';

function account_db_available(): bool
{
    try {
        db()->query('SELECT 1 FROM bookmarks LIMIT 1');
        db()->query('SELECT 1 FROM bookings LIMIT 1');
        db()->query('SELECT 1 FROM reviews LIMIT 1');
        return true;
    } catch (Throwable $exception) {
        return false;
    }
}

function account_table_available(string $table): bool
{
    $allowedTables = ['bookmarks', 'bookings', 'reviews'];

    if (!in_array($table, $allowedTables, true)) {
        return false;
    }

    try {
        db()->query("SELECT 1 FROM {$table} LIMIT 1");
        return true;
    } catch (Throwable $exception) {
        return false;
    }
}

function user_bookmarks(int $userId): array
{
    if (!account_table_available('bookmarks')) {
        return [];
    }

    $statement = db()->prepare(
        'SELECT bookmarks.id AS bookmark_id, bookmarks.created_at AS bookmarked_at,
                listings.*, categories.name AS category_name, categories.slug AS category_slug
         FROM bookmarks
         INNER JOIN listings ON listings.id = bookmarks.listing_id
         INNER JOIN categories ON categories.id = listings.category_id
         WHERE bookmarks.user_id = :user_id
         ORDER BY bookmarks.created_at DESC'
    );
    $statement->execute(['user_id' => $userId]);

    return $statement->fetchAll();
}

function is_bookmarked(int $userId, int $listingId): bool
{
    if (!account_table_available('bookmarks')) {
        return false;
    }

    $statement = db()->prepare('SELECT id FROM bookmarks WHERE user_id = :user_id AND listing_id = :listing_id LIMIT 1');
    $statement->execute(['user_id' => $userId, 'listing_id' => $listingId]);

    return (bool) $statement->fetch();
}

function add_bookmark(int $userId, int $listingId): void
{
    if (!account_table_available('bookmarks')) {
        throw new RuntimeException('Bookmarks are not ready yet.');
    }

    $statement = db()->prepare('INSERT IGNORE INTO bookmarks (user_id, listing_id) VALUES (:user_id, :listing_id)');
    $statement->execute(['user_id' => $userId, 'listing_id' => $listingId]);
}

function remove_bookmark(int $userId, int $listingId): void
{
    if (!account_table_available('bookmarks')) {
        throw new RuntimeException('Bookmarks are not ready yet.');
    }

    $statement = db()->prepare('DELETE FROM bookmarks WHERE user_id = :user_id AND listing_id = :listing_id');
    $statement->execute(['user_id' => $userId, 'listing_id' => $listingId]);
}

function user_bookings(int $userId): array
{
    if (!account_table_available('bookings')) {
        return [];
    }

    $statement = db()->prepare(
        'SELECT bookings.*, listings.title, listings.image_path, categories.name AS category_name
         FROM bookings
         INNER JOIN listings ON listings.id = bookings.listing_id
         INNER JOIN categories ON categories.id = listings.category_id
         WHERE bookings.user_id = :user_id
         ORDER BY bookings.booking_date DESC, bookings.created_at DESC'
    );
    $statement->execute(['user_id' => $userId]);

    return $statement->fetchAll();
}

function create_booking(int $userId, int $listingId, string $bookingDate, int $guests, string $note): void
{
    $statement = db()->prepare(
        'INSERT INTO bookings (user_id, listing_id, booking_date, guests, note)
         VALUES (:user_id, :listing_id, :booking_date, :guests, :note)'
    );
    $statement->execute([
        'user_id' => $userId,
        'listing_id' => $listingId,
        'booking_date' => $bookingDate,
        'guests' => $guests,
        'note' => $note,
    ]);
}

function user_reviews(int $userId): array
{
    if (!account_table_available('reviews')) {
        return [];
    }

    $statement = db()->prepare(
        'SELECT reviews.*, listings.title, listings.image_path, categories.name AS category_name
         FROM reviews
         INNER JOIN listings ON listings.id = reviews.listing_id
         INNER JOIN categories ON categories.id = listings.category_id
         WHERE reviews.user_id = :user_id
         ORDER BY reviews.created_at DESC'
    );
    $statement->execute(['user_id' => $userId]);

    return $statement->fetchAll();
}

function create_or_update_review(int $userId, int $listingId, int $rating, string $comment): void
{
    $statement = db()->prepare(
        'INSERT INTO reviews (user_id, listing_id, rating, comment)
         VALUES (:user_id, :listing_id, :rating, :comment)
         ON DUPLICATE KEY UPDATE rating = VALUES(rating), comment = VALUES(comment), status = "published", updated_at = CURRENT_TIMESTAMP'
    );
    $statement->execute([
        'user_id' => $userId,
        'listing_id' => $listingId,
        'rating' => $rating,
        'comment' => $comment,
    ]);
}

function account_counts(int $userId): array
{
    return [
        'bookmarks' => count(user_bookmarks($userId)),
        'bookings' => count(user_bookings($userId)),
        'reviews' => count(user_reviews($userId)),
    ];
}

function recent_account_activity(int $userId): array
{
    $activity = [];

    foreach (array_slice(user_bookmarks($userId), 0, 3) as $bookmark) {
        $activity[] = ['icon' => 'fa-heart', 'title' => 'Saved ' . $bookmark['title'], 'body' => 'You added a listing to bookmarks.', 'time' => $bookmark['bookmarked_at']];
    }

    foreach (array_slice(user_bookings($userId), 0, 3) as $booking) {
        $activity[] = ['icon' => 'fa-calendar', 'title' => 'Booking request for ' . $booking['title'], 'body' => ucfirst($booking['status']) . ' booking for ' . $booking['booking_date'] . '.', 'time' => $booking['created_at']];
    }

    foreach (array_slice(user_reviews($userId), 0, 3) as $review) {
        $activity[] = ['icon' => 'fa-star', 'title' => 'Reviewed ' . $review['title'], 'body' => $review['comment'], 'time' => $review['created_at']];
    }

    usort($activity, fn (array $a, array $b): int => strcmp((string) $b['time'], (string) $a['time']));

    return array_slice($activity, 0, 6);
}
