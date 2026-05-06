CREATE DATABASE IF NOT EXISTS listeo
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE listeo;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    slug VARCHAR(140) NOT NULL UNIQUE,
    description TEXT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS listings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NOT NULL,
    title VARCHAR(160) NOT NULL,
    slug VARCHAR(180) NOT NULL UNIQUE,
    location VARCHAR(160) NOT NULL,
    price_label VARCHAR(80) NOT NULL,
    rating DECIMAL(2,1) NOT NULL DEFAULT 0.0,
    review_count INT UNSIGNED NOT NULL DEFAULT 0,
    image_path VARCHAR(255) NOT NULL,
    short_description VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('draft', 'published') NOT NULL DEFAULT 'published',
    featured TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT listings_category_id_foreign FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

INSERT IGNORE INTO categories (name, slug, description, image_path, sort_order) VALUES
('Eat & Drink', 'eat-drink', 'Restaurants, cafes, kitchens, lounges, and food vendors with rich listing details.', 'assets/images/categories/categories1.jpg', 1),
('Apartments', 'apartments', 'Short-stay and long-stay homes, featured with images, amenities, and locations.', 'assets/images/new-home/new-home-2.jpg', 2),
('Events', 'events', 'Event spaces, planners, vendors, and experiences for memorable gatherings.', 'assets/images/categories/categories4.jpg', 3),
('Fitness', 'fitness', 'Coaches, gyms, wellness services, and programs for active lifestyles.', 'assets/images/categories/categories5.jpg', 4),
('Cars', 'cars', 'Vehicle rentals, rides, logistics, and mobility services for everyday movement.', 'assets/images/categories/categories2.jpg', 5),
('Everyday Help', 'everyday-help', 'Laundry, cleaning, errands, and practical support from trusted providers.', 'assets/images/categories/categories6.jpg', 6);

INSERT IGNORE INTO listings (category_id, title, slug, location, price_label, rating, review_count, image_path, short_description, description, featured) VALUES
((SELECT id FROM categories WHERE slug = 'apartments'), 'Urban comfort suite', 'urban-comfort-suite', 'Victoria Island', 'From ₦85,000/night', 4.8, 24, 'assets/images/visited-places/visited-places-1.jpg', 'Modern stay with easy access to food, transport, and local attractions.', 'A polished city apartment for travellers who want comfort, privacy, and quick access to restaurants, business districts, and weekend experiences.', 1),
((SELECT id FROM categories WHERE slug = 'eat-drink'), 'The dinner room', 'the-dinner-room', 'Lekki', 'Open today', 4.7, 31, 'assets/images/food-gallery/food-gallery-img-1.webp', 'A polished food experience for casual meals, dates, and small gatherings.', 'A warm dining spot built for simple reservations, quality meals, and relaxed evenings with friends, family, or clients.', 1),
((SELECT id FROM categories WHERE slug = 'events'), 'Garden event space', 'garden-event-space', 'Ikeja', 'Custom quote', 4.9, 18, 'assets/images/location/location4.jpeg', 'Flexible outdoor venue for birthdays, launches, and private experiences.', 'A flexible outdoor setting with room for small ceremonies, birthdays, launches, and curated private experiences.', 1),
((SELECT id FROM categories WHERE slug = 'fitness'), 'Pulse fitness coach', 'pulse-fitness-coach', 'Yaba', 'From ₦15,000/session', 4.6, 14, 'assets/images/categories/categories5.jpg', 'Personal training and wellness coaching for busy schedules.', 'A personal coaching service for users who need flexible sessions, habit support, and measurable progress.', 0),
((SELECT id FROM categories WHERE slug = 'cars'), 'City ride rentals', 'city-ride-rentals', 'Surulere', 'From ₦45,000/day', 4.5, 11, 'assets/images/categories/categories2.jpg', 'Clean vehicle rentals for errands, events, and weekend movement.', 'A simple car rental option for local movement, business errands, and planned weekend experiences.', 0),
((SELECT id FROM categories WHERE slug = 'everyday-help'), 'Fresh fold laundry', 'fresh-fold-laundry', 'Ajah', 'From ₦7,500/order', 4.7, 20, 'assets/images/categories/categories6.jpg', 'Pickup laundry service with neat folding and reliable turnaround.', 'An everyday help service for laundry pickup, cleaning support, and convenient home routines.', 0);
