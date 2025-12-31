-- Eleni Mekuria Meal Planning Platform - MySQL Database Setup
-- Copy and paste all SQL statements below into your cPanel MySQL

-- Create Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    age INT,
    weight DECIMAL(10,2),
    height DECIMAL(10,2),
    goal TEXT,
    package VARCHAR(100),
    status VARCHAR(50) DEFAULT 'pending',
    role VARCHAR(50) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create Site Settings Table
CREATE TABLE IF NOT EXISTS site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    key VARCHAR(255) UNIQUE NOT NULL,
    value LONGTEXT
);

-- Create Hero Slides Table
CREATE TABLE IF NOT EXISTS hero_slides (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_url LONGTEXT,
    title_main VARCHAR(255),
    title_accent VARCHAR(255),
    subtitle LONGTEXT,
    display_order INT DEFAULT 0
);

-- Create Meal Plans Table
CREATE TABLE IF NOT EXISTS meal_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    package_type VARCHAR(100),
    price DECIMAL(10,2) DEFAULT 0,
    file_url LONGTEXT,
    preview_image LONGTEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create Payment Options Table
CREATE TABLE IF NOT EXISTS payment_options (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    account_number VARCHAR(255) NOT NULL,
    account_holder VARCHAR(255) NOT NULL,
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create Payments Table
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    receipt_path LONGTEXT,
    trx_number VARCHAR(255),
    payment_method_id INT,
    status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (payment_method_id) REFERENCES payment_options(id)
);

-- Create Testimonials Table
CREATE TABLE IF NOT EXISTS testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_name VARCHAR(255),
    quote LONGTEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create Certificates Table
CREATE TABLE IF NOT EXISTS certificates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    image_url LONGTEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ========== INSERT DEFAULT DATA ==========

-- Insert Admin User (password: admin123)
INSERT INTO users (name, email, password, role, status) 
VALUES ('Admin', 'admin@example.com', '$2y$10$qdcgsQ14xWKBMJcSzdwiBOVBwAIsscuoRfleqDjCBjETBY23bk7iq', 'admin', 'approved')
ON DUPLICATE KEY UPDATE id=id;

-- Insert Sample Meal Plans
INSERT INTO meal_plans (title, package_type, price, file_url, preview_image) VALUES
('Weight Loss Plan', 'weight_loss', 499.99, 'protected_files/plan_1_1767172800.pdf', 'uploads/previews/preview_1_1767172800_weight_loss.jpg'),
('Muscle Gain Plan', 'muscle_gain', 599.99, 'protected_files/plan_2_1767172800.pdf', 'uploads/previews/preview_2_1767172800_muscle_gain.jpg'),
('Sports Performance', 'sports_performance', 699.99, 'protected_files/plan_3_1767172800.pdf', 'uploads/previews/preview_3_1767172800_sports.jpg');

-- Insert Payment Options
INSERT INTO payment_options (name, account_number, account_holder, display_order, is_active) VALUES
('TeleBirr', '0911234567', 'Eleni Mekuria', 1, TRUE),
('CBE Transfer', '1000234567890', 'Eleni Mekuria', 2, TRUE),
('Dashen Bank', '0801234567', 'Eleni Mekuria', 3, TRUE);

-- Insert Sample Site Settings
INSERT INTO site_settings (key, value) VALUES
('hero_bg_image', 'attached_assets/stock_images/modern_nutrition_hea_7726d1af.jpg'),
('about_image', 'attached_assets/stock_images/professional_dietiti_8bb8decd.jpg'),
('about_philosophy', 'Science & Empathy'),
('about_detailed_bio', 'MSc from Addis Ababa University, Afrihealth TV host, and founder of EDPA.'),
('footer_address', 'Addis Ababa, Ethiopia'),
('contact_phone', '+251 911 000 000'),
('footer_email', 'info@elenimekuria.com'),
('footer_tiktok', 'https://tiktok.com/@elenimekuria'),
('contact_social_ig', 'https://instagram.com/elenimekuria'),
('contact_whatsapp_link', 'https://wa.me/251911000000'),
('footer_telegram', 'https://t.me/elenimekuria');
