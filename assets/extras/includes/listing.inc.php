<?php

declare(strict_types=1);

require_once __DIR__ . '/function.inc.php';
require_once __DIR__ . '/db.inc.php';

function fallback_categories(): array
{
    return [
        ['id' => 1, 'name' => 'Eat & Drink', 'slug' => 'eat-drink', 'description' => 'Restaurants, cafes, kitchens, lounges, and food vendors with rich listing details.', 'image_path' => 'assets/images/categories/categories1.jpg'],
        ['id' => 2, 'name' => 'Apartments', 'slug' => 'apartments', 'description' => 'Short-stay and long-stay homes, featured with images, amenities, and locations.', 'image_path' => 'assets/images/new-home/new-home-2.jpg'],
        ['id' => 3, 'name' => 'Events', 'slug' => 'events', 'description' => 'Event spaces, planners, vendors, and experiences for memorable gatherings.', 'image_path' => 'assets/images/categories/categories4.jpg'],
        ['id' => 4, 'name' => 'Fitness', 'slug' => 'fitness', 'description' => 'Coaches, gyms, wellness services, and programs for active lifestyles.', 'image_path' => 'assets/images/categories/categories5.jpg'],
        ['id' => 5, 'name' => 'Cars', 'slug' => 'cars', 'description' => 'Vehicle rentals, rides, logistics, and mobility services for everyday movement.', 'image_path' => 'assets/images/categories/categories2.jpg'],
        ['id' => 6, 'name' => 'Everyday Help', 'slug' => 'everyday-help', 'description' => 'Laundry, cleaning, errands, and practical support from trusted providers.', 'image_path' => 'assets/images/categories/categories6.jpg'],
    ];
}

function fallback_listings(): array
{
    return [
        ['id' => 1, 'category_name' => 'Apartments', 'category_slug' => 'apartments', 'title' => 'Urban comfort suite', 'slug' => 'urban-comfort-suite', 'location' => 'Victoria Island', 'price_label' => 'From ₦85,000/night', 'rating' => '4.8', 'review_count' => 24, 'image_path' => 'assets/images/visited-places/visited-places-1.jpg', 'short_description' => 'Modern stay with easy access to food, transport, and local attractions.', 'description' => 'A polished city apartment for travellers who want comfort, privacy, and quick access to restaurants, business districts, and weekend experiences.'],
        ['id' => 2, 'category_name' => 'Eat & Drink', 'category_slug' => 'eat-drink', 'title' => 'The dinner room', 'slug' => 'the-dinner-room', 'location' => 'Lekki', 'price_label' => 'Open today', 'rating' => '4.7', 'review_count' => 31, 'image_path' => 'assets/images/food-gallery/food-gallery-img-1.webp', 'short_description' => 'A polished food experience for casual meals, dates, and small gatherings.', 'description' => 'A warm dining spot built for simple reservations, quality meals, and relaxed evenings with friends, family, or clients.'],
        ['id' => 3, 'category_name' => 'Events', 'category_slug' => 'events', 'title' => 'Garden event space', 'slug' => 'garden-event-space', 'location' => 'Ikeja', 'price_label' => 'Custom quote', 'rating' => '4.9', 'review_count' => 18, 'image_path' => 'assets/images/location/location4.jpeg', 'short_description' => 'Flexible outdoor venue for birthdays, launches, and private experiences.', 'description' => 'A flexible outdoor setting with room for small ceremonies, birthdays, launches, and curated private experiences.'],
    ];
}

function all_categories(): array
{
    try {
        $statement = db()->query('SELECT id, name, slug, description, image_path FROM categories ORDER BY sort_order, name');
        return $statement->fetchAll();
    } catch (Throwable $exception) {
        return [];
    }
}

function all_listings(?string $categorySlug = null): array
{
    try {
        $sql = 'SELECT listings.*, categories.name AS category_name, categories.slug AS category_slug
                FROM listings
                INNER JOIN categories ON categories.id = listings.category_id
                WHERE listings.status = "published"';
        $params = [];

        if ($categorySlug) {
            $sql .= ' AND categories.slug = :category_slug';
            $params['category_slug'] = $categorySlug;
        }

        $sql .= ' ORDER BY listings.featured DESC, listings.created_at DESC, listings.title';
        $statement = db()->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll();
    } catch (Throwable $exception) {
        return [];
    }
}

function find_listing(int $id): ?array
{
    try {
        $statement = db()->prepare(
            'SELECT listings.*, categories.name AS category_name, categories.slug AS category_slug
             FROM listings
             INNER JOIN categories ON categories.id = listings.category_id
             WHERE listings.id = :id AND listings.status = "published"
             LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        $listing = $statement->fetch();

        return $listing ?: null;
    } catch (Throwable $exception) {
        return null;
    }
}

function find_fallback_listing(int $id): ?array
{
    foreach (fallback_listings() as $listing) {
        if ((int) $listing['id'] === $id) {
            return $listing;
        }
    }

    return null;
}
