CREATE DATABASE IF NOT EXISTS listeo
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE listeo;

DELIMITER $$

DROP PROCEDURE IF EXISTS add_column_if_missing $$
CREATE PROCEDURE add_column_if_missing(
    IN table_name_value VARCHAR(64),
    IN column_name_value VARCHAR(64),
    IN column_definition_value TEXT
)
BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = table_name_value
            AND COLUMN_NAME = column_name_value
    ) THEN
        SET @sql = CONCAT('ALTER TABLE `', table_name_value, '` ADD COLUMN ', column_definition_value);
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END $$

DROP PROCEDURE IF EXISTS add_fk_if_missing $$
CREATE PROCEDURE add_fk_if_missing(
    IN table_name_value VARCHAR(64),
    IN constraint_name_value VARCHAR(64),
    IN constraint_definition_value TEXT
)
BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.TABLE_CONSTRAINTS
        WHERE CONSTRAINT_SCHEMA = DATABASE()
            AND TABLE_NAME = table_name_value
            AND CONSTRAINT_NAME = constraint_name_value
    ) THEN
        SET @sql = CONCAT('ALTER TABLE `', table_name_value, '` ADD CONSTRAINT ', constraint_definition_value);
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END $$

DELIMITER ;

CALL add_column_if_missing('users', 'role', "`role` ENUM('user', 'lister', 'admin') NOT NULL DEFAULT 'user' AFTER `password_hash`");
CALL add_column_if_missing('users', 'account_status', "`account_status` ENUM('active', 'suspended') NOT NULL DEFAULT 'active' AFTER `role`");

CALL add_column_if_missing('listings', 'owner_id', "`owner_id` INT UNSIGNED NULL AFTER `id`");
CALL add_column_if_missing('listings', 'status', "`status` ENUM('draft', 'pending_payment', 'pending_review', 'published', 'rejected') NOT NULL DEFAULT 'published' AFTER `description`");
CALL add_column_if_missing('listings', 'payment_status', "`payment_status` ENUM('unpaid', 'pending', 'paid') NOT NULL DEFAULT 'paid' AFTER `status`");
CALL add_column_if_missing('listings', 'rejection_reason', "`rejection_reason` TEXT NULL AFTER `payment_status`");

ALTER TABLE listings
    MODIFY COLUMN status VARCHAR(40) NOT NULL DEFAULT 'published';

UPDATE listings
SET status = 'published'
WHERE status NOT IN ('draft', 'pending_payment', 'pending_review', 'published', 'rejected');

ALTER TABLE listings
    MODIFY COLUMN status ENUM('draft', 'pending_payment', 'pending_review', 'published', 'rejected') NOT NULL DEFAULT 'published';

CALL add_fk_if_missing('listings', 'listings_owner_id_foreign', '`listings_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `users`(`id`) ON DELETE SET NULL');

CREATE TABLE IF NOT EXISTS listing_payments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    listing_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    plan_name VARCHAR(80) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_reference VARCHAR(190) NULL,
    status ENUM('pending', 'verified', 'rejected') NOT NULL DEFAULT 'pending',
    admin_note TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT listing_payments_listing_id_foreign FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE,
    CONSTRAINT listing_payments_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS admin_audit_logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admin_id INT UNSIGNED NOT NULL,
    action VARCHAR(120) NOT NULL,
    target_type VARCHAR(80) NULL,
    target_id INT UNSIGNED NULL,
    details TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT admin_audit_logs_admin_id_foreign FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS admin_impersonation_logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admin_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    reason TEXT NOT NULL,
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ended_at TIMESTAMP NULL,
    CONSTRAINT admin_impersonation_logs_admin_id_foreign FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT admin_impersonation_logs_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS bookmarks (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    listing_id INT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY bookmarks_user_listing_unique (user_id, listing_id),
    CONSTRAINT bookmarks_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT bookmarks_listing_id_foreign FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS bookings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    listing_id INT UNSIGNED NOT NULL,
    booking_date DATE NOT NULL,
    guests INT UNSIGNED NOT NULL DEFAULT 1,
    note TEXT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT bookings_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT bookings_listing_id_foreign FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS reviews (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    listing_id INT UNSIGNED NOT NULL,
    rating TINYINT UNSIGNED NOT NULL,
    comment TEXT NOT NULL,
    status ENUM('pending', 'published') NOT NULL DEFAULT 'published',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY reviews_user_listing_unique (user_id, listing_id),
    CONSTRAINT reviews_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT reviews_listing_id_foreign FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE
);

INSERT IGNORE INTO users (name, email, password_hash, role) VALUES
('Listeo Admin', 'admin@listeo.local', '$2y$12$TfiARpJU7eiZaNNTa5CtO.HUC.1NOeHvrPljjXHgZzTVvojBP96hC', 'admin');

INSERT IGNORE INTO categories (name, slug, description, image_path, sort_order) VALUES
('Eat & Drink', 'eat-drink', 'Restaurants, cafes, kitchens, lounges, and food vendors with rich listing details.', 'assets/images/categories/categories1.jpg', 1),
('Apartments', 'apartments', 'Short-stay and long-stay homes, featured with images, amenities, and locations.', 'assets/images/new-home/new-home-2.jpg', 2),
('Events', 'events', 'Event spaces, planners, vendors, and experiences for memorable gatherings.', 'assets/images/categories/categories4.jpg', 3),
('Fitness', 'fitness', 'Coaches, gyms, wellness services, and programs for active lifestyles.', 'assets/images/categories/categories5.jpg', 4),
('Cars', 'cars', 'Vehicle rentals, rides, logistics, and mobility services for everyday movement.', 'assets/images/categories/categories2.jpg', 5),
('Everyday Help', 'everyday-help', 'Laundry, cleaning, errands, and practical support from trusted providers.', 'assets/images/categories/categories6.jpg', 6);

INSERT IGNORE INTO listings (category_id, title, slug, location, price_label, rating, review_count, image_path, short_description, description, status, payment_status, featured) VALUES
((SELECT id FROM categories WHERE slug = 'apartments'), 'Urban comfort suite', 'urban-comfort-suite', 'Victoria Island', 'From NGN 85,000/night', 4.8, 24, 'assets/images/visited-places/visited-places-1.jpg', 'Modern stay with easy access to food, transport, and local attractions.', 'A polished city apartment for travellers who want comfort, privacy, and quick access to restaurants, business districts, and weekend experiences.', 'published', 'paid', 1),
((SELECT id FROM categories WHERE slug = 'eat-drink'), 'The dinner room', 'the-dinner-room', 'Lekki', 'Open today', 4.7, 31, 'assets/images/food-gallery/food-gallery-img-1.webp', 'A polished food experience for casual meals, dates, and small gatherings.', 'A warm dining spot built for simple reservations, quality meals, and relaxed evenings with friends, family, or clients.', 'published', 'paid', 1),
((SELECT id FROM categories WHERE slug = 'events'), 'Garden event space', 'garden-event-space', 'Ikeja', 'Custom quote', 4.9, 18, 'assets/images/location/location4.jpeg', 'Flexible outdoor venue for birthdays, launches, and private experiences.', 'A flexible outdoor setting with room for small ceremonies, birthdays, launches, and curated private experiences.', 'published', 'paid', 1),
((SELECT id FROM categories WHERE slug = 'fitness'), 'Pulse fitness coach', 'pulse-fitness-coach', 'Yaba', 'From NGN 15,000/session', 4.6, 14, 'assets/images/categories/categories5.jpg', 'Personal training and wellness coaching for busy schedules.', 'A personal coaching service for users who need flexible sessions, habit support, and measurable progress.', 'published', 'paid', 0),
((SELECT id FROM categories WHERE slug = 'cars'), 'City ride rentals', 'city-ride-rentals', 'Surulere', 'From NGN 45,000/day', 4.5, 11, 'assets/images/categories/categories2.jpg', 'Clean vehicle rentals for errands, events, and weekend movement.', 'A simple car rental option for local movement, business errands, and planned weekend experiences.', 'published', 'paid', 0),
((SELECT id FROM categories WHERE slug = 'everyday-help'), 'Fresh fold laundry', 'fresh-fold-laundry', 'Ajah', 'From NGN 7,500/order', 4.7, 20, 'assets/images/categories/categories6.jpg', 'Pickup laundry service with neat folding and reliable turnaround.', 'An everyday help service for laundry pickup, cleaning support, and convenient home routines.', 'published', 'paid', 0);

DROP PROCEDURE IF EXISTS add_column_if_missing;
DROP PROCEDURE IF EXISTS add_fk_if_missing;
