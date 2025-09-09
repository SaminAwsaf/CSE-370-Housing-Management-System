-- Create the database
CREATE DATABASE RealEstateDB;
USE RealEstateDB;


-- User related tables


CREATE TABLE Admin (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone_number VARCHAR(20),
    password VARCHAR(255) NOT NULL
);

CREATE TABLE User (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone_number VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    nid VARCHAR(50),
    admin_id INT,
    FOREIGN KEY (admin_id) REFERENCES Admin(admin_id)
);

CREATE TABLE Owner (
    owner_id INT PRIMARY KEY,
    FOREIGN KEY (owner_id) REFERENCES User(user_id) ON DELETE CASCADE
);

CREATE TABLE Customer (
    customer_id INT PRIMARY KEY,
    FOREIGN KEY (customer_id) REFERENCES User(user_id) ON DELETE CASCADE
);


-- Property related


CREATE TABLE Property (
    property_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    property_type VARCHAR(50) NOT NULL,
    location VARCHAR(200),
    utility_cost DECIMAL(10,2),
    furnished BOOLEAN,
    parking BOOLEAN,
    status VARCHAR(50), -- available/sold/rented
    price DECIMAL(12,2),
    owner_id INT,
    customer_id INT,
    FOREIGN KEY (owner_id) REFERENCES Owner(owner_id),
    FOREIGN KEY (customer_id) REFERENCES Customer(customer_id)
);

CREATE TABLE Photos (
    photo_id INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT,
    photo_url VARCHAR(255),
    FOREIGN KEY (property_id) REFERENCES Property(property_id) ON DELETE CASCADE
);


-- History & Transactions


CREATE TABLE History (
    transaction_id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_type VARCHAR(50),
    pay_status VARCHAR(50),
    owner_id INT,
    customer_id INT,
    FOREIGN KEY (owner_id) REFERENCES Owner(owner_id),
    FOREIGN KEY (customer_id) REFERENCES Customer(customer_id)
);

ALTER TABLE History ADD property_id INT; ALTER TABLE History ADD FOREIGN KEY (property_id) REFERENCES Property(property_id);
-- Reviews & Comments


CREATE TABLE Review_Ratings (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    review_date DATE,
    property_id INT,
    customer_id INT,
    admin_id INT,
    FOREIGN KEY (property_id) REFERENCES Property(property_id),
    FOREIGN KEY (customer_id) REFERENCES Customer(customer_id),
    FOREIGN KEY (admin_id) REFERENCES Admin(admin_id)
);

CREATE TABLE Comments (
    comment_id INT AUTO_INCREMENT PRIMARY KEY,
    review_id INT,
    comment_text TEXT,
    FOREIGN KEY (review_id) REFERENCES Review_Ratings(review_id) ON DELETE CASCADE
);


-- Wishlist


CREATE TABLE Wishlist (
    wishlist_id INT AUTO_INCREMENT PRIMARY KEY,
    saved_date DATE,
    customer_id INT,
    FOREIGN KEY (customer_id) REFERENCES Customer(customer_id)
);

CREATE TABLE Wishlist_Has (
    property_id INT,
    wishlist_id INT,
    PRIMARY KEY (property_id, wishlist_id),
    FOREIGN KEY (property_id) REFERENCES Property(property_id) ON DELETE CASCADE,
    FOREIGN KEY (wishlist_id) REFERENCES Wishlist(wishlist_id) ON DELETE CASCADE
);
