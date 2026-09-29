-- ============================================================================
-- SKILLVATION / CLUB-SHOP CONSUMABLES & CATEGORIES IMPORT SQL
-- Total Consumable Products: 219
-- Source: Consumables sheets from 5 Excel files in D:\UniServerZ\www\skillvation.comphp\Files
-- ============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------------------------------------------------------
-- 1. PARENT & SUBCATEGORIES SETUP
-- ----------------------------------------------------------------------------

-- Ensure Parent Categories Exist
INSERT INTO categories (slug, parent_id, category_order, featured_order, status, show_on_main_menu, created_at)
SELECT 'life-science', 0, 1, 1, 1, 1, NOW()
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE slug = 'life-science');

SET @parent_life_science = (SELECT id FROM categories WHERE slug = 'life-science' LIMIT 1);
INSERT INTO category_lang (category_id, lang_id, name)
SELECT @parent_life_science, 1, 'Life Science'
WHERE NOT EXISTS (SELECT 1 FROM category_lang WHERE category_id = @parent_life_science AND lang_id = 1);

INSERT INTO categories (slug, parent_id, category_order, featured_order, status, show_on_main_menu, created_at)
SELECT 'machines-and-materials', 0, 2, 1, 1, 1, NOW()
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE slug = 'machines-and-materials');

SET @parent_machines = (SELECT id FROM categories WHERE slug = 'machines-and-materials' LIMIT 1);
INSERT INTO category_lang (category_id, lang_id, name)
SELECT @parent_machines, 1, 'Machines and Materials'
WHERE NOT EXISTS (SELECT 1 FROM category_lang WHERE category_id = @parent_machines AND lang_id = 1);

INSERT INTO categories (slug, parent_id, category_order, featured_order, status, show_on_main_menu, created_at)
SELECT 'human-services', 0, 3, 1, 1, 1, NOW()
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE slug = 'human-services');

SET @parent_human_services = (SELECT id FROM categories WHERE slug = 'human-services' LIMIT 1);
INSERT INTO category_lang (category_id, lang_id, name)
SELECT @parent_human_services, 1, 'Human Services'
WHERE NOT EXISTS (SELECT 1 FROM category_lang WHERE category_id = @parent_human_services AND lang_id = 1);

-- Category: Agriculture & Gardening (slug: agriculture-gardening)
SET @parent_id_agri = (SELECT id FROM categories WHERE slug = 'life-science' LIMIT 1);
INSERT INTO categories (slug, parent_id, category_order, featured_order, status, show_on_main_menu, created_at)
SELECT 'agriculture-gardening', IFNULL(@parent_id_agri, 0), 1, 1, 1, 1, NOW()
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE slug = 'agriculture-gardening');

SET @cat_id_agri = (SELECT id FROM categories WHERE slug = 'agriculture-gardening' LIMIT 1);
INSERT INTO category_lang (category_id, lang_id, name)
SELECT @cat_id_agri, 1, 'Agriculture & Gardening'
WHERE NOT EXISTS (SELECT 1 FROM category_lang WHERE category_id = @cat_id_agri AND lang_id = 1);

-- Category: Apparel & fashion (slug: apparel-fashion)
SET @parent_id_app = (SELECT id FROM categories WHERE slug = 'machines-and-materials' LIMIT 1);
INSERT INTO categories (slug, parent_id, category_order, featured_order, status, show_on_main_menu, created_at)
SELECT 'apparel-fashion', IFNULL(@parent_id_app, 0), 1, 1, 1, 1, NOW()
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE slug = 'apparel-fashion');

SET @cat_id_app = (SELECT id FROM categories WHERE slug = 'apparel-fashion' LIMIT 1);
INSERT INTO category_lang (category_id, lang_id, name)
SELECT @cat_id_app, 1, 'Apparel & fashion'
WHERE NOT EXISTS (SELECT 1 FROM category_lang WHERE category_id = @cat_id_app AND lang_id = 1);

-- Category: Beauty & wellness (slug: beauty-wellness)
SET @parent_id_bw = (SELECT id FROM categories WHERE slug = 'human-services' LIMIT 1);
INSERT INTO categories (slug, parent_id, category_order, featured_order, status, show_on_main_menu, created_at)
SELECT 'beauty-wellness', IFNULL(@parent_id_bw, 0), 1, 1, 1, 1, NOW()
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE slug = 'beauty-wellness');

SET @cat_id_bw = (SELECT id FROM categories WHERE slug = 'beauty-wellness' LIMIT 1);
INSERT INTO category_lang (category_id, lang_id, name)
SELECT @cat_id_bw, 1, 'Beauty & wellness'
WHERE NOT EXISTS (SELECT 1 FROM category_lang WHERE category_id = @cat_id_bw AND lang_id = 1);

-- Category: Food production (slug: food-production)
SET @parent_id_fp = (SELECT id FROM categories WHERE slug = 'life-science' LIMIT 1);
INSERT INTO categories (slug, parent_id, category_order, featured_order, status, show_on_main_menu, created_at)
SELECT 'food-production', IFNULL(@parent_id_fp, 0), 1, 1, 1, 1, NOW()
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE slug = 'food-production');

SET @cat_id_fp = (SELECT id FROM categories WHERE slug = 'food-production' LIMIT 1);
INSERT INTO category_lang (category_id, lang_id, name)
SELECT @cat_id_fp, 1, 'Food production'
WHERE NOT EXISTS (SELECT 1 FROM category_lang WHERE category_id = @cat_id_fp AND lang_id = 1);

-- Category: Health care (slug: health-care)
SET @parent_id_hc = (SELECT id FROM categories WHERE slug = 'human-services' LIMIT 1);
INSERT INTO categories (slug, parent_id, category_order, featured_order, status, show_on_main_menu, created_at)
SELECT 'health-care', IFNULL(@parent_id_hc, 0), 1, 1, 1, 1, NOW()
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE slug = 'health-care');

SET @cat_id_hc = (SELECT id FROM categories WHERE slug = 'health-care' LIMIT 1);
INSERT INTO category_lang (category_id, lang_id, name)
SELECT @cat_id_hc, 1, 'Health care'
WHERE NOT EXISTS (SELECT 1 FROM category_lang WHERE category_id = @cat_id_hc AND lang_id = 1);

-- ----------------------------------------------------------------------------
-- 2. CONSUMABLE PRODUCTS & DETAILS INSERTION
-- ----------------------------------------------------------------------------


-- ============================================================================
-- FILE: agriculture and gardening.xlsx | CATEGORY: Agriculture & Gardening
-- ============================================================================
SET @cur_cat_id = (SELECT id FROM categories WHERE slug = 'agriculture-gardening' LIMIT 1);

-- Product: Seeds - assorted medicinal herbs (SKU: AGRI-001)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('seeds-assorted-medicinal-herbs-agri', 'physical', 'sell_on_site', 'AGRI-001', @cur_cat_id, 50.00, 50.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Seeds - assorted medicinal herbs', '<p><strong>Item:</strong> Seeds - assorted medicinal herbs</p><p><strong>Category:</strong> Agriculture & Gardening</p><p><strong>SKU / Item Code:</strong> AGRI-001</p><p><strong>Standard Quantity:</strong> 5 PACKETS</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;50.00</p>', 'Consumables item for Agriculture & Gardening | Qty: 5 PACKETS | Recurring: Yes');

-- Product: Potting soil (SKU: AGRI-002)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('potting-soil-agri', 'physical', 'sell_on_site', 'AGRI-002', @cur_cat_id, 100.00, 100.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Potting soil', '<p><strong>Item:</strong> Potting soil</p><p><strong>Category:</strong> Agriculture & Gardening</p><p><strong>SKU / Item Code:</strong> AGRI-002</p><p><strong>Standard Quantity:</strong> 10kgs</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;100.00</p>', 'Consumables item for Agriculture & Gardening | Qty: 10kgs | Recurring: Yes');

-- Product: Compost / Vermicompost (SKU: AGRI-003)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('compost-vermicompost-agri', 'physical', 'sell_on_site', 'AGRI-003', @cur_cat_id, 200.00, 200.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Compost / Vermicompost', '<p><strong>Item:</strong> Compost / Vermicompost</p><p><strong>Category:</strong> Agriculture & Gardening</p><p><strong>SKU / Item Code:</strong> AGRI-003</p><p><strong>Standard Quantity:</strong> 5kgs</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;200.00</p>', 'Consumables item for Agriculture & Gardening | Qty: 5kgs | Recurring: Yes');

-- Product: hydrophonics spone (SKU: AGRI-004)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('hydrophonics-spone-agri', 'physical', 'sell_on_site', 'AGRI-004', @cur_cat_id, 5.00, 5.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'hydrophonics spone', '<p><strong>Item:</strong> hydrophonics spone</p><p><strong>Category:</strong> Agriculture & Gardening</p><p><strong>SKU / Item Code:</strong> AGRI-004</p><p><strong>Standard Quantity:</strong> 30</p><p><strong>Recurring Consumable:</strong> yes</p><p><strong>Periodic Consumable:</strong> yes</p><p><strong>Unit Cost:</strong> &#8377;5.00</p>', 'Consumables item for Agriculture & Gardening | Qty: 30 | Recurring: yes');

-- Product: Gardening pots (SKU: AGRI-005)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('gardening-pots-agri', 'physical', 'sell_on_site', 'AGRI-005', @cur_cat_id, 5.00, 5.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Gardening pots', '<p><strong>Item:</strong> Gardening pots</p><p><strong>Category:</strong> Agriculture & Gardening</p><p><strong>SKU / Item Code:</strong> AGRI-005</p><p><strong>Standard Quantity:</strong> 60</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;5.00</p>', 'Consumables item for Agriculture & Gardening | Qty: 60 | Recurring: No');

-- Product: Seedling trays (SKU: AGRI-006)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('seedling-trays-agri', 'physical', 'sell_on_site', 'AGRI-006', @cur_cat_id, 20.00, 20.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Seedling trays', '<p><strong>Item:</strong> Seedling trays</p><p><strong>Category:</strong> Agriculture & Gardening</p><p><strong>SKU / Item Code:</strong> AGRI-006</p><p><strong>Standard Quantity:</strong> 15</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;20.00</p>', 'Consumables item for Agriculture & Gardening | Qty: 15 | Recurring: No');

-- Product: Small gardening tools - assorted (SKU: AGRI-007)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('small-gardening-tools-assorted-agri', 'physical', 'sell_on_site', 'AGRI-007', @cur_cat_id, 150.00, 150.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Small gardening tools - assorted', '<p><strong>Item:</strong> Small gardening tools - assorted</p><p><strong>Category:</strong> Agriculture & Gardening</p><p><strong>SKU / Item Code:</strong> AGRI-007</p><p><strong>Standard Quantity:</strong> 3 sets</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;150.00</p>', 'Consumables item for Agriculture & Gardening | Qty: 3 sets | Recurring: No');

-- Product: Spray bottles (SKU: AGRI-008)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('spray-bottles-agri', 'physical', 'sell_on_site', 'AGRI-008', @cur_cat_id, 75.00, 75.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Spray bottles', '<p><strong>Item:</strong> Spray bottles</p><p><strong>Category:</strong> Agriculture & Gardening</p><p><strong>SKU / Item Code:</strong> AGRI-008</p><p><strong>Standard Quantity:</strong> 9</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;75.00</p>', 'Consumables item for Agriculture & Gardening | Qty: 9 | Recurring: No');

-- Product: Gardening gloves (SKU: AGRI-009)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('gardening-gloves-agri', 'physical', 'sell_on_site', 'AGRI-009', @cur_cat_id, 80.00, 80.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Gardening gloves', '<p><strong>Item:</strong> Gardening gloves</p><p><strong>Category:</strong> Agriculture & Gardening</p><p><strong>SKU / Item Code:</strong> AGRI-009</p><p><strong>Standard Quantity:</strong> 10</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;80.00</p>', 'Consumables item for Agriculture & Gardening | Qty: 10 | Recurring: No');

-- Product: Plant labels / markers (SKU: AGRI-010)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('plant-labels-markers-agri', 'physical', 'sell_on_site', 'AGRI-010', @cur_cat_id, 5.00, 5.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Plant labels / markers', '<p><strong>Item:</strong> Plant labels / markers</p><p><strong>Category:</strong> Agriculture & Gardening</p><p><strong>SKU / Item Code:</strong> AGRI-010</p><p><strong>Standard Quantity:</strong> 60</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;5.00</p>', 'Consumables item for Agriculture & Gardening | Qty: 60 | Recurring: Yes');

-- Product: Permanent markers (SKU: AGRI-011)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('permanent-markers-agri', 'physical', 'sell_on_site', 'AGRI-011', @cur_cat_id, 20.00, 20.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Permanent markers', '<p><strong>Item:</strong> Permanent markers</p><p><strong>Category:</strong> Agriculture & Gardening</p><p><strong>SKU / Item Code:</strong> AGRI-011</p><p><strong>Standard Quantity:</strong> 3</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;20.00</p>', 'Consumables item for Agriculture & Gardening | Qty: 3 | Recurring: Yes');

-- Product: Measuring cups (SKU: AGRI-012)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('measuring-cups-agri', 'physical', 'sell_on_site', 'AGRI-012', @cur_cat_id, 90.00, 90.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Measuring cups', '<p><strong>Item:</strong> Measuring cups</p><p><strong>Category:</strong> Agriculture & Gardening</p><p><strong>SKU / Item Code:</strong> AGRI-012</p><p><strong>Standard Quantity:</strong> 3</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;90.00</p>', 'Consumables item for Agriculture & Gardening | Qty: 3 | Recurring: No');

-- Product: Muslin cloth (SKU: AGRI-013)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('muslin-cloth-agri', 'physical', 'sell_on_site', 'AGRI-013', @cur_cat_id, 80.00, 80.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Muslin cloth', '<p><strong>Item:</strong> Muslin cloth</p><p><strong>Category:</strong> Agriculture & Gardening</p><p><strong>SKU / Item Code:</strong> AGRI-013</p><p><strong>Standard Quantity:</strong> 15mts</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;80.00</p>', 'Consumables item for Agriculture & Gardening | Qty: 15mts | Recurring: Yes');

-- Product: Herb-drying trays (SKU: AGRI-014)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('herb-drying-trays-agri', 'physical', 'sell_on_site', 'AGRI-014', @cur_cat_id, 60.00, 60.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Herb-drying trays', '<p><strong>Item:</strong> Herb-drying trays</p><p><strong>Category:</strong> Agriculture & Gardening</p><p><strong>SKU / Item Code:</strong> AGRI-014</p><p><strong>Standard Quantity:</strong> 10</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;60.00</p>', 'Consumables item for Agriculture & Gardening | Qty: 10 | Recurring: No');

-- Product: Paper bags / kraft bags (SKU: AGRI-015)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('paper-bags-kraft-bags-agri', 'physical', 'sell_on_site', 'AGRI-015', @cur_cat_id, 5.00, 5.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Paper bags / kraft bags', '<p><strong>Item:</strong> Paper bags / kraft bags</p><p><strong>Category:</strong> Agriculture & Gardening</p><p><strong>SKU / Item Code:</strong> AGRI-015</p><p><strong>Standard Quantity:</strong> 30</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;5.00</p>', 'Consumables item for Agriculture & Gardening | Qty: 30 | Recurring: Yes');

-- Product: Jute Rope (SKU: AGRI-016)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('jute-rope-agri', 'physical', 'sell_on_site', 'AGRI-016', @cur_cat_id, 5.00, 5.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Jute Rope', '<p><strong>Item:</strong> Jute Rope</p><p><strong>Category:</strong> Agriculture & Gardening</p><p><strong>SKU / Item Code:</strong> AGRI-016</p><p><strong>Standard Quantity:</strong> 9</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;5.00</p>', 'Consumables item for Agriculture & Gardening | Qty: 9 | Recurring: Yes');

-- Product: Chart paper / project sheets (SKU: AGRI-017)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('chart-paper-project-sheets-agri', 'physical', 'sell_on_site', 'AGRI-017', @cur_cat_id, 14.00, 14.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Chart paper / project sheets', '<p><strong>Item:</strong> Chart paper / project sheets</p><p><strong>Category:</strong> Agriculture & Gardening</p><p><strong>SKU / Item Code:</strong> AGRI-017</p><p><strong>Standard Quantity:</strong> 30</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;14.00</p>', 'Consumables item for Agriculture & Gardening | Qty: 30 | Recurring: Yes');

-- Product: Magnifying glasses (SKU: AGRI-018)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('magnifying-glasses-agri', 'physical', 'sell_on_site', 'AGRI-018', @cur_cat_id, 90.00, 90.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Magnifying glasses', '<p><strong>Item:</strong> Magnifying glasses</p><p><strong>Category:</strong> Agriculture & Gardening</p><p><strong>SKU / Item Code:</strong> AGRI-018</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;90.00</p>', 'Consumables item for Agriculture & Gardening | Qty: 1 | Recurring: No');

-- Product: Gardening apron (SKU: AGRI-019)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('gardening-apron-agri', 'physical', 'sell_on_site', 'AGRI-019', @cur_cat_id, 140.00, 140.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Gardening apron', '<p><strong>Item:</strong> Gardening apron</p><p><strong>Category:</strong> Agriculture & Gardening</p><p><strong>SKU / Item Code:</strong> AGRI-019</p><p><strong>Standard Quantity:</strong> 10</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;140.00</p>', 'Consumables item for Agriculture & Gardening | Qty: 10 | Recurring: No');

-- Product: Digital / kitchen weighing scale (SKU: AGRI-020)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('digital-kitchen-weighing-scale-agri', 'physical', 'sell_on_site', 'AGRI-020', @cur_cat_id, 200.00, 200.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Digital / kitchen weighing scale', '<p><strong>Item:</strong> Digital / kitchen weighing scale</p><p><strong>Category:</strong> Agriculture & Gardening</p><p><strong>SKU / Item Code:</strong> AGRI-020</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;200.00</p>', 'Consumables item for Agriculture & Gardening | Qty: 1 | Recurring: No');

-- Product: Storage boxes (SKU: AGRI-021)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('storage-boxes-agri', 'physical', 'sell_on_site', 'AGRI-021', @cur_cat_id, 40.00, 40.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Storage boxes', '<p><strong>Item:</strong> Storage boxes</p><p><strong>Category:</strong> Agriculture & Gardening</p><p><strong>SKU / Item Code:</strong> AGRI-021</p><p><strong>Standard Quantity:</strong> 10</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;40.00</p>', 'Consumables item for Agriculture & Gardening | Qty: 10 | Recurring: No');

-- Product: hydrophonics tower (SKU: AGRI-022)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('hydrophonics-tower-agri', 'physical', 'sell_on_site', 'AGRI-022', @cur_cat_id, 5000.00, 5000.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'hydrophonics tower', '<p><strong>Item:</strong> hydrophonics tower</p><p><strong>Category:</strong> Agriculture & Gardening</p><p><strong>SKU / Item Code:</strong> AGRI-022</p><p><strong>Standard Quantity:</strong> 3</p><p><strong>Recurring Consumable:</strong> no</p><p><strong>Periodic Consumable:</strong> yes</p><p><strong>Unit Cost:</strong> &#8377;5000.00</p>', 'Consumables item for Agriculture & Gardening | Qty: 3 | Recurring: no');

-- Product: terrarium planter (SKU: AGRI-023)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('terrarium-planter-agri', 'physical', 'sell_on_site', 'AGRI-023', @cur_cat_id, 4000.00, 4000.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'terrarium planter', '<p><strong>Item:</strong> terrarium planter</p><p><strong>Category:</strong> Agriculture & Gardening</p><p><strong>SKU / Item Code:</strong> AGRI-023</p><p><strong>Standard Quantity:</strong> 3</p><p><strong>Recurring Consumable:</strong> no</p><p><strong>Periodic Consumable:</strong> yes</p><p><strong>Unit Cost:</strong> &#8377;4000.00</p>', 'Consumables item for Agriculture & Gardening | Qty: 3 | Recurring: no');

-- Product: mini green house kit (SKU: AGRI-024)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('mini-green-house-kit-agri', 'physical', 'sell_on_site', 'AGRI-024', @cur_cat_id, 4000.00, 4000.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'mini green house kit', '<p><strong>Item:</strong> mini green house kit</p><p><strong>Category:</strong> Agriculture & Gardening</p><p><strong>SKU / Item Code:</strong> AGRI-024</p><p><strong>Standard Quantity:</strong> 3</p><p><strong>Recurring Consumable:</strong> no</p><p><strong>Periodic Consumable:</strong> yes</p><p><strong>Unit Cost:</strong> &#8377;4000.00</p>', 'Consumables item for Agriculture & Gardening | Qty: 3 | Recurring: no');


-- ============================================================================
-- FILE: APPARALS LIST.xlsx | CATEGORY: Apparel & fashion
-- ============================================================================
SET @cur_cat_id = (SELECT id FROM categories WHERE slug = 'apparel-fashion' LIMIT 1);

-- Product: Cotton embroidery fabric (SKU: APP-001)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('cotton-embroidery-fabric-app', 'physical', 'sell_on_site', 'APP-001', @cur_cat_id, 80.00, 80.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Cotton embroidery fabric', '<p><strong>Item:</strong> Cotton embroidery fabric</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-001</p><p><strong>Standard Quantity:</strong> 10</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;80.00</p>', 'Consumables item for Apparel & fashion | Qty: 10 | Recurring: Yes');

-- Product: Embroidery floss - assorted colours (SKU: APP-002)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('embroidery-floss-assorted-colours-app', 'physical', 'sell_on_site', 'APP-002', @cur_cat_id, 10.00, 10.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Embroidery floss - assorted colours', '<p><strong>Item:</strong> Embroidery floss - assorted colours</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-002</p><p><strong>Standard Quantity:</strong> 50</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;10.00</p>', 'Consumables item for Apparel & fashion | Qty: 50 | Recurring: Yes');

-- Product: Cotton embroidery thread - assorted (SKU: APP-003)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('cotton-embroidery-thread-assorted-app', 'physical', 'sell_on_site', 'APP-003', @cur_cat_id, 380.00, 380.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Cotton embroidery thread - assorted', '<p><strong>Item:</strong> Cotton embroidery thread - assorted</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-003</p><p><strong>Standard Quantity:</strong> 2</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;380.00</p>', 'Consumables item for Apparel & fashion | Qty: 2 | Recurring: Yes');

-- Product: Wool / thicker embroidery yarn (SKU: APP-004)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('wool-thicker-embroidery-yarn-app', 'physical', 'sell_on_site', 'APP-004', @cur_cat_id, 20.00, 20.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Wool / thicker embroidery yarn', '<p><strong>Item:</strong> Wool / thicker embroidery yarn</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-004</p><p><strong>Standard Quantity:</strong> 15</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;20.00</p>', 'Consumables item for Apparel & fashion | Qty: 15 | Recurring: Yes');

-- Product: Metallic embroidery thread (SKU: APP-005)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('metallic-embroidery-thread-app', 'physical', 'sell_on_site', 'APP-005', @cur_cat_id, 20.00, 20.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Metallic embroidery thread', '<p><strong>Item:</strong> Metallic embroidery thread</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-005</p><p><strong>Standard Quantity:</strong> 10</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;20.00</p>', 'Consumables item for Apparel & fashion | Qty: 10 | Recurring: Yes');

-- Product: Embroidery hoops - assorted sizes (SKU: APP-006)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('embroidery-hoops-assorted-sizes-app', 'physical', 'sell_on_site', 'APP-006', @cur_cat_id, 40.00, 40.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Embroidery hoops - assorted sizes', '<p><strong>Item:</strong> Embroidery hoops - assorted sizes</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-006</p><p><strong>Standard Quantity:</strong> 4 sizes</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;40.00</p>', 'Consumables item for Apparel & fashion | Qty: 4 sizes | Recurring: No');

-- Product: Embroidery needles - assorted sizes (SKU: APP-007)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('embroidery-needles-assorted-sizes-app', 'physical', 'sell_on_site', 'APP-007', @cur_cat_id, 40.00, 40.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Embroidery needles - assorted sizes', '<p><strong>Item:</strong> Embroidery needles - assorted sizes</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-007</p><p><strong>Standard Quantity:</strong> 2 boxes</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;40.00</p>', 'Consumables item for Apparel & fashion | Qty: 2 boxes | Recurring: No');

-- Product: Needle threaders (SKU: APP-008)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('needle-threaders-app', 'physical', 'sell_on_site', 'APP-008', @cur_cat_id, 20.00, 20.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Needle threaders', '<p><strong>Item:</strong> Needle threaders</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-008</p><p><strong>Standard Quantity:</strong> 10</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;20.00</p>', 'Consumables item for Apparel & fashion | Qty: 10 | Recurring: No');

-- Product: Embroidery scissors (SKU: APP-009)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('embroidery-scissors-app', 'physical', 'sell_on_site', 'APP-009', @cur_cat_id, 200.00, 200.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Embroidery scissors', '<p><strong>Item:</strong> Embroidery scissors</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-009</p><p><strong>Standard Quantity:</strong> 3</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;200.00</p>', 'Consumables item for Apparel & fashion | Qty: 3 | Recurring: No');

-- Product: Fabric scissors (SKU: APP-010)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('fabric-scissors-app', 'physical', 'sell_on_site', 'APP-010', @cur_cat_id, 200.00, 200.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Fabric scissors', '<p><strong>Item:</strong> Fabric scissors</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-010</p><p><strong>Standard Quantity:</strong> 3</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;200.00</p>', 'Consumables item for Apparel & fashion | Qty: 3 | Recurring: No');

-- Product: Thread snips (SKU: APP-011)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('thread-snips-app', 'physical', 'sell_on_site', 'APP-011', @cur_cat_id, 20.00, 20.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Thread snips', '<p><strong>Item:</strong> Thread snips</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-011</p><p><strong>Standard Quantity:</strong> 3</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;20.00</p>', 'Consumables item for Apparel & fashion | Qty: 3 | Recurring: No');

-- Product: Embroidery patterns / motif templates (SKU: APP-012)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('embroidery-patterns-motif-templates-app', 'physical', 'sell_on_site', 'APP-012', @cur_cat_id, 20.00, 20.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Embroidery patterns / motif templates', '<p><strong>Item:</strong> Embroidery patterns / motif templates</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-012</p><p><strong>Standard Quantity:</strong> 30</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;20.00</p>', 'Consumables item for Apparel & fashion | Qty: 30 | Recurring: Yes');

-- Product: Carbon paper - fabric transfer (SKU: APP-013)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('carbon-paper-fabric-transfer-app', 'physical', 'sell_on_site', 'APP-013', @cur_cat_id, 20.00, 20.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Carbon paper - fabric transfer', '<p><strong>Item:</strong> Carbon paper - fabric transfer</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-013</p><p><strong>Standard Quantity:</strong> 10</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;20.00</p>', 'Consumables item for Apparel & fashion | Qty: 10 | Recurring: Yes');

-- Product: Transfer pencils / fabric marking pencils (SKU: APP-014)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('transfer-pencils-fabric-marking-pencils-app', 'physical', 'sell_on_site', 'APP-014', @cur_cat_id, 40.00, 40.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Transfer pencils / fabric marking pencils', '<p><strong>Item:</strong> Transfer pencils / fabric marking pencils</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-014</p><p><strong>Standard Quantity:</strong> 1 box</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;40.00</p>', 'Consumables item for Apparel & fashion | Qty: 1 box | Recurring: Yes');

-- Product: Rulers (SKU: APP-015)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('rulers-app', 'physical', 'sell_on_site', 'APP-015', @cur_cat_id, 50.00, 50.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Rulers', '<p><strong>Item:</strong> Rulers</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-015</p><p><strong>Standard Quantity:</strong> 5</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;50.00</p>', 'Consumables item for Apparel & fashion | Qty: 5 | Recurring: No');

-- Product: Measuring tapes (SKU: APP-016)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('measuring-tapes-app', 'physical', 'sell_on_site', 'APP-016', @cur_cat_id, 20.00, 20.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Measuring tapes', '<p><strong>Item:</strong> Measuring tapes</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-016</p><p><strong>Standard Quantity:</strong> 5</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;20.00</p>', 'Consumables item for Apparel & fashion | Qty: 5 | Recurring: No');

-- Product: Pin cushions (SKU: APP-017)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('pin-cushions-app', 'physical', 'sell_on_site', 'APP-017', @cur_cat_id, 200.00, 200.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Pin cushions', '<p><strong>Item:</strong> Pin cushions</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-017</p><p><strong>Standard Quantity:</strong> 3</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;200.00</p>', 'Consumables item for Apparel & fashion | Qty: 3 | Recurring: No');

-- Product: Sewing pins (SKU: APP-018)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('sewing-pins-app', 'physical', 'sell_on_site', 'APP-018', @cur_cat_id, 80.00, 80.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Sewing pins', '<p><strong>Item:</strong> Sewing pins</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-018</p><p><strong>Standard Quantity:</strong> 10</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;80.00</p>', 'Consumables item for Apparel & fashion | Qty: 10 | Recurring: Yes');

-- Product: Buttons - assorted (SKU: APP-019)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('buttons-assorted-app', 'physical', 'sell_on_site', 'APP-019', @cur_cat_id, 3.00, 3.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Buttons - assorted', '<p><strong>Item:</strong> Buttons - assorted</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-019</p><p><strong>Standard Quantity:</strong> 300</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;3.00</p>', 'Consumables item for Apparel & fashion | Qty: 300 | Recurring: Yes');

-- Product: Beads - assorted (SKU: APP-020)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('beads-assorted-app', 'physical', 'sell_on_site', 'APP-020', @cur_cat_id, 3.00, 3.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Beads - assorted', '<p><strong>Item:</strong> Beads - assorted</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-020</p><p><strong>Standard Quantity:</strong> 300</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;3.00</p>', 'Consumables item for Apparel & fashion | Qty: 300 | Recurring: Yes');

-- Product: Sequins - assorted (SKU: APP-021)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('sequins-assorted-app', 'physical', 'sell_on_site', 'APP-021', @cur_cat_id, 10.00, 10.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Sequins - assorted', '<p><strong>Item:</strong> Sequins - assorted</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-021</p><p><strong>Standard Quantity:</strong> 30 PACKETS</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;10.00</p>', 'Consumables item for Apparel & fashion | Qty: 30 PACKETS | Recurring: Yes');

-- Product: Ribbons - assorted (SKU: APP-022)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('ribbons-assorted-app', 'physical', 'sell_on_site', 'APP-022', @cur_cat_id, 10.00, 10.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Ribbons - assorted', '<p><strong>Item:</strong> Ribbons - assorted</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-022</p><p><strong>Standard Quantity:</strong> 30 pcs</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;10.00</p>', 'Consumables item for Apparel & fashion | Qty: 30 pcs | Recurring: Yes');

-- Product: Lace / decorative trims (SKU: APP-023)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('lace-decorative-trims-app', 'physical', 'sell_on_site', 'APP-023', @cur_cat_id, 0.00, 0.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Lace / decorative trims', '<p><strong>Item:</strong> Lace / decorative trims</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-023</p><p><strong>Standard Quantity:</strong> 30 mts</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;0.00</p>', 'Consumables item for Apparel & fashion | Qty: 30 mts | Recurring: Yes');

-- Product: Felt sheets (SKU: APP-024)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('felt-sheets-app', 'physical', 'sell_on_site', 'APP-024', @cur_cat_id, 20.00, 20.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Felt sheets', '<p><strong>Item:</strong> Felt sheets</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-024</p><p><strong>Standard Quantity:</strong> 30 sheets</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;20.00</p>', 'Consumables item for Apparel & fashion | Qty: 30 sheets | Recurring: Yes');

-- Product: Canvas sheets / embroidery canvas (SKU: APP-025)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('canvas-sheets-embroidery-canvas-app', 'physical', 'sell_on_site', 'APP-025', @cur_cat_id, 15.00, 15.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Canvas sheets / embroidery canvas', '<p><strong>Item:</strong> Canvas sheets / embroidery canvas</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-025</p><p><strong>Standard Quantity:</strong> 30 sheets</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;15.00</p>', 'Consumables item for Apparel & fashion | Qty: 30 sheets | Recurring: Yes');

-- Product: Jute / natural fabric (SKU: APP-026)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('jute-natural-fabric-app', 'physical', 'sell_on_site', 'APP-026', @cur_cat_id, 80.00, 80.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Jute / natural fabric', '<p><strong>Item:</strong> Jute / natural fabric</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-026</p><p><strong>Standard Quantity:</strong> 10 mtrs</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;80.00</p>', 'Consumables item for Apparel & fashion | Qty: 10 mtrs | Recurring: Yes');

-- Product: Fabric glue (SKU: APP-027)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('fabric-glue-app', 'physical', 'sell_on_site', 'APP-027', @cur_cat_id, 20.00, 20.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Fabric glue', '<p><strong>Item:</strong> Fabric glue</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-027</p><p><strong>Standard Quantity:</strong> 6</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;20.00</p>', 'Consumables item for Apparel & fashion | Qty: 6 | Recurring: Yes');

-- Product: Textile / fabric paints (SKU: APP-028)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('textile-fabric-paints-app', 'physical', 'sell_on_site', 'APP-028', @cur_cat_id, 200.00, 200.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Textile / fabric paints', '<p><strong>Item:</strong> Textile / fabric paints</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-028</p><p><strong>Standard Quantity:</strong> 6</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;200.00</p>', 'Consumables item for Apparel & fashion | Qty: 6 | Recurring: Yes');

-- Product: Paint brushes - fine (SKU: APP-029)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('paint-brushes-fine-app', 'physical', 'sell_on_site', 'APP-029', @cur_cat_id, 100.00, 100.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Paint brushes - fine', '<p><strong>Item:</strong> Paint brushes - fine</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-029</p><p><strong>Standard Quantity:</strong> 6 sets</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;100.00</p>', 'Consumables item for Apparel & fashion | Qty: 6 sets | Recurring: No');

-- Product: Colour pencils (SKU: APP-030)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('colour-pencils-app', 'physical', 'sell_on_site', 'APP-030', @cur_cat_id, 50.00, 50.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Colour pencils', '<p><strong>Item:</strong> Colour pencils</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-030</p><p><strong>Standard Quantity:</strong> 9</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;50.00</p>', 'Consumables item for Apparel & fashion | Qty: 9 | Recurring: No');

-- Product: Sketch pens / markers (SKU: APP-031)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('sketch-pens-markers-app', 'physical', 'sell_on_site', 'APP-031', @cur_cat_id, 20.00, 20.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Sketch pens / markers', '<p><strong>Item:</strong> Sketch pens / markers</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-031</p><p><strong>Standard Quantity:</strong> 15</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;20.00</p>', 'Consumables item for Apparel & fashion | Qty: 15 | Recurring: Yes');

-- Product: Stitch practice fabric (SKU: APP-032)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('stitch-practice-fabric-app', 'physical', 'sell_on_site', 'APP-032', @cur_cat_id, 50.00, 50.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Stitch practice fabric', '<p><strong>Item:</strong> Stitch practice fabric</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-032</p><p><strong>Standard Quantity:</strong> 30</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;50.00</p>', 'Consumables item for Apparel & fashion | Qty: 30 | Recurring: Yes');

-- Product: Storage boxes for threads (SKU: APP-033)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('storage-boxes-for-threads-app', 'physical', 'sell_on_site', 'APP-033', @cur_cat_id, 80.00, 80.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Storage boxes for threads', '<p><strong>Item:</strong> Storage boxes for threads</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-033</p><p><strong>Standard Quantity:</strong> 3</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;80.00</p>', 'Consumables item for Apparel & fashion | Qty: 3 | Recurring: No');

-- Product: Thread organisers / bobbins (SKU: APP-034)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('thread-organisers-bobbins-app', 'physical', 'sell_on_site', 'APP-034', @cur_cat_id, 10.00, 10.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Thread organisers / bobbins', '<p><strong>Item:</strong> Thread organisers / bobbins</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-034</p><p><strong>Standard Quantity:</strong> 10</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;10.00</p>', 'Consumables item for Apparel & fashion | Qty: 10 | Recurring: No');

-- Product: Material sorting trays (SKU: APP-035)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('material-sorting-trays-app', 'physical', 'sell_on_site', 'APP-035', @cur_cat_id, 50.00, 50.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Material sorting trays', '<p><strong>Item:</strong> Material sorting trays</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-035</p><p><strong>Standard Quantity:</strong> 5</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;50.00</p>', 'Consumables item for Apparel & fashion | Qty: 5 | Recurring: No');

-- Product: Work mats (SKU: APP-036)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('work-mats-app', 'physical', 'sell_on_site', 'APP-036', @cur_cat_id, 300.00, 300.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Work mats', '<p><strong>Item:</strong> Work mats</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-036</p><p><strong>Standard Quantity:</strong> 10</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;300.00</p>', 'Consumables item for Apparel & fashion | Qty: 10 | Recurring: No');

-- Product: Aprons (SKU: APP-037)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('aprons-app', 'physical', 'sell_on_site', 'APP-037', @cur_cat_id, 150.00, 150.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Aprons', '<p><strong>Item:</strong> Aprons</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-037</p><p><strong>Standard Quantity:</strong> 10</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;150.00</p>', 'Consumables item for Apparel & fashion | Qty: 10 | Recurring: No');

-- Product: Product tags / labels (SKU: APP-038)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('product-tags-labels-app', 'physical', 'sell_on_site', 'APP-038', @cur_cat_id, 5.00, 5.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Product tags / labels', '<p><strong>Item:</strong> Product tags / labels</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-038</p><p><strong>Standard Quantity:</strong> 30</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;5.00</p>', 'Consumables item for Apparel & fashion | Qty: 30 | Recurring: Yes');

-- Product: Handmade paper (SKU: APP-039)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('handmade-paper-app', 'physical', 'sell_on_site', 'APP-039', @cur_cat_id, 20.00, 20.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Handmade paper', '<p><strong>Item:</strong> Handmade paper</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-039</p><p><strong>Standard Quantity:</strong> 30</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;20.00</p>', 'Consumables item for Apparel & fashion | Qty: 30 | Recurring: Yes');

-- Product: chart papers sheets (SKU: APP-040)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('chart-papers-sheets-app', 'physical', 'sell_on_site', 'APP-040', @cur_cat_id, 5.00, 5.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'chart papers sheets', '<p><strong>Item:</strong> chart papers sheets</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-040</p><p><strong>Standard Quantity:</strong> 30</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;5.00</p>', 'Consumables item for Apparel & fashion | Qty: 30 | Recurring: Yes');

-- Product: Corrugated cardboard (SKU: APP-041)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('corrugated-cardboard-app', 'physical', 'sell_on_site', 'APP-041', @cur_cat_id, 10.00, 10.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Corrugated cardboard', '<p><strong>Item:</strong> Corrugated cardboard</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-041</p><p><strong>Standard Quantity:</strong> 30</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;10.00</p>', 'Consumables item for Apparel & fashion | Qty: 30 | Recurring: Yes');

-- Product: Popsicle / ice-cream sticks (SKU: APP-042)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('popsicle-ice-cream-sticks-app', 'physical', 'sell_on_site', 'APP-042', @cur_cat_id, 3.00, 3.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Popsicle / ice-cream sticks', '<p><strong>Item:</strong> Popsicle / ice-cream sticks</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-042</p><p><strong>Standard Quantity:</strong> 600</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;3.00</p>', 'Consumables item for Apparel & fashion | Qty: 600 | Recurring: Yes');

-- Product: Bamboo sticks / craft sticks (SKU: APP-043)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('bamboo-sticks-craft-sticks-app', 'physical', 'sell_on_site', 'APP-043', @cur_cat_id, 5.00, 5.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Bamboo sticks / craft sticks', '<p><strong>Item:</strong> Bamboo sticks / craft sticks</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-043</p><p><strong>Standard Quantity:</strong> 300</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;5.00</p>', 'Consumables item for Apparel & fashion | Qty: 300 | Recurring: Yes');

-- Product: Wooden beads (SKU: APP-044)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('wooden-beads-app', 'physical', 'sell_on_site', 'APP-044', @cur_cat_id, 1.00, 1.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Wooden beads', '<p><strong>Item:</strong> Wooden beads</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-044</p><p><strong>Standard Quantity:</strong> 600 pcs</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;1.00</p>', 'Consumables item for Apparel & fashion | Qty: 600 pcs | Recurring: Yes');

-- Product: Craft foam sheets (SKU: APP-045)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('craft-foam-sheets-app', 'physical', 'sell_on_site', 'APP-045', @cur_cat_id, 10.00, 10.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Craft foam sheets', '<p><strong>Item:</strong> Craft foam sheets</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-045</p><p><strong>Standard Quantity:</strong> 30</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;10.00</p>', 'Consumables item for Apparel & fashion | Qty: 30 | Recurring: Yes');

-- Product: Air-dry / craft clay (SKU: APP-046)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('air-dry-craft-clay-app', 'physical', 'sell_on_site', 'APP-046', @cur_cat_id, 100.00, 100.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Air-dry / craft clay', '<p><strong>Item:</strong> Air-dry / craft clay</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-046</p><p><strong>Standard Quantity:</strong> 10kgs</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;100.00</p>', 'Consumables item for Apparel & fashion | Qty: 10kgs | Recurring: Yes');

-- Product: Paper plates / paper bowls (SKU: APP-047)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('paper-plates-paper-bowls-app', 'physical', 'sell_on_site', 'APP-047', @cur_cat_id, 2.00, 2.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Paper plates / paper bowls', '<p><strong>Item:</strong> Paper plates / paper bowls</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-047</p><p><strong>Standard Quantity:</strong> 60</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;2.00</p>', 'Consumables item for Apparel & fashion | Qty: 60 | Recurring: Yes');

-- Product: Paper cups (SKU: APP-048)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('paper-cups-app', 'physical', 'sell_on_site', 'APP-048', @cur_cat_id, 2.00, 2.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Paper cups', '<p><strong>Item:</strong> Paper cups</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-048</p><p><strong>Standard Quantity:</strong> 60</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;2.00</p>', 'Consumables item for Apparel & fashion | Qty: 60 | Recurring: Yes');

-- Product: Paper bags (SKU: APP-049)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('paper-bags-app', 'physical', 'sell_on_site', 'APP-049', @cur_cat_id, 2.00, 2.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Paper bags', '<p><strong>Item:</strong> Paper bags</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-049</p><p><strong>Standard Quantity:</strong> 60</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;2.00</p>', 'Consumables item for Apparel & fashion | Qty: 60 | Recurring: Yes');

-- Product: Craft glue / PVA glue (SKU: APP-050)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('craft-glue-pva-glue-app', 'physical', 'sell_on_site', 'APP-050', @cur_cat_id, 20.00, 20.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Craft glue / PVA glue', '<p><strong>Item:</strong> Craft glue / PVA glue</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-050</p><p><strong>Standard Quantity:</strong> 10</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;20.00</p>', 'Consumables item for Apparel & fashion | Qty: 10 | Recurring: Yes');

-- Product: Hot glue gun - teacher use (SKU: APP-051)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('hot-glue-gun-teacher-use-app', 'physical', 'sell_on_site', 'APP-051', @cur_cat_id, 150.00, 150.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Hot glue gun - teacher use', '<p><strong>Item:</strong> Hot glue gun - teacher use</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-051</p><p><strong>Standard Quantity:</strong> 3</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;150.00</p>', 'Consumables item for Apparel & fashion | Qty: 3 | Recurring: No');

-- Product: Hot glue sticks (SKU: APP-052)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('hot-glue-sticks-app', 'physical', 'sell_on_site', 'APP-052', @cur_cat_id, 10.00, 10.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Hot glue sticks', '<p><strong>Item:</strong> Hot glue sticks</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-052</p><p><strong>Standard Quantity:</strong> 90</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;10.00</p>', 'Consumables item for Apparel & fashion | Qty: 90 | Recurring: Yes');

-- Product: Double-sided tape (SKU: APP-053)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('double-sided-tape-app', 'physical', 'sell_on_site', 'APP-053', @cur_cat_id, 10.00, 10.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Double-sided tape', '<p><strong>Item:</strong> Double-sided tape</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-053</p><p><strong>Standard Quantity:</strong> 10 rolls</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;10.00</p>', 'Consumables item for Apparel & fashion | Qty: 10 rolls | Recurring: Yes');

-- Product: Transparent tape (SKU: APP-054)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('transparent-tape-app', 'physical', 'sell_on_site', 'APP-054', @cur_cat_id, 10.00, 10.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Transparent tape', '<p><strong>Item:</strong> Transparent tape</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-054</p><p><strong>Standard Quantity:</strong> 10 rolls</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;10.00</p>', 'Consumables item for Apparel & fashion | Qty: 10 rolls | Recurring: Yes');

-- Product: Cutting mats (SKU: APP-055)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('cutting-mats-app', 'physical', 'sell_on_site', 'APP-055', @cur_cat_id, 300.00, 300.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Cutting mats', '<p><strong>Item:</strong> Cutting mats</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-055</p><p><strong>Standard Quantity:</strong> 3</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;300.00</p>', 'Consumables item for Apparel & fashion | Qty: 3 | Recurring: No');

-- Product: Silk embroidery thread (SKU: APP-056)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('silk-embroidery-thread-app', 'physical', 'sell_on_site', 'APP-056', @cur_cat_id, 20.00, 20.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Silk embroidery thread', '<p><strong>Item:</strong> Silk embroidery thread</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-056</p><p><strong>Standard Quantity:</strong> 30</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;20.00</p>', 'Consumables item for Apparel & fashion | Qty: 30 | Recurring: Yes');

-- Product: Kashmiri motif template sheets (SKU: APP-057)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('kashmiri-motif-template-sheets-app', 'physical', 'sell_on_site', 'APP-057', @cur_cat_id, 50.00, 50.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Kashmiri motif template sheets', '<p><strong>Item:</strong> Kashmiri motif template sheets</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-057</p><p><strong>Standard Quantity:</strong> 30</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;50.00</p>', 'Consumables item for Apparel & fashion | Qty: 30 | Recurring: No');

-- Product: Clothes pegs / clips (SKU: APP-058)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('clothes-pegs-clips-app', 'physical', 'sell_on_site', 'APP-058', @cur_cat_id, 5.00, 5.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Clothes pegs / clips', '<p><strong>Item:</strong> Clothes pegs / clips</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-058</p><p><strong>Standard Quantity:</strong> 30</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;5.00</p>', 'Consumables item for Apparel & fashion | Qty: 30 | Recurring: No');

-- Product: Spinning wheels / charkha accessories (SKU: APP-059)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('spinning-wheels-charkha-accessories-app', 'physical', 'sell_on_site', 'APP-059', @cur_cat_id, 700.00, 700.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Spinning wheels / charkha accessories', '<p><strong>Item:</strong> Spinning wheels / charkha accessories</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-059</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Periodic</p><p><strong>Unit Cost:</strong> &#8377;700.00</p>', 'Consumables item for Apparel & fashion | Qty: 1 | Recurring: No');

-- Product: Mini handloom frames (SKU: APP-060)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('mini-handloom-frames-app', 'physical', 'sell_on_site', 'APP-060', @cur_cat_id, 500.00, 500.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Mini handloom frames', '<p><strong>Item:</strong> Mini handloom frames</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-060</p><p><strong>Standard Quantity:</strong> 3</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Periodic</p><p><strong>Unit Cost:</strong> &#8377;500.00</p>', 'Consumables item for Apparel & fashion | Qty: 3 | Recurring: No');

-- Product: Khadi fabric - assorted colours (SKU: APP-061)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('khadi-fabric-assorted-colours-app', 'physical', 'sell_on_site', 'APP-061', @cur_cat_id, 60.00, 60.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Khadi fabric - assorted colours', '<p><strong>Item:</strong> Khadi fabric - assorted colours</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-061</p><p><strong>Standard Quantity:</strong> 10mts</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;60.00</p>', 'Consumables item for Apparel & fashion | Qty: 10mts | Recurring: Yes');

-- Product: Digital weighing scales (SKU: APP-062)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('digital-weighing-scales-app', 'physical', 'sell_on_site', 'APP-062', @cur_cat_id, 200.00, 200.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Digital weighing scales', '<p><strong>Item:</strong> Digital weighing scales</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-062</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;200.00</p>', 'Consumables item for Apparel & fashion | Qty: 1 | Recurring: No');

-- Product: sewing machine (SKU: APP-063)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('sewing-machine-app', 'physical', 'sell_on_site', 'APP-063', @cur_cat_id, 6000.00, 6000.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'sewing machine', '<p><strong>Item:</strong> sewing machine</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-063</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> no</p><p><strong>Periodic Consumable:</strong> yes</p><p><strong>Unit Cost:</strong> &#8377;6000.00</p>', 'Consumables item for Apparel & fashion | Qty: 1 | Recurring: no');

-- Product: Elastic bands / mask elastic (SKU: APP-064)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('elastic-bands-mask-elastic-app', 'physical', 'sell_on_site', 'APP-064', @cur_cat_id, 0.00, 0.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Elastic bands / mask elastic', '<p><strong>Item:</strong> Elastic bands / mask elastic</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-064</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;0.00</p>', 'Consumables item for Apparel & fashion | Recurring: Yes');

-- Product: block prints (SKU: APP-065)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('block-prints-app', 'physical', 'sell_on_site', 'APP-065', @cur_cat_id, 40.00, 40.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'block prints', '<p><strong>Item:</strong> block prints</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-065</p><p><strong>Standard Quantity:</strong> 30</p><p><strong>Recurring Consumable:</strong> no</p><p><strong>Periodic Consumable:</strong> yes</p><p><strong>Unit Cost:</strong> &#8377;40.00</p>', 'Consumables item for Apparel & fashion | Qty: 30 | Recurring: no');

-- Product: crochets tool (SKU: APP-066)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('crochets-tool-app', 'physical', 'sell_on_site', 'APP-066', @cur_cat_id, 200.00, 200.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'crochets tool', '<p><strong>Item:</strong> crochets tool</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-066</p><p><strong>Standard Quantity:</strong> 3</p><p><strong>Recurring Consumable:</strong> no</p><p><strong>Periodic Consumable:</strong> yes</p><p><strong>Unit Cost:</strong> &#8377;200.00</p>', 'Consumables item for Apparel & fashion | Qty: 3 | Recurring: no');

-- Product: Kundans (SKU: APP-067)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('kundans-app', 'physical', 'sell_on_site', 'APP-067', @cur_cat_id, 2.00, 2.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Kundans', '<p><strong>Item:</strong> Kundans</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-067</p><p><strong>Standard Quantity:</strong> 300gms</p><p><strong>Recurring Consumable:</strong> yes</p><p><strong>Periodic Consumable:</strong> yes</p><p><strong>Unit Cost:</strong> &#8377;2.00</p>', 'Consumables item for Apparel & fashion | Qty: 300gms | Recurring: yes');

-- Product: mirrors (SKU: APP-068)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('mirrors-app', 'physical', 'sell_on_site', 'APP-068', @cur_cat_id, 2.00, 2.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'mirrors', '<p><strong>Item:</strong> mirrors</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-068</p><p><strong>Standard Quantity:</strong> 300gms</p><p><strong>Recurring Consumable:</strong> yes</p><p><strong>Periodic Consumable:</strong> yes</p><p><strong>Unit Cost:</strong> &#8377;2.00</p>', 'Consumables item for Apparel & fashion | Qty: 300gms | Recurring: yes');

-- Product: pallets (SKU: APP-069)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('pallets-app', 'physical', 'sell_on_site', 'APP-069', @cur_cat_id, 15.00, 15.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'pallets', '<p><strong>Item:</strong> pallets</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-069</p><p><strong>Standard Quantity:</strong> 9</p><p><strong>Recurring Consumable:</strong> yes</p><p><strong>Periodic Consumable:</strong> yes</p><p><strong>Unit Cost:</strong> &#8377;15.00</p>', 'Consumables item for Apparel & fashion | Qty: 9 | Recurring: yes');

-- Product: quilling strips (SKU: APP-070)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('quilling-strips-app', 'physical', 'sell_on_site', 'APP-070', @cur_cat_id, 2.00, 2.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'quilling strips', '<p><strong>Item:</strong> quilling strips</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-070</p><p><strong>Standard Quantity:</strong> 300</p><p><strong>Recurring Consumable:</strong> yes</p><p><strong>Periodic Consumable:</strong> yes</p><p><strong>Unit Cost:</strong> &#8377;2.00</p>', 'Consumables item for Apparel & fashion | Qty: 300 | Recurring: yes');

-- Product: quilling tools (SKU: APP-071)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('quilling-tools-app', 'physical', 'sell_on_site', 'APP-071', @cur_cat_id, 5.00, 5.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'quilling tools', '<p><strong>Item:</strong> quilling tools</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-071</p><p><strong>Standard Quantity:</strong> 15</p><p><strong>Recurring Consumable:</strong> yes</p><p><strong>Periodic Consumable:</strong> yes</p><p><strong>Unit Cost:</strong> &#8377;5.00</p>', 'Consumables item for Apparel & fashion | Qty: 15 | Recurring: yes');

-- Product: Drying trays (SKU: APP-072)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('drying-trays-app', 'physical', 'sell_on_site', 'APP-072', @cur_cat_id, 40.00, 40.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Drying trays', '<p><strong>Item:</strong> Drying trays</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-072</p><p><strong>Standard Quantity:</strong> 10</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;40.00</p>', 'Consumables item for Apparel & fashion | Qty: 10 | Recurring: No');

-- Product: Paint brushes - assorted sizes (SKU: APP-073)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('paint-brushes-assorted-sizes-app', 'physical', 'sell_on_site', 'APP-073', @cur_cat_id, 50.00, 50.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Paint brushes - assorted sizes', '<p><strong>Item:</strong> Paint brushes - assorted sizes</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-073</p><p><strong>Standard Quantity:</strong> 3 sets</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;50.00</p>', 'Consumables item for Apparel & fashion | Qty: 3 sets | Recurring: No');

-- Product: Sponges (SKU: APP-074)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('sponges-app', 'physical', 'sell_on_site', 'APP-074', @cur_cat_id, 15.00, 15.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Sponges', '<p><strong>Item:</strong> Sponges</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-074</p><p><strong>Standard Quantity:</strong> 30</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;15.00</p>', 'Consumables item for Apparel & fashion | Qty: 30 | Recurring: Yes');

-- Product: Strings roll (SKU: APP-075)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('strings-roll-app', 'physical', 'sell_on_site', 'APP-075', @cur_cat_id, 10.00, 10.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Strings roll', '<p><strong>Item:</strong> Strings roll</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-075</p><p><strong>Standard Quantity:</strong> 6</p><p><strong>Recurring Consumable:</strong> yes</p><p><strong>Periodic Consumable:</strong> yes</p><p><strong>Unit Cost:</strong> &#8377;10.00</p>', 'Consumables item for Apparel & fashion | Qty: 6 | Recurring: yes');

-- Product: feather (SKU: APP-076)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('feather-app', 'physical', 'sell_on_site', 'APP-076', @cur_cat_id, 20.00, 20.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'feather', '<p><strong>Item:</strong> feather</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-076</p><p><strong>Standard Quantity:</strong> 30 pckts</p><p><strong>Recurring Consumable:</strong> yes</p><p><strong>Periodic Consumable:</strong> yes</p><p><strong>Unit Cost:</strong> &#8377;20.00</p>', 'Consumables item for Apparel & fashion | Qty: 30 pckts | Recurring: yes');

-- Product: Plaster of Paris - mould making (SKU: APP-077)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('plaster-of-paris-mould-making-app', 'physical', 'sell_on_site', 'APP-077', @cur_cat_id, 100.00, 100.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Plaster of Paris - mould making', '<p><strong>Item:</strong> Plaster of Paris - mould making</p><p><strong>Category:</strong> Apparel & fashion</p><p><strong>SKU / Item Code:</strong> APP-077</p><p><strong>Standard Quantity:</strong> 5kgs</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;100.00</p>', 'Consumables item for Apparel & fashion | Qty: 5kgs | Recurring: Yes');


-- ============================================================================
-- FILE: BEAUTY AND WELLNESS.xlsx | CATEGORY: Beauty & wellness
-- ============================================================================
SET @cur_cat_id = (SELECT id FROM categories WHERE slug = 'beauty-wellness' LIMIT 1);

-- Product: Cosmetic mixing bowls (SKU: BW-001)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('cosmetic-mixing-bowls-bw', 'physical', 'sell_on_site', 'BW-001', @cur_cat_id, 40.00, 40.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Cosmetic mixing bowls', '<p><strong>Item:</strong> Cosmetic mixing bowls</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-001</p><p><strong>Standard Quantity:</strong> 5</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;40.00</p>', 'Consumables item for Beauty & wellness | Qty: 5 | Recurring: No');

-- Product: Stainless-steel bowls (SKU: BW-002)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('stainless-steel-bowls-bw', 'physical', 'sell_on_site', 'BW-002', @cur_cat_id, 50.00, 50.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Stainless-steel bowls', '<p><strong>Item:</strong> Stainless-steel bowls</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-002</p><p><strong>Standard Quantity:</strong> 5</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;50.00</p>', 'Consumables item for Beauty & wellness | Qty: 5 | Recurring: No');

-- Product: Measuring cups (SKU: BW-003)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('measuring-cups-bw', 'physical', 'sell_on_site', 'BW-003', @cur_cat_id, 70.00, 70.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Measuring cups', '<p><strong>Item:</strong> Measuring cups</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-003</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;70.00</p>', 'Consumables item for Beauty & wellness | Qty: 1 | Recurring: No');

-- Product: Measuring spoons (SKU: BW-004)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('measuring-spoons-bw', 'physical', 'sell_on_site', 'BW-004', @cur_cat_id, 80.00, 80.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Measuring spoons', '<p><strong>Item:</strong> Measuring spoons</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-004</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;80.00</p>', 'Consumables item for Beauty & wellness | Qty: 1 | Recurring: No');

-- Product: Digital weighing scales (SKU: BW-005)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('digital-weighing-scales-bw', 'physical', 'sell_on_site', 'BW-005', @cur_cat_id, 200.00, 200.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Digital weighing scales', '<p><strong>Item:</strong> Digital weighing scales</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-005</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;200.00</p>', 'Consumables item for Beauty & wellness | Qty: 1 | Recurring: No');

-- Product: Droppers / pipettes (SKU: BW-006)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('droppers-pipettes-bw', 'physical', 'sell_on_site', 'BW-006', @cur_cat_id, 50.00, 50.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Droppers / pipettes', '<p><strong>Item:</strong> Droppers / pipettes</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-006</p><p><strong>Standard Quantity:</strong> 5</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;50.00</p>', 'Consumables item for Beauty & wellness | Qty: 5 | Recurring: No');

-- Product: Small funnels (SKU: BW-007)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('small-funnels-bw', 'physical', 'sell_on_site', 'BW-007', @cur_cat_id, 20.00, 20.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Small funnels', '<p><strong>Item:</strong> Small funnels</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-007</p><p><strong>Standard Quantity:</strong> 10</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;20.00</p>', 'Consumables item for Beauty & wellness | Qty: 10 | Recurring: No');

-- Product: Fine sieves / strainers (SKU: BW-008)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('fine-sieves-strainers-bw', 'physical', 'sell_on_site', 'BW-008', @cur_cat_id, 20.00, 20.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Fine sieves / strainers', '<p><strong>Item:</strong> Fine sieves / strainers</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-008</p><p><strong>Standard Quantity:</strong> 5</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;20.00</p>', 'Consumables item for Beauty & wellness | Qty: 5 | Recurring: No');

-- Product: Spatulas / mixing sticks (SKU: BW-009)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('spatulas-mixing-sticks-bw', 'physical', 'sell_on_site', 'BW-009', @cur_cat_id, 30.00, 30.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Spatulas / mixing sticks', '<p><strong>Item:</strong> Spatulas / mixing sticks</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-009</p><p><strong>Standard Quantity:</strong> 10</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;30.00</p>', 'Consumables item for Beauty & wellness | Qty: 10 | Recurring: No');

-- Product: Cosmetic jars - empty (SKU: BW-010)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('cosmetic-jars-empty-bw', 'physical', 'sell_on_site', 'BW-010', @cur_cat_id, 50.00, 50.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Cosmetic jars - empty', '<p><strong>Item:</strong> Cosmetic jars - empty</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-010</p><p><strong>Standard Quantity:</strong> 15</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;50.00</p>', 'Consumables item for Beauty & wellness | Qty: 15 | Recurring: No');

-- Product: Spray bottles - empty (SKU: BW-011)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('spray-bottles-empty-bw', 'physical', 'sell_on_site', 'BW-011', @cur_cat_id, 0.00, 0.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Spray bottles - empty', '<p><strong>Item:</strong> Spray bottles - empty</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-011</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;0.00</p>', 'Consumables item for Beauty & wellness | Recurring: No');

-- Product: Soap moulds - reusable (SKU: BW-012)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('soap-moulds-reusable-bw', 'physical', 'sell_on_site', 'BW-012', @cur_cat_id, 70.00, 70.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Soap moulds - reusable', '<p><strong>Item:</strong> Soap moulds - reusable</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-012</p><p><strong>Standard Quantity:</strong> 5</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Periodic</p><p><strong>Unit Cost:</strong> &#8377;70.00</p>', 'Consumables item for Beauty & wellness | Qty: 5 | Recurring: No');

-- Product: Soap base - ready-to-use (SKU: BW-013)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('soap-base-ready-to-use-bw', 'physical', 'sell_on_site', 'BW-013', @cur_cat_id, 200.00, 200.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Soap base - ready-to-use', '<p><strong>Item:</strong> Soap base - ready-to-use</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-013</p><p><strong>Standard Quantity:</strong> 2</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;200.00</p>', 'Consumables item for Beauty & wellness | Qty: 2 | Recurring: Yes');

-- Product: Cosmetic spatulas (SKU: BW-014)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('cosmetic-spatulas-bw', 'physical', 'sell_on_site', 'BW-014', @cur_cat_id, 50.00, 50.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Cosmetic spatulas', '<p><strong>Item:</strong> Cosmetic spatulas</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-014</p><p><strong>Standard Quantity:</strong> 10</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;50.00</p>', 'Consumables item for Beauty & wellness | Qty: 10 | Recurring: No');

-- Product: Cosmetic brushes - assorted (SKU: BW-015)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('cosmetic-brushes-assorted-bw', 'physical', 'sell_on_site', 'BW-015', @cur_cat_id, 100.00, 100.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Cosmetic brushes - assorted', '<p><strong>Item:</strong> Cosmetic brushes - assorted</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-015</p><p><strong>Standard Quantity:</strong> 10</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;100.00</p>', 'Consumables item for Beauty & wellness | Qty: 10 | Recurring: No');

-- Product: Combs - assorted (SKU: BW-016)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('combs-assorted-bw', 'physical', 'sell_on_site', 'BW-016', @cur_cat_id, 20.00, 20.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Combs - assorted', '<p><strong>Item:</strong> Combs - assorted</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-016</p><p><strong>Standard Quantity:</strong> 3</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;20.00</p>', 'Consumables item for Beauty & wellness | Qty: 3 | Recurring: No');

-- Product: Hair brushes (SKU: BW-017)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('hair-brushes-bw', 'physical', 'sell_on_site', 'BW-017', @cur_cat_id, 40.00, 40.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Hair brushes', '<p><strong>Item:</strong> Hair brushes</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-017</p><p><strong>Standard Quantity:</strong> 5</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;40.00</p>', 'Consumables item for Beauty & wellness | Qty: 5 | Recurring: No');

-- Product: Spray bottles - hair demonstration (SKU: BW-018)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('spray-bottles-hair-demonstration-bw', 'physical', 'sell_on_site', 'BW-018', @cur_cat_id, 100.00, 100.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Spray bottles - hair demonstration', '<p><strong>Item:</strong> Spray bottles - hair demonstration</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-018</p><p><strong>Standard Quantity:</strong> 3</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;100.00</p>', 'Consumables item for Beauty & wellness | Qty: 3 | Recurring: No');

-- Product: Hair styling mannequins / heads (SKU: BW-019)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('hair-styling-mannequins-heads-bw', 'physical', 'sell_on_site', 'BW-019', @cur_cat_id, 500.00, 500.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Hair styling mannequins / heads', '<p><strong>Item:</strong> Hair styling mannequins / heads</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-019</p><p><strong>Standard Quantity:</strong> 3</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Periodic</p><p><strong>Unit Cost:</strong> &#8377;500.00</p>', 'Consumables item for Beauty & wellness | Qty: 3 | Recurring: No');

-- Product: Mannequin head stands (SKU: BW-020)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('mannequin-head-stands-bw', 'physical', 'sell_on_site', 'BW-020', @cur_cat_id, 200.00, 200.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Mannequin head stands', '<p><strong>Item:</strong> Mannequin head stands</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-020</p><p><strong>Standard Quantity:</strong> 3</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;200.00</p>', 'Consumables item for Beauty & wellness | Qty: 3 | Recurring: No');

-- Product: Hair sectioning clips (SKU: BW-021)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('hair-sectioning-clips-bw', 'physical', 'sell_on_site', 'BW-021', @cur_cat_id, 20.00, 20.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Hair sectioning clips', '<p><strong>Item:</strong> Hair sectioning clips</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-021</p><p><strong>Standard Quantity:</strong> 20</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;20.00</p>', 'Consumables item for Beauty & wellness | Qty: 20 | Recurring: No');

-- Product: Practice hair rollers (SKU: BW-022)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('practice-hair-rollers-bw', 'physical', 'sell_on_site', 'BW-022', @cur_cat_id, 100.00, 100.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Practice hair rollers', '<p><strong>Item:</strong> Practice hair rollers</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-022</p><p><strong>Standard Quantity:</strong> 5</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;100.00</p>', 'Consumables item for Beauty & wellness | Qty: 5 | Recurring: No');

-- Product: Basic hair styling tools - demonstration (SKU: BW-023)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('basic-hair-styling-tools-demonstration-bw', 'physical', 'sell_on_site', 'BW-023', @cur_cat_id, 300.00, 300.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Basic hair styling tools - demonstration', '<p><strong>Item:</strong> Basic hair styling tools - demonstration</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-023</p><p><strong>Standard Quantity:</strong> 3</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;300.00</p>', 'Consumables item for Beauty & wellness | Qty: 3 | Recurring: No');

-- Product: Nail files (SKU: BW-024)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('nail-files-bw', 'physical', 'sell_on_site', 'BW-024', @cur_cat_id, 500.00, 500.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Nail files', '<p><strong>Item:</strong> Nail files</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-024</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;500.00</p>', 'Consumables item for Beauty & wellness | Qty: 1 | Recurring: Yes');

-- Product: Foot-care demonstration kits (SKU: BW-025)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('foot-care-demonstration-kits-bw', 'physical', 'sell_on_site', 'BW-025', @cur_cat_id, 500.00, 500.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Foot-care demonstration kits', '<p><strong>Item:</strong> Foot-care demonstration kits</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-025</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;500.00</p>', 'Consumables item for Beauty & wellness | Qty: 1 | Recurring: No');

-- Product: Hand mirrors (SKU: BW-026)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('hand-mirrors-bw', 'physical', 'sell_on_site', 'BW-026', @cur_cat_id, 50.00, 50.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Hand mirrors', '<p><strong>Item:</strong> Hand mirrors</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-026</p><p><strong>Standard Quantity:</strong> 3</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;50.00</p>', 'Consumables item for Beauty & wellness | Qty: 3 | Recurring: No');

-- Product: pedicure water tub (SKU: BW-027)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('pedicure-water-tub-bw', 'physical', 'sell_on_site', 'BW-027', @cur_cat_id, 1000.00, 1000.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'pedicure water tub', '<p><strong>Item:</strong> pedicure water tub</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-027</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> no</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;1000.00</p>', 'Consumables item for Beauty & wellness | Qty: 1 | Recurring: no');

-- Product: hair dryer (SKU: BW-028)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('hair-dryer-bw', 'physical', 'sell_on_site', 'BW-028', @cur_cat_id, 1000.00, 1000.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'hair dryer', '<p><strong>Item:</strong> hair dryer</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-028</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> no</p><p><strong>Periodic Consumable:</strong> yes</p><p><strong>Unit Cost:</strong> &#8377;1000.00</p>', 'Consumables item for Beauty & wellness | Qty: 1 | Recurring: no');

-- Product: Facial care demonstration charts (SKU: BW-029)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('facial-care-demonstration-charts-bw', 'physical', 'sell_on_site', 'BW-029', @cur_cat_id, 500.00, 500.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Facial care demonstration charts', '<p><strong>Item:</strong> Facial care demonstration charts</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-029</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Periodic</p><p><strong>Unit Cost:</strong> &#8377;500.00</p>', 'Consumables item for Beauty & wellness | Qty: 1 | Recurring: No');

-- Product: Skin-type identification cards (SKU: BW-030)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('skin-type-identification-cards-bw', 'physical', 'sell_on_site', 'BW-030', @cur_cat_id, 500.00, 500.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Skin-type identification cards', '<p><strong>Item:</strong> Skin-type identification cards</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-030</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Periodic</p><p><strong>Unit Cost:</strong> &#8377;500.00</p>', 'Consumables item for Beauty & wellness | Qty: 1 | Recurring: No');

-- Product: Hair-type identification cards (SKU: BW-031)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('hair-type-identification-cards-bw', 'physical', 'sell_on_site', 'BW-031', @cur_cat_id, 100.00, 100.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Hair-type identification cards', '<p><strong>Item:</strong> Hair-type identification cards</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-031</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Periodic</p><p><strong>Unit Cost:</strong> &#8377;100.00</p>', 'Consumables item for Beauty & wellness | Qty: 1 | Recurring: No');

-- Product: Wellness habit cards (SKU: BW-032)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('wellness-habit-cards-bw', 'physical', 'sell_on_site', 'BW-032', @cur_cat_id, 100.00, 100.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Wellness habit cards', '<p><strong>Item:</strong> Wellness habit cards</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-032</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Periodic</p><p><strong>Unit Cost:</strong> &#8377;100.00</p>', 'Consumables item for Beauty & wellness | Qty: 1 | Recurring: No');

-- Product: Healthy lifestyle cards (SKU: BW-033)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('healthy-lifestyle-cards-bw', 'physical', 'sell_on_site', 'BW-033', @cur_cat_id, 100.00, 100.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Healthy lifestyle cards', '<p><strong>Item:</strong> Healthy lifestyle cards</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-033</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Periodic</p><p><strong>Unit Cost:</strong> &#8377;100.00</p>', 'Consumables item for Beauty & wellness | Qty: 1 | Recurring: No');

-- Product: Stress-management activity cards (SKU: BW-034)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('stress-management-activity-cards-bw', 'physical', 'sell_on_site', 'BW-034', @cur_cat_id, 100.00, 100.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Stress-management activity cards', '<p><strong>Item:</strong> Stress-management activity cards</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-034</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Periodic</p><p><strong>Unit Cost:</strong> &#8377;100.00</p>', 'Consumables item for Beauty & wellness | Qty: 1 | Recurring: No');

-- Product: Yoga / stretching instruction cards (SKU: BW-035)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('yoga-stretching-instruction-cards-bw', 'physical', 'sell_on_site', 'BW-035', @cur_cat_id, 300.00, 300.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Yoga / stretching instruction cards', '<p><strong>Item:</strong> Yoga / stretching instruction cards</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-035</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Periodic</p><p><strong>Unit Cost:</strong> &#8377;300.00</p>', 'Consumables item for Beauty & wellness | Qty: 1 | Recurring: No');

-- Product: Personal hygiene checklist (SKU: BW-036)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('personal-hygiene-checklist-bw', 'physical', 'sell_on_site', 'BW-036', @cur_cat_id, 100.00, 100.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Personal hygiene checklist', '<p><strong>Item:</strong> Personal hygiene checklist</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-036</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;100.00</p>', 'Consumables item for Beauty & wellness | Qty: 1 | Recurring: Yes');

-- Product: Skin-care routine worksheets (SKU: BW-037)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('skin-care-routine-worksheets-bw', 'physical', 'sell_on_site', 'BW-037', @cur_cat_id, 100.00, 100.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Skin-care routine worksheets', '<p><strong>Item:</strong> Skin-care routine worksheets</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-037</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;100.00</p>', 'Consumables item for Beauty & wellness | Qty: 1 | Recurring: Yes');

-- Product: Hair-care routine worksheets (SKU: BW-038)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('hair-care-routine-worksheets-bw', 'physical', 'sell_on_site', 'BW-038', @cur_cat_id, 100.00, 100.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Hair-care routine worksheets', '<p><strong>Item:</strong> Hair-care routine worksheets</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-038</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;100.00</p>', 'Consumables item for Beauty & wellness | Qty: 1 | Recurring: Yes');

-- Product: Chart paper (SKU: BW-039)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('chart-paper-bw', 'physical', 'sell_on_site', 'BW-039', @cur_cat_id, 15.00, 15.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Chart paper', '<p><strong>Item:</strong> Chart paper</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-039</p><p><strong>Standard Quantity:</strong> 10</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;15.00</p>', 'Consumables item for Beauty & wellness | Qty: 10 | Recurring: Yes');

-- Product: Coloured paper (SKU: BW-040)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('coloured-paper-bw', 'physical', 'sell_on_site', 'BW-040', @cur_cat_id, 60.00, 60.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Coloured paper', '<p><strong>Item:</strong> Coloured paper</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-040</p><p><strong>Standard Quantity:</strong> 5</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;60.00</p>', 'Consumables item for Beauty & wellness | Qty: 5 | Recurring: Yes');

-- Product: Scissors (SKU: BW-041)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('scissors-bw', 'physical', 'sell_on_site', 'BW-041', @cur_cat_id, 200.00, 200.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Scissors', '<p><strong>Item:</strong> Scissors</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-041</p><p><strong>Standard Quantity:</strong> 3</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;200.00</p>', 'Consumables item for Beauty & wellness | Qty: 3 | Recurring: No');

-- Product: Aprons / beauty-work gowns (SKU: BW-042)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('aprons-beauty-work-gowns-bw', 'physical', 'sell_on_site', 'BW-042', @cur_cat_id, 100.00, 100.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Aprons / beauty-work gowns', '<p><strong>Item:</strong> Aprons / beauty-work gowns</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-042</p><p><strong>Standard Quantity:</strong> 10</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;100.00</p>', 'Consumables item for Beauty & wellness | Qty: 10 | Recurring: No');

-- Product: Disposable gloves (SKU: BW-043)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('disposable-gloves-bw', 'physical', 'sell_on_site', 'BW-043', @cur_cat_id, 100.00, 100.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Disposable gloves', '<p><strong>Item:</strong> Disposable gloves</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-043</p><p><strong>Standard Quantity:</strong> 2</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;100.00</p>', 'Consumables item for Beauty & wellness | Qty: 2 | Recurring: Yes');

-- Product: Cleaning cloths (SKU: BW-044)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('cleaning-cloths-bw', 'physical', 'sell_on_site', 'BW-044', @cur_cat_id, 20.00, 20.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Cleaning cloths', '<p><strong>Item:</strong> Cleaning cloths</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-044</p><p><strong>Standard Quantity:</strong> 10</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;20.00</p>', 'Consumables item for Beauty & wellness | Qty: 10 | Recurring: Yes');

-- Product: Storage trays (SKU: BW-045)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('storage-trays-bw', 'physical', 'sell_on_site', 'BW-045', @cur_cat_id, 100.00, 100.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Storage trays', '<p><strong>Item:</strong> Storage trays</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-045</p><p><strong>Standard Quantity:</strong> 5</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;100.00</p>', 'Consumables item for Beauty & wellness | Qty: 5 | Recurring: No');

-- Product: Storage boxes (SKU: BW-046)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('storage-boxes-bw', 'physical', 'sell_on_site', 'BW-046', @cur_cat_id, 100.00, 100.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Storage boxes', '<p><strong>Item:</strong> Storage boxes</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-046</p><p><strong>Standard Quantity:</strong> 10</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;100.00</p>', 'Consumables item for Beauty & wellness | Qty: 10 | Recurring: No');

-- Product: Product labels / stickers (SKU: BW-047)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('product-labels-stickers-bw', 'physical', 'sell_on_site', 'BW-047', @cur_cat_id, 3.00, 3.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Product labels / stickers', '<p><strong>Item:</strong> Product labels / stickers</p><p><strong>Category:</strong> Beauty & wellness</p><p><strong>SKU / Item Code:</strong> BW-047</p><p><strong>Standard Quantity:</strong> 90</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;3.00</p>', 'Consumables item for Beauty & wellness | Qty: 90 | Recurring: Yes');


-- ============================================================================
-- FILE: Food production.xlsx | CATEGORY: Food production
-- ============================================================================
SET @cur_cat_id = (SELECT id FROM categories WHERE slug = 'food-production' LIMIT 1);

-- Product: Measuring cup sets (SKU: FP-001)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('measuring-cup-sets-fp', 'physical', 'sell_on_site', 'FP-001', @cur_cat_id, 70.00, 70.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Measuring cup sets', '<p><strong>Item:</strong> Measuring cup sets</p><p><strong>Category:</strong> Food production</p><p><strong>SKU / Item Code:</strong> FP-001</p><p><strong>Standard Quantity:</strong> 3</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;70.00</p>', 'Consumables item for Food production | Qty: 3 | Recurring: No');

-- Product: Measuring spoon sets (SKU: FP-002)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('measuring-spoon-sets-fp', 'physical', 'sell_on_site', 'FP-002', @cur_cat_id, 80.00, 80.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Measuring spoon sets', '<p><strong>Item:</strong> Measuring spoon sets</p><p><strong>Category:</strong> Food production</p><p><strong>SKU / Item Code:</strong> FP-002</p><p><strong>Standard Quantity:</strong> 3</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;80.00</p>', 'Consumables item for Food production | Qty: 3 | Recurring: No');

-- Product: Mixing bowls (SKU: FP-003)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('mixing-bowls-fp', 'physical', 'sell_on_site', 'FP-003', @cur_cat_id, 50.00, 50.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Mixing bowls', '<p><strong>Item:</strong> Mixing bowls</p><p><strong>Category:</strong> Food production</p><p><strong>SKU / Item Code:</strong> FP-003</p><p><strong>Standard Quantity:</strong> 10</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;50.00</p>', 'Consumables item for Food production | Qty: 10 | Recurring: No');

-- Product: Whisks (SKU: FP-004)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('whisks-fp', 'physical', 'sell_on_site', 'FP-004', @cur_cat_id, 100.00, 100.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Whisks', '<p><strong>Item:</strong> Whisks</p><p><strong>Category:</strong> Food production</p><p><strong>SKU / Item Code:</strong> FP-004</p><p><strong>Standard Quantity:</strong> 6</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;100.00</p>', 'Consumables item for Food production | Qty: 6 | Recurring: No');

-- Product: Spatulas (SKU: FP-005)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('spatulas-fp', 'physical', 'sell_on_site', 'FP-005', @cur_cat_id, 120.00, 120.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Spatulas', '<p><strong>Item:</strong> Spatulas</p><p><strong>Category:</strong> Food production</p><p><strong>SKU / Item Code:</strong> FP-005</p><p><strong>Standard Quantity:</strong> 6</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;120.00</p>', 'Consumables item for Food production | Qty: 6 | Recurring: No');

-- Product: Rolling pins (SKU: FP-006)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('rolling-pins-fp', 'physical', 'sell_on_site', 'FP-006', @cur_cat_id, 100.00, 100.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Rolling pins', '<p><strong>Item:</strong> Rolling pins</p><p><strong>Category:</strong> Food production</p><p><strong>SKU / Item Code:</strong> FP-006</p><p><strong>Standard Quantity:</strong> 6</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;100.00</p>', 'Consumables item for Food production | Qty: 6 | Recurring: No');

-- Product: Baking trays (SKU: FP-007)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('baking-trays-fp', 'physical', 'sell_on_site', 'FP-007', @cur_cat_id, 100.00, 100.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Baking trays', '<p><strong>Item:</strong> Baking trays</p><p><strong>Category:</strong> Food production</p><p><strong>SKU / Item Code:</strong> FP-007</p><p><strong>Standard Quantity:</strong> 6</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;100.00</p>', 'Consumables item for Food production | Qty: 6 | Recurring: No');

-- Product: Muffin / cupcake trays (SKU: FP-008)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('muffin-cupcake-trays-fp', 'physical', 'sell_on_site', 'FP-008', @cur_cat_id, 120.00, 120.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Muffin / cupcake trays', '<p><strong>Item:</strong> Muffin / cupcake trays</p><p><strong>Category:</strong> Food production</p><p><strong>SKU / Item Code:</strong> FP-008</p><p><strong>Standard Quantity:</strong> 3</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;120.00</p>', 'Consumables item for Food production | Qty: 3 | Recurring: No');

-- Product: Cake tins - assorted sizes (SKU: FP-009)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('cake-tins-assorted-sizes-fp', 'physical', 'sell_on_site', 'FP-009', @cur_cat_id, 400.00, 400.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Cake tins - assorted sizes', '<p><strong>Item:</strong> Cake tins - assorted sizes</p><p><strong>Category:</strong> Food production</p><p><strong>SKU / Item Code:</strong> FP-009</p><p><strong>Standard Quantity:</strong> 2</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;400.00</p>', 'Consumables item for Food production | Qty: 2 | Recurring: No');

-- Product: Cookie cutters - assorted (SKU: FP-010)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('cookie-cutters-assorted-fp', 'physical', 'sell_on_site', 'FP-010', @cur_cat_id, 120.00, 120.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Cookie cutters - assorted', '<p><strong>Item:</strong> Cookie cutters - assorted</p><p><strong>Category:</strong> Food production</p><p><strong>SKU / Item Code:</strong> FP-010</p><p><strong>Standard Quantity:</strong> 3</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;120.00</p>', 'Consumables item for Food production | Qty: 3 | Recurring: No');

-- Product: Pastry brushes (SKU: FP-011)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('pastry-brushes-fp', 'physical', 'sell_on_site', 'FP-011', @cur_cat_id, 60.00, 60.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Pastry brushes', '<p><strong>Item:</strong> Pastry brushes</p><p><strong>Category:</strong> Food production</p><p><strong>SKU / Item Code:</strong> FP-011</p><p><strong>Standard Quantity:</strong> 3</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;60.00</p>', 'Consumables item for Food production | Qty: 3 | Recurring: No');

-- Product: Piping nozzles (SKU: FP-012)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('piping-nozzles-fp', 'physical', 'sell_on_site', 'FP-012', @cur_cat_id, 40.00, 40.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Piping nozzles', '<p><strong>Item:</strong> Piping nozzles</p><p><strong>Category:</strong> Food production</p><p><strong>SKU / Item Code:</strong> FP-012</p><p><strong>Standard Quantity:</strong> 10</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;40.00</p>', 'Consumables item for Food production | Qty: 10 | Recurring: No');

-- Product: Kitchen weighing scales (SKU: FP-013)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('kitchen-weighing-scales-fp', 'physical', 'sell_on_site', 'FP-013', @cur_cat_id, 200.00, 200.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Kitchen weighing scales', '<p><strong>Item:</strong> Kitchen weighing scales</p><p><strong>Category:</strong> Food production</p><p><strong>SKU / Item Code:</strong> FP-013</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;200.00</p>', 'Consumables item for Food production | Qty: 1 | Recurring: No');

-- Product: Oven / OTG (SKU: FP-014)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('oven-otg-fp', 'physical', 'sell_on_site', 'FP-014', @cur_cat_id, 3000.00, 3000.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Oven / OTG', '<p><strong>Item:</strong> Oven / OTG</p><p><strong>Category:</strong> Food production</p><p><strong>SKU / Item Code:</strong> FP-014</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;3000.00</p>', 'Consumables item for Food production | Qty: 1 | Recurring: No');

-- Product: Food thermometer (SKU: FP-015)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('food-thermometer-fp', 'physical', 'sell_on_site', 'FP-015', @cur_cat_id, 150.00, 150.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Food thermometer', '<p><strong>Item:</strong> Food thermometer</p><p><strong>Category:</strong> Food production</p><p><strong>SKU / Item Code:</strong> FP-015</p><p><strong>Standard Quantity:</strong> 2</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;150.00</p>', 'Consumables item for Food production | Qty: 2 | Recurring: No');

-- Product: Oven mitts / heat-resistant gloves (SKU: FP-016)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('oven-mitts-heat-resistant-gloves-fp', 'physical', 'sell_on_site', 'FP-016', @cur_cat_id, 260.00, 260.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Oven mitts / heat-resistant gloves', '<p><strong>Item:</strong> Oven mitts / heat-resistant gloves</p><p><strong>Category:</strong> Food production</p><p><strong>SKU / Item Code:</strong> FP-016</p><p><strong>Standard Quantity:</strong> 3</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;260.00</p>', 'Consumables item for Food production | Qty: 3 | Recurring: No');

-- Product: Aprons (SKU: FP-017)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('aprons-fp', 'physical', 'sell_on_site', 'FP-017', @cur_cat_id, 140.00, 140.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Aprons', '<p><strong>Item:</strong> Aprons</p><p><strong>Category:</strong> Food production</p><p><strong>SKU / Item Code:</strong> FP-017</p><p><strong>Standard Quantity:</strong> 10</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;140.00</p>', 'Consumables item for Food production | Qty: 10 | Recurring: No');

-- Product: Chef caps / hair nets (SKU: FP-018)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('chef-caps-hair-nets-fp', 'physical', 'sell_on_site', 'FP-018', @cur_cat_id, 15.00, 15.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Chef caps / hair nets', '<p><strong>Item:</strong> Chef caps / hair nets</p><p><strong>Category:</strong> Food production</p><p><strong>SKU / Item Code:</strong> FP-018</p><p><strong>Standard Quantity:</strong> 10</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;15.00</p>', 'Consumables item for Food production | Qty: 10 | Recurring: Yes');

-- Product: Disposable gloves (SKU: FP-019)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('disposable-gloves-fp', 'physical', 'sell_on_site', 'FP-019', @cur_cat_id, 10.00, 10.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Disposable gloves', '<p><strong>Item:</strong> Disposable gloves</p><p><strong>Category:</strong> Food production</p><p><strong>SKU / Item Code:</strong> FP-019</p><p><strong>Standard Quantity:</strong> 20</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;10.00</p>', 'Consumables item for Food production | Qty: 20 | Recurring: Yes');

-- Product: Parchment / baking paper (SKU: FP-020)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('parchment-baking-paper-fp', 'physical', 'sell_on_site', 'FP-020', @cur_cat_id, 150.00, 150.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Parchment / baking paper', '<p><strong>Item:</strong> Parchment / baking paper</p><p><strong>Category:</strong> Food production</p><p><strong>SKU / Item Code:</strong> FP-020</p><p><strong>Standard Quantity:</strong> 3</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;150.00</p>', 'Consumables item for Food production | Qty: 3 | Recurring: Yes');

-- Product: pallate knives (SKU: FP-021)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('pallate-knives-fp', 'physical', 'sell_on_site', 'FP-021', @cur_cat_id, 120.00, 120.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'pallate knives', '<p><strong>Item:</strong> pallate knives</p><p><strong>Category:</strong> Food production</p><p><strong>SKU / Item Code:</strong> FP-021</p><p><strong>Standard Quantity:</strong> 3</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;120.00</p>', 'Consumables item for Food production | Qty: 3 | Recurring: Yes');

-- Product: Cake turntable (SKU: FP-022)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('cake-turntable-fp', 'physical', 'sell_on_site', 'FP-022', @cur_cat_id, 600.00, 600.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Cake turntable', '<p><strong>Item:</strong> Cake turntable</p><p><strong>Category:</strong> Food production</p><p><strong>SKU / Item Code:</strong> FP-022</p><p><strong>Standard Quantity:</strong> 2</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;600.00</p>', 'Consumables item for Food production | Qty: 2 | Recurring: Yes');

-- Product: Blender / mixer (SKU: FP-023)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('blender-mixer-fp', 'physical', 'sell_on_site', 'FP-023', @cur_cat_id, 600.00, 600.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Blender / mixer', '<p><strong>Item:</strong> Blender / mixer</p><p><strong>Category:</strong> Food production</p><p><strong>SKU / Item Code:</strong> FP-023</p><p><strong>Standard Quantity:</strong> 3</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;600.00</p>', 'Consumables item for Food production | Qty: 3 | Recurring: No');

-- Product: Chopping boards (SKU: FP-024)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('chopping-boards-fp', 'physical', 'sell_on_site', 'FP-024', @cur_cat_id, 100.00, 100.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Chopping boards', '<p><strong>Item:</strong> Chopping boards</p><p><strong>Category:</strong> Food production</p><p><strong>SKU / Item Code:</strong> FP-024</p><p><strong>Standard Quantity:</strong> 3</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;100.00</p>', 'Consumables item for Food production | Qty: 3 | Recurring: No');

-- Product: pH test strips (SKU: FP-025)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('ph-test-strips-fp', 'physical', 'sell_on_site', 'FP-025', @cur_cat_id, 100.00, 100.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'pH test strips', '<p><strong>Item:</strong> pH test strips</p><p><strong>Category:</strong> Food production</p><p><strong>SKU / Item Code:</strong> FP-025</p><p><strong>Standard Quantity:</strong> 3</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;100.00</p>', 'Consumables item for Food production | Qty: 3 | Recurring: Yes');

-- Product: Measuring cylinders (SKU: FP-026)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('measuring-cylinders-fp', 'physical', 'sell_on_site', 'FP-026', @cur_cat_id, 300.00, 300.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Measuring cylinders', '<p><strong>Item:</strong> Measuring cylinders</p><p><strong>Category:</strong> Food production</p><p><strong>SKU / Item Code:</strong> FP-026</p><p><strong>Standard Quantity:</strong> 3</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;300.00</p>', 'Consumables item for Food production | Qty: 3 | Recurring: No');


-- ============================================================================
-- FILE: HEALTH CARE FINAL.xlsx | CATEGORY: Health care
-- ============================================================================
SET @cur_cat_id = (SELECT id FROM categories WHERE slug = 'health-care' LIMIT 1);

-- Product: First-aid demonstration kits (SKU: HC-001)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('first-aid-demonstration-kits-hc', 'physical', 'sell_on_site', 'HC-001', @cur_cat_id, 600.00, 600.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'First-aid demonstration kits', '<p><strong>Item:</strong> First-aid demonstration kits</p><p><strong>Category:</strong> Health care</p><p><strong>SKU / Item Code:</strong> HC-001</p><p><strong>Standard Quantity:</strong> 3</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;600.00</p>', 'Consumables item for Health care | Qty: 3 | Recurring: No');

-- Product: Adhesive bandages / plasters (SKU: HC-002)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('adhesive-bandages-plasters-hc', 'physical', 'sell_on_site', 'HC-002', @cur_cat_id, 10.00, 10.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Adhesive bandages / plasters', '<p><strong>Item:</strong> Adhesive bandages / plasters</p><p><strong>Category:</strong> Health care</p><p><strong>SKU / Item Code:</strong> HC-002</p><p><strong>Standard Quantity:</strong> 10</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;10.00</p>', 'Consumables item for Health care | Qty: 10 | Recurring: Yes');

-- Product: Sterile gauze pads (SKU: HC-003)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('sterile-gauze-pads-hc', 'physical', 'sell_on_site', 'HC-003', @cur_cat_id, 10.00, 10.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Sterile gauze pads', '<p><strong>Item:</strong> Sterile gauze pads</p><p><strong>Category:</strong> Health care</p><p><strong>SKU / Item Code:</strong> HC-003</p><p><strong>Standard Quantity:</strong> 30</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;10.00</p>', 'Consumables item for Health care | Qty: 30 | Recurring: Yes');

-- Product: Cotton rolls (SKU: HC-004)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('cotton-rolls-hc', 'physical', 'sell_on_site', 'HC-004', @cur_cat_id, 15.00, 15.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Cotton rolls', '<p><strong>Item:</strong> Cotton rolls</p><p><strong>Category:</strong> Health care</p><p><strong>SKU / Item Code:</strong> HC-004</p><p><strong>Standard Quantity:</strong> 10</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;15.00</p>', 'Consumables item for Health care | Qty: 10 | Recurring: Yes');

-- Product: Crepe bandages (SKU: HC-005)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('crepe-bandages-hc', 'physical', 'sell_on_site', 'HC-005', @cur_cat_id, 50.00, 50.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Crepe bandages', '<p><strong>Item:</strong> Crepe bandages</p><p><strong>Category:</strong> Health care</p><p><strong>SKU / Item Code:</strong> HC-005</p><p><strong>Standard Quantity:</strong> 3</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;50.00</p>', 'Consumables item for Health care | Qty: 3 | Recurring: Yes');

-- Product: Triangular bandages (SKU: HC-006)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('triangular-bandages-hc', 'physical', 'sell_on_site', 'HC-006', @cur_cat_id, 20.00, 20.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Triangular bandages', '<p><strong>Item:</strong> Triangular bandages</p><p><strong>Category:</strong> Health care</p><p><strong>SKU / Item Code:</strong> HC-006</p><p><strong>Standard Quantity:</strong> 5</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;20.00</p>', 'Consumables item for Health care | Qty: 5 | Recurring: No');

-- Product: Elastic bandages (SKU: HC-007)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('elastic-bandages-hc', 'physical', 'sell_on_site', 'HC-007', @cur_cat_id, 30.00, 30.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Elastic bandages', '<p><strong>Item:</strong> Elastic bandages</p><p><strong>Category:</strong> Health care</p><p><strong>SKU / Item Code:</strong> HC-007</p><p><strong>Standard Quantity:</strong> 10</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;30.00</p>', 'Consumables item for Health care | Qty: 10 | Recurring: No');

-- Product: Disposable gloves (SKU: HC-008)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('disposable-gloves-hc', 'physical', 'sell_on_site', 'HC-008', @cur_cat_id, 5.00, 5.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Disposable gloves', '<p><strong>Item:</strong> Disposable gloves</p><p><strong>Category:</strong> Health care</p><p><strong>SKU / Item Code:</strong> HC-008</p><p><strong>Standard Quantity:</strong> 30</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;5.00</p>', 'Consumables item for Health care | Qty: 30 | Recurring: Yes');

-- Product: Disposable face masks (SKU: HC-009)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('disposable-face-masks-hc', 'physical', 'sell_on_site', 'HC-009', @cur_cat_id, 10.00, 10.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Disposable face masks', '<p><strong>Item:</strong> Disposable face masks</p><p><strong>Category:</strong> Health care</p><p><strong>SKU / Item Code:</strong> HC-009</p><p><strong>Standard Quantity:</strong> 30</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;10.00</p>', 'Consumables item for Health care | Qty: 30 | Recurring: Yes');

-- Product: Safety scissors (SKU: HC-010)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('safety-scissors-hc', 'physical', 'sell_on_site', 'HC-010', @cur_cat_id, 100.00, 100.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Safety scissors', '<p><strong>Item:</strong> Safety scissors</p><p><strong>Category:</strong> Health care</p><p><strong>SKU / Item Code:</strong> HC-010</p><p><strong>Standard Quantity:</strong> 3</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;100.00</p>', 'Consumables item for Health care | Qty: 3 | Recurring: No');

-- Product: Tweezers - demonstration use (SKU: HC-011)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('tweezers-demonstration-use-hc', 'physical', 'sell_on_site', 'HC-011', @cur_cat_id, 50.00, 50.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Tweezers - demonstration use', '<p><strong>Item:</strong> Tweezers - demonstration use</p><p><strong>Category:</strong> Health care</p><p><strong>SKU / Item Code:</strong> HC-011</p><p><strong>Standard Quantity:</strong> 3</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;50.00</p>', 'Consumables item for Health care | Qty: 3 | Recurring: No');

-- Product: Digital thermometers (SKU: HC-012)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('digital-thermometers-hc', 'physical', 'sell_on_site', 'HC-012', @cur_cat_id, 250.00, 250.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Digital thermometers', '<p><strong>Item:</strong> Digital thermometers</p><p><strong>Category:</strong> Health care</p><p><strong>SKU / Item Code:</strong> HC-012</p><p><strong>Standard Quantity:</strong> 3</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;250.00</p>', 'Consumables item for Health care | Qty: 3 | Recurring: No');

-- Product: Pulse oximeters (SKU: HC-013)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('pulse-oximeters-hc', 'physical', 'sell_on_site', 'HC-013', @cur_cat_id, 300.00, 300.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Pulse oximeters', '<p><strong>Item:</strong> Pulse oximeters</p><p><strong>Category:</strong> Health care</p><p><strong>SKU / Item Code:</strong> HC-013</p><p><strong>Standard Quantity:</strong> 3</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;300.00</p>', 'Consumables item for Health care | Qty: 3 | Recurring: No');

-- Product: Digital weighing scales (SKU: HC-014)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('digital-weighing-scales-hc', 'physical', 'sell_on_site', 'HC-014', @cur_cat_id, 300.00, 300.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Digital weighing scales', '<p><strong>Item:</strong> Digital weighing scales</p><p><strong>Category:</strong> Health care</p><p><strong>SKU / Item Code:</strong> HC-014</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;300.00</p>', 'Consumables item for Health care | Qty: 1 | Recurring: No');

-- Product: Height measuring scales / stadiometers (SKU: HC-015)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('height-measuring-scales-stadiometers-hc', 'physical', 'sell_on_site', 'HC-015', @cur_cat_id, 400.00, 400.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Height measuring scales / stadiometers', '<p><strong>Item:</strong> Height measuring scales / stadiometers</p><p><strong>Category:</strong> Health care</p><p><strong>SKU / Item Code:</strong> HC-015</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;400.00</p>', 'Consumables item for Health care | Qty: 1 | Recurring: No');

-- Product: Measuring tapes (SKU: HC-016)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('measuring-tapes-hc', 'physical', 'sell_on_site', 'HC-016', @cur_cat_id, 20.00, 20.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Measuring tapes', '<p><strong>Item:</strong> Measuring tapes</p><p><strong>Category:</strong> Health care</p><p><strong>SKU / Item Code:</strong> HC-016</p><p><strong>Standard Quantity:</strong> 3</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;20.00</p>', 'Consumables item for Health care | Qty: 3 | Recurring: No');

-- Product: Manual BP apparatus - demonstration (SKU: HC-017)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('manual-bp-apparatus-demonstration-hc', 'physical', 'sell_on_site', 'HC-017', @cur_cat_id, 2000.00, 2000.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Manual BP apparatus - demonstration', '<p><strong>Item:</strong> Manual BP apparatus - demonstration</p><p><strong>Category:</strong> Health care</p><p><strong>SKU / Item Code:</strong> HC-017</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;2000.00</p>', 'Consumables item for Health care | Qty: 1 | Recurring: No');

-- Product: Digital BP monitors (SKU: HC-018)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('digital-bp-monitors-hc', 'physical', 'sell_on_site', 'HC-018', @cur_cat_id, 1000.00, 1000.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Digital BP monitors', '<p><strong>Item:</strong> Digital BP monitors</p><p><strong>Category:</strong> Health care</p><p><strong>SKU / Item Code:</strong> HC-018</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;1000.00</p>', 'Consumables item for Health care | Qty: 1 | Recurring: No');

-- Product: Stethoscopes - demonstration (SKU: HC-019)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('stethoscopes-demonstration-hc', 'physical', 'sell_on_site', 'HC-019', @cur_cat_id, 2000.00, 2000.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Stethoscopes - demonstration', '<p><strong>Item:</strong> Stethoscopes - demonstration</p><p><strong>Category:</strong> Health care</p><p><strong>SKU / Item Code:</strong> HC-019</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;2000.00</p>', 'Consumables item for Health care | Qty: 1 | Recurring: No');

-- Product: Stopwatch / pulse timer (SKU: HC-020)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('stopwatch-pulse-timer-hc', 'physical', 'sell_on_site', 'HC-020', @cur_cat_id, 300.00, 300.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Stopwatch / pulse timer', '<p><strong>Item:</strong> Stopwatch / pulse timer</p><p><strong>Category:</strong> Health care</p><p><strong>SKU / Item Code:</strong> HC-020</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;300.00</p>', 'Consumables item for Health care | Qty: 1 | Recurring: No');

-- Product: Respiratory-rate observation cards (SKU: HC-021)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('respiratory-rate-observation-cards-hc', 'physical', 'sell_on_site', 'HC-021', @cur_cat_id, 100.00, 100.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Respiratory-rate observation cards', '<p><strong>Item:</strong> Respiratory-rate observation cards</p><p><strong>Category:</strong> Health care</p><p><strong>SKU / Item Code:</strong> HC-021</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Periodic</p><p><strong>Unit Cost:</strong> &#8377;100.00</p>', 'Consumables item for Health care | Qty: 1 | Recurring: No');

-- Product: Clinical examination torch (SKU: HC-022)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('clinical-examination-torch-hc', 'physical', 'sell_on_site', 'HC-022', @cur_cat_id, 50.00, 50.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Clinical examination torch', '<p><strong>Item:</strong> Clinical examination torch</p><p><strong>Category:</strong> Health care</p><p><strong>SKU / Item Code:</strong> HC-022</p><p><strong>Standard Quantity:</strong> 3</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;50.00</p>', 'Consumables item for Health care | Qty: 3 | Recurring: No');

-- Product: Medicine syringes - without needles (SKU: HC-023)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('medicine-syringes-without-needles-hc', 'physical', 'sell_on_site', 'HC-023', @cur_cat_id, 10.00, 10.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Medicine syringes - without needles', '<p><strong>Item:</strong> Medicine syringes - without needles</p><p><strong>Category:</strong> Health care</p><p><strong>SKU / Item Code:</strong> HC-023</p><p><strong>Standard Quantity:</strong> 30</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;10.00</p>', 'Consumables item for Health care | Qty: 30 | Recurring: No');

-- Product: Health record cards (SKU: HC-024)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('health-record-cards-hc', 'physical', 'sell_on_site', 'HC-024', @cur_cat_id, 100.00, 100.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Health record cards', '<p><strong>Item:</strong> Health record cards</p><p><strong>Category:</strong> Health care</p><p><strong>SKU / Item Code:</strong> HC-024</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;100.00</p>', 'Consumables item for Health care | Qty: 1 | Recurring: Yes');

-- Product: Health assessment forms (SKU: HC-025)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('health-assessment-forms-hc', 'physical', 'sell_on_site', 'HC-025', @cur_cat_id, 100.00, 100.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Health assessment forms', '<p><strong>Item:</strong> Health assessment forms</p><p><strong>Category:</strong> Health care</p><p><strong>SKU / Item Code:</strong> HC-025</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> Yes</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;100.00</p>', 'Consumables item for Health care | Qty: 1 | Recurring: Yes');

-- Product: First-aid scenario cards (SKU: HC-026)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('first-aid-scenario-cards-hc', 'physical', 'sell_on_site', 'HC-026', @cur_cat_id, 100.00, 100.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'First-aid scenario cards', '<p><strong>Item:</strong> First-aid scenario cards</p><p><strong>Category:</strong> Health care</p><p><strong>SKU / Item Code:</strong> HC-026</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Periodic</p><p><strong>Unit Cost:</strong> &#8377;100.00</p>', 'Consumables item for Health care | Qty: 1 | Recurring: No');

-- Product: Emergency situation cards (SKU: HC-027)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('emergency-situation-cards-hc', 'physical', 'sell_on_site', 'HC-027', @cur_cat_id, 100.00, 100.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Emergency situation cards', '<p><strong>Item:</strong> Emergency situation cards</p><p><strong>Category:</strong> Health care</p><p><strong>SKU / Item Code:</strong> HC-027</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Periodic</p><p><strong>Unit Cost:</strong> &#8377;100.00</p>', 'Consumables item for Health care | Qty: 1 | Recurring: No');

-- Product: CPR training manikin - child/teen (SKU: HC-028)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('cpr-training-manikin-childteen-hc', 'physical', 'sell_on_site', 'HC-028', @cur_cat_id, 7000.00, 7000.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'CPR training manikin - child/teen', '<p><strong>Item:</strong> CPR training manikin - child/teen</p><p><strong>Category:</strong> Health care</p><p><strong>SKU / Item Code:</strong> HC-028</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;7000.00</p>', 'Consumables item for Health care | Qty: 1 | Recurring: No');

-- Product: CPR instruction cards (SKU: HC-029)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('cpr-instruction-cards-hc', 'physical', 'sell_on_site', 'HC-029', @cur_cat_id, 100.00, 100.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'CPR instruction cards', '<p><strong>Item:</strong> CPR instruction cards</p><p><strong>Category:</strong> Health care</p><p><strong>SKU / Item Code:</strong> HC-029</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Periodic</p><p><strong>Unit Cost:</strong> &#8377;100.00</p>', 'Consumables item for Health care | Qty: 1 | Recurring: No');

-- Product: Health & safety posters (SKU: HC-030)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('health-and-safety-posters-hc', 'physical', 'sell_on_site', 'HC-030', @cur_cat_id, 100.00, 100.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Health & safety posters', '<p><strong>Item:</strong> Health & safety posters</p><p><strong>Category:</strong> Health care</p><p><strong>SKU / Item Code:</strong> HC-030</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Periodic</p><p><strong>Unit Cost:</strong> &#8377;100.00</p>', 'Consumables item for Health care | Qty: 1 | Recurring: No');

-- Product: Human body charts (SKU: HC-031)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('human-body-charts-hc', 'physical', 'sell_on_site', 'HC-031', @cur_cat_id, 100.00, 100.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Human body charts', '<p><strong>Item:</strong> Human body charts</p><p><strong>Category:</strong> Health care</p><p><strong>SKU / Item Code:</strong> HC-031</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Periodic</p><p><strong>Unit Cost:</strong> &#8377;100.00</p>', 'Consumables item for Health care | Qty: 1 | Recurring: No');

-- Product: Hand-washing demonstration kit (SKU: HC-032)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('hand-washing-demonstration-kit-hc', 'physical', 'sell_on_site', 'HC-032', @cur_cat_id, 100.00, 100.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Hand-washing demonstration kit', '<p><strong>Item:</strong> Hand-washing demonstration kit</p><p><strong>Category:</strong> Health care</p><p><strong>SKU / Item Code:</strong> HC-032</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;100.00</p>', 'Consumables item for Health care | Qty: 1 | Recurring: No');

-- Product: Waste segregation cards (SKU: HC-033)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('waste-segregation-cards-hc', 'physical', 'sell_on_site', 'HC-033', @cur_cat_id, 100.00, 100.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Waste segregation cards', '<p><strong>Item:</strong> Waste segregation cards</p><p><strong>Category:</strong> Health care</p><p><strong>SKU / Item Code:</strong> HC-033</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Periodic</p><p><strong>Unit Cost:</strong> &#8377;100.00</p>', 'Consumables item for Health care | Qty: 1 | Recurring: No');

-- Product: Personal hygiene scenario cards (SKU: HC-034)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('personal-hygiene-scenario-cards-hc', 'physical', 'sell_on_site', 'HC-034', @cur_cat_id, 100.00, 100.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Personal hygiene scenario cards', '<p><strong>Item:</strong> Personal hygiene scenario cards</p><p><strong>Category:</strong> Health care</p><p><strong>SKU / Item Code:</strong> HC-034</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Periodic</p><p><strong>Unit Cost:</strong> &#8377;100.00</p>', 'Consumables item for Health care | Qty: 1 | Recurring: No');

-- Product: Nutrition awareness cards (SKU: HC-035)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('nutrition-awareness-cards-hc', 'physical', 'sell_on_site', 'HC-035', @cur_cat_id, 100.00, 100.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Nutrition awareness cards', '<p><strong>Item:</strong> Nutrition awareness cards</p><p><strong>Category:</strong> Health care</p><p><strong>SKU / Item Code:</strong> HC-035</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Periodic</p><p><strong>Unit Cost:</strong> &#8377;100.00</p>', 'Consumables item for Health care | Qty: 1 | Recurring: No');

-- Product: Disease prevention cards (SKU: HC-036)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('disease-prevention-cards-hc', 'physical', 'sell_on_site', 'HC-036', @cur_cat_id, 100.00, 100.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Disease prevention cards', '<p><strong>Item:</strong> Disease prevention cards</p><p><strong>Category:</strong> Health care</p><p><strong>SKU / Item Code:</strong> HC-036</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Periodic</p><p><strong>Unit Cost:</strong> &#8377;100.00</p>', 'Consumables item for Health care | Qty: 1 | Recurring: No');

-- Product: Communicable disease awareness cards (SKU: HC-037)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('communicable-disease-awareness-cards-hc', 'physical', 'sell_on_site', 'HC-037', @cur_cat_id, 100.00, 100.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Communicable disease awareness cards', '<p><strong>Item:</strong> Communicable disease awareness cards</p><p><strong>Category:</strong> Health care</p><p><strong>SKU / Item Code:</strong> HC-037</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Periodic</p><p><strong>Unit Cost:</strong> &#8377;100.00</p>', 'Consumables item for Health care | Qty: 1 | Recurring: No');

-- Product: Health myth vs fact cards (SKU: HC-038)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('health-myth-vs-fact-cards-hc', 'physical', 'sell_on_site', 'HC-038', @cur_cat_id, 100.00, 100.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Health myth vs fact cards', '<p><strong>Item:</strong> Health myth vs fact cards</p><p><strong>Category:</strong> Health care</p><p><strong>SKU / Item Code:</strong> HC-038</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Periodic</p><p><strong>Unit Cost:</strong> &#8377;100.00</p>', 'Consumables item for Health care | Qty: 1 | Recurring: No');

-- Product: Aprons / health-work gowns (SKU: HC-039)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('aprons-health-work-gowns-hc', 'physical', 'sell_on_site', 'HC-039', @cur_cat_id, 140.00, 140.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Aprons / health-work gowns', '<p><strong>Item:</strong> Aprons / health-work gowns</p><p><strong>Category:</strong> Health care</p><p><strong>SKU / Item Code:</strong> HC-039</p><p><strong>Standard Quantity:</strong> 10</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;140.00</p>', 'Consumables item for Health care | Qty: 10 | Recurring: No');

-- Product: Safety goggles (SKU: HC-040)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('safety-goggles-hc', 'physical', 'sell_on_site', 'HC-040', @cur_cat_id, 100.00, 100.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Safety goggles', '<p><strong>Item:</strong> Safety goggles</p><p><strong>Category:</strong> Health care</p><p><strong>SKU / Item Code:</strong> HC-040</p><p><strong>Standard Quantity:</strong> 10</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;100.00</p>', 'Consumables item for Health care | Qty: 10 | Recurring: No');

-- Product: Protective gloves - reusable (SKU: HC-041)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('protective-gloves-reusable-hc', 'physical', 'sell_on_site', 'HC-041', @cur_cat_id, 10.00, 10.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Protective gloves - reusable', '<p><strong>Item:</strong> Protective gloves - reusable</p><p><strong>Category:</strong> Health care</p><p><strong>SKU / Item Code:</strong> HC-041</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;10.00</p>', 'Consumables item for Health care | Qty: 1 | Recurring: No');

-- Product: Storage trays (SKU: HC-042)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('storage-trays-hc', 'physical', 'sell_on_site', 'HC-042', @cur_cat_id, 100.00, 100.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Storage trays', '<p><strong>Item:</strong> Storage trays</p><p><strong>Category:</strong> Health care</p><p><strong>SKU / Item Code:</strong> HC-042</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;100.00</p>', 'Consumables item for Health care | Qty: 1 | Recurring: No');

-- Product: Storage boxes (SKU: HC-043)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('storage-boxes-hc', 'physical', 'sell_on_site', 'HC-043', @cur_cat_id, 100.00, 100.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Storage boxes', '<p><strong>Item:</strong> Storage boxes</p><p><strong>Category:</strong> Health care</p><p><strong>SKU / Item Code:</strong> HC-043</p><p><strong>Standard Quantity:</strong> 3</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> No</p><p><strong>Unit Cost:</strong> &#8377;100.00</p>', 'Consumables item for Health care | Qty: 3 | Recurring: No');

-- Product: Vaccine life-cycle flowchart (SKU: HC-044)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('vaccine-life-cycle-flowchart-hc', 'physical', 'sell_on_site', 'HC-044', @cur_cat_id, 100.00, 100.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Vaccine life-cycle flowchart', '<p><strong>Item:</strong> Vaccine life-cycle flowchart</p><p><strong>Category:</strong> Health care</p><p><strong>SKU / Item Code:</strong> HC-044</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Recurring Consumable:</strong> No</p><p><strong>Periodic Consumable:</strong> Yes</p><p><strong>Unit Cost:</strong> &#8377;100.00</p>', 'Consumables item for Health care | Qty: 1 | Recurring: No');

-- Product: Medicine & vaccine awareness posters (SKU: HC-045)
INSERT INTO products (slug, product_type, listing_type, sku, category_id, price, price_discounted, currency, discount_rate, vat_rate, user_id, status, is_promoted, visibility, rating, pageviews, demo_url, external_link, files_included, stock, shipping_delivery_time_id, multiple_sale, digital_file_download_link, country_id, state_id, city_id, address, zip_code, brand_id, is_sold, is_deleted, is_draft, is_edited, is_active, is_free_product, is_rejected, is_affiliate, is_commission_set, commission_rate, created_at, is_bundle, bundle_pricing_type, bundle_discount_rate)
VALUES ('medicine-and-vaccine-awareness-posters-hc', 'physical', 'sell_on_site', 'HC-045', @cur_cat_id, 100.00, 100.00, 'INR', 0, 0, 2, 1, 0, 1, '0', 0, '', '', '', 1000, 0, 1, '', 0, 0, 0, '', '', 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0.00, NOW(), 0, 'fixed', 0.00);
SET @new_prdt_id = LAST_INSERT_ID();
INSERT INTO product_details (product_id, lang_id, title, description, short_description)
VALUES (@new_prdt_id, 1, 'Medicine & vaccine awareness posters', '<p><strong>Item:</strong> Medicine & vaccine awareness posters</p><p><strong>Category:</strong> Health care</p><p><strong>SKU / Item Code:</strong> HC-045</p><p><strong>Standard Quantity:</strong> 1</p><p><strong>Unit Cost:</strong> &#8377;100.00</p>', 'Consumables item for Health care | Qty: 1');

SET FOREIGN_KEY_CHECKS = 1;
-- ============================================================================
-- IMPORT COMPLETE
-- ============================================================================