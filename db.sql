CREATE TABLE vendors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    category VARCHAR(255),  -- e.g., plumber, electrician, etc.
    latitude FLOAT(10,6),
    longitude FLOAT(10,6),
    contact_info VARCHAR(255)  -- e.g., phone number or email
);
ALTER TABLE vendors 
ADD COLUMN rating DECIMAL(2,1) DEFAULT 0,
ADD COLUMN price_range VARCHAR(50);
CREATE TABLE vendor_details (
    id INT PRIMARY KEY AUTO_INCREMENT,
    vendor_id INT,
    name VARCHAR(255),
    experience TEXT,
    category VARCHAR(50),
    rating DECIMAL(3, 2),
    location VARCHAR(255),
    FOREIGN KEY (vendor_id) REFERENCES vendors(id)
);
CREATE TABLE vendor_ratings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vendor_id INT,
    user_id INT,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    review TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vendor_id) REFERENCES vendors(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
CREATE TABLE service_vendors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    experience INT NOT NULL,
    skills TEXT,
    service_area VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE vendor_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vendor_id INT NOT NULL,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vendor_id) REFERENCES service_vendors(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
ALTER TABLE service_vendors ADD COLUMN email VARCHAR(255);
ALTER TABLE service_vendors
ADD COLUMN password VARCHAR(255) NOT NULL;
ALTER TABLE users
MODIFY role ENUM('user', 'vendor', 'service_vendor');
CREATE TABLE vendor_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vendor_id INT,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    review TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE vendor_jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vendor_id INT,
    job_title VARCHAR(255),
    job_description TEXT,
    completed_on DATE
);
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'vendor', 'service_vendor') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
INSERT INTO users (name, email, password, role) 
VALUES (?, ?, ?, ?);
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vendor_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image1 VARCHAR(255),
    category VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vendor_id) REFERENCES users(id)
);
CREATE TABLE service_vendors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    experience VARCHAR(100),
    skills TEXT,
    location VARCHAR(255),
    rating FLOAT DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vendor_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vendor_id) REFERENCES users(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
