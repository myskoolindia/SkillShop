-- Database schema update for global discount and category markup/discount exceptions
-- Standard MySQL compatible syntax (works across all MySQL / phpMyAdmin versions)

ALTER TABLE `payment_settings`
    ADD `seller_discount_status` TINYINT(1) DEFAULT 0,
    ADD `seller_discount_rate` DECIMAL(5,2) DEFAULT 0.00;

ALTER TABLE `categories`
    ADD `is_markup_set` TINYINT(1) DEFAULT 0,
    ADD `markup_rate` DECIMAL(5,2) DEFAULT 0.00,
    ADD `is_discount_set` TINYINT(1) DEFAULT 0,
    ADD `discount_rate` DECIMAL(5,2) DEFAULT 0.00;

