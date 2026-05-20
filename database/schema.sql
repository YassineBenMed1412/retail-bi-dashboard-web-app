-- Create Database
CREATE DATABASE IF NOT EXISTS retail_bi_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE retail_bi_db;

-- Roles Table
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL,
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Companies Table
CREATE TABLE companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    phone VARCHAR(20),
    address TEXT,
    tax_id VARCHAR(50),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    company_id INT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
);

-- Categories Table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products Table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    category_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    sku VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock_quantity INT DEFAULT 0,
    min_stock_level INT DEFAULT 10,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Customers Table
CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
);

-- Sales Table
CREATE TABLE sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    customer_id INT,
    user_id INT NOT NULL,
    invoice_number VARCHAR(100) UNIQUE NOT NULL,
    sale_date DATE NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    discount DECIMAL(10,2) DEFAULT 0,
    tax DECIMAL(10,2) DEFAULT 0,
    payment_method VARCHAR(50) DEFAULT 'cash',
    status VARCHAR(20) DEFAULT 'completed',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Sale Products (Pivot)
CREATE TABLE sale_products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- INSERT SAMPLE DATA
INSERT INTO roles (name, description) VALUES
('admin', 'Administrator with full access'),
('company', 'Company user with limited access'),
('guest', 'Guest with no dashboard access');

INSERT INTO companies (name, email, phone, address) VALUES
('Retail Store ABC', 'contact@retailabc.com', '555-0123', '123 Main Street');

INSERT INTO categories (name, description) VALUES
('Electronics', 'Electronic products'),
('Clothing', 'Apparel and fashion'),
('Food', 'Food and beverages'),
('Home & Garden', 'Home improvement');

-- Create admin user (password: password)
INSERT INTO users (role_id, company_id, name, email, password, phone) VALUES
(1, NULL, 'Admin User', 'admin@retailbi.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1234567890');

-- Create company user (password: password)
INSERT INTO users (role_id, company_id, name, email, password, phone) VALUES
(2, 1, 'Yassine Ben Mohamed', 'yassinebenmohamed@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0987654321');

-- Insert sample products
INSERT INTO products (company_id, category_id, name, sku, price, stock_quantity) VALUES
(1, 1, 'Smartphone', 'SMP1001', 699.99, 50),
(1, 1, 'Laptop', 'LPT1002', 1299.99, 30),
(1, 2, 'T-Shirt', 'TSH1003', 29.99, 200),
(1, 2, 'Jeans', 'JNS1004', 59.99, 150),
(1, 3, 'Coffee', 'COF1005', 12.99, 300);

-- Insert sample customers
INSERT INTO customers (company_id, name, email, phone) VALUES
(1, 'Customer 1', 'customer1@email.com', '555-0001'),
(1, 'Customer 2', 'customer2@email.com', '555-0002'),
(1, 'Customer 3', 'customer3@email.com', '555-0003');

-- Insert sample sales
INSERT INTO sales (company_id, customer_id, user_id, invoice_number, sale_date, total_amount, payment_method) VALUES
(1, 1, 2, 'INV-2024-0001', CURDATE() - INTERVAL 5 DAY, 149.98, 'card'),
(1, 2, 2, 'INV-2024-0002', CURDATE() - INTERVAL 3 DAY, 699.99, 'cash'),
(1, 1, 2, 'INV-2024-0003', CURDATE() - INTERVAL 1 DAY, 89.97, 'card');

-- Insert sale products
INSERT INTO sale_products (sale_id, product_id, quantity, unit_price, subtotal) VALUES
(1, 3, 2, 29.99, 59.98),
(1, 4, 1, 59.99, 59.99),
(2, 1, 1, 699.99, 699.99),
(3, 3, 3, 29.99, 89.97);