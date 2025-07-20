-- Brain Swarm Real Estate Website Database Schema
-- Converted from Django models to MySQL
-- Compatible with Hostinger Premium Web Hosting

CREATE DATABASE IF NOT EXISTS brain_swarm;
USE brain_swarm;

-- Users table (replaces Django's auth_user)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(150) UNIQUE NOT NULL,
    email VARCHAR(254) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL, -- Will store hashed passwords
    first_name VARCHAR(150) DEFAULT '',
    last_name VARCHAR(150) DEFAULT '',
    is_active BOOLEAN DEFAULT TRUE,
    is_staff BOOLEAN DEFAULT FALSE,
    is_superuser BOOLEAN DEFAULT FALSE,
    date_joined DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_login DATETIME NULL,
    INDEX idx_username (username),
    INDEX idx_email (email)
);

-- User profiles table
CREATE TABLE profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE NOT NULL,
    full_name VARCHAR(100) DEFAULT '',
    profile_picture VARCHAR(255) DEFAULT '', -- Store relative path to uploaded image
    is_admin BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_is_admin (is_admin)
);

-- Event posts table
CREATE TABLE event (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    author_id INT NOT NULL,
    image VARCHAR(255) DEFAULT '', -- Store relative path to uploaded image
    publish_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_author (author_id),
    INDEX idx_publish_date (publish_date),
    INDEX idx_title (title)
);

-- Event contributors table
CREATE TABLE contributors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(254) DEFAULT '',
    github VARCHAR(255) DEFAULT '',
    linkedin VARCHAR(255) DEFAULT '',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES event(id) ON DELETE CASCADE,
    INDEX idx_event (event_id),
    INDEX idx_name (name)
);

-- Form submissions table
CREATE TABLE form_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    form_type ENUM('contact', 'meeting', 'home') NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(254) NOT NULL,
    message TEXT DEFAULT '',
    subject VARCHAR(200) DEFAULT '',
    phone VARCHAR(20) DEFAULT '',
    meeting_purpose ENUM('buy_kit', 'custom_project') DEFAULT NULL,
    preferred_date VARCHAR(50) DEFAULT '',
    submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_form_type (form_type),
    INDEX idx_submitted_at (submitted_at),
    INDEX idx_name (name),
    INDEX idx_email (email)
);

-- Session management table
CREATE TABLE user_sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id INT DEFAULT NULL,
    data TEXT,
    expires_at DATETIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_expires (expires_at),
    INDEX idx_user (user_id)
);

-- Insert default admin user
INSERT INTO users (username, email, password, first_name, last_name, is_staff, is_superuser) 
VALUES ('admin', 'admin@brainswarm.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'User', TRUE, TRUE);

-- Insert profile for admin user
INSERT INTO profiles (user_id, full_name, is_admin) 
VALUES (1, 'Admin User', TRUE);

-- Sample event post
INSERT INTO event (title, content, author_id, publish_date) VALUES 
('Welcome to Brain Swarm', 'Welcome to our innovative real estate platform. We are dedicated to transforming educational experiences and accelerating innovative research in the real estate sector.', 1, NOW());

-- Sample contributor for the event post
INSERT INTO contributors (event_id, name, email, github) VALUES 
(1, 'Brain Swarm Team', 'team@brainswarm.com', 'https://github.com/shanooo773');

-- Sample form submissions
INSERT INTO form_submissions (form_type, name, email, subject, message) VALUES 
('contact', 'John Doe', 'john@example.com', 'Inquiry about services', 'I am interested in learning more about your real estate solutions.'),
('home', 'Jane Smith', 'jane@example.com', 'General inquiry', 'Looking forward to collaborating with your team.');

INSERT INTO form_submissions (form_type, name, email, phone, meeting_purpose, preferred_date, message) VALUES 
('meeting', 'Mike Johnson', 'mike@example.com', '0300-123-4567', 'custom_project', '2025-02-15', 'I would like to discuss a custom robotics project for our real estate automation needs.');