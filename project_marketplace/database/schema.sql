CREATE DATABASE IF NOT EXISTS turnkey_marketplace CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE turnkey_marketplace;

-- 1. Users Table (Buyers, Sellers, Admins)
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(191) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('buyer', 'seller', 'admin') DEFAULT 'buyer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Turnkey Projects Table
CREATE TABLE listings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    seller_id INT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    tagline VARCHAR(255) NOT NULL,
    description LONGTEXT NOT NULL,
    category ENUM('SaaS', 'E-commerce', 'Mobile App', 'Turnkey Web Tool', 'Content/Blog') NOT NULL,
    asking_price DECIMAL(12, 2) NOT NULL,
    monthly_revenue DECIMAL(10, 2) DEFAULT 0.00,
    monthly_profit DECIMAL(10, 2) DEFAULT 0.00,
    primary_tech_stack VARCHAR(255) NOT NULL,
    demo_url VARCHAR(255) NULL,
    included_assets TEXT NOT NULL, -- e.g., "Domain, Source Code, Database, Branding assets, 30-day support"
    status ENUM('available', 'escrow_pending', 'sold') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 3. Acquisition & Escrow Transactions
CREATE TABLE transactions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    listing_id INT UNSIGNED NOT NULL,
    buyer_id INT UNSIGNED NOT NULL,
    seller_id INT UNSIGNED NOT NULL,
    transaction_uuid CHAR(36) NOT NULL UNIQUE,
    agreed_price DECIMAL(12, 2) NOT NULL,
    escrow_status ENUM('initiated', 'funds_held', 'assets_transferred', 'completed', 'disputed', 'cancelled') DEFAULT 'initiated',
    handover_notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE RESTRICT,
    FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB;