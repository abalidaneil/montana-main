-- Complete Database Schema for Firstworldchoice Banking Application
-- Database Name: montana

CREATE DATABASE IF NOT EXISTS montana;
USE montana;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fname VARCHAR(50) NOT NULL,
    lname VARCHAR(50) NOT NULL,
    email VARCHAR(50) NOT NULL UNIQUE,
    phone VARCHAR(20),
    gender VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    address TEXT,
    country VARCHAR(50),
    state VARCHAR(50),
    zip VARCHAR(20),
    type VARCHAR(20) DEFAULT 'savings',
    discription TEXT,
    balance DECIMAL(15, 2) DEFAULT 0.00,
    verify_status VARCHAR(20) DEFAULT 'Unverified',
    account_number VARCHAR(50),
    login_code INT(4)
);

-- Loans table
CREATE TABLE loans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(15, 2) NOT NULL,
    status VARCHAR(20) DEFAULT 'Approved',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Withdrawals table
CREATE TABLE withdrawals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(15, 2) NOT NULL,
    bank_name VARCHAR(100),
    account_number VARCHAR(50) NOT NULL,
    routing_number VARCHAR(50) NOT NULL,
    status ENUM('Pending', 'Approved', 'Declined') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Messages table for chat
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    sender ENUM('user', 'admin') NOT NULL,
    message_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Images table for user profile images
CREATE TABLE images (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    image1 LONGBLOB NOT NULL,
    image2 LONGBLOB NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Verify status table for verification images
CREATE TABLE verify_status (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    image1 LONGBLOB NOT NULL,
    image2 LONGBLOB NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Admins table
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Insert default admin user
INSERT INTO admins (username, password) VALUES ('admin', 'admin123');</content>
<parameter name="filePath">c:\Users\USER\Documents\server\root\montana-main\index.sql


ALTER TABLE withdrawals ADD COLUMN swift_code VARCHAR(50);

-- Or if you need to modify the entire withdrawals table:
ALTER TABLE withdrawals 
ADD COLUMN swift_code VARCHAR(50) AFTER bank_name;