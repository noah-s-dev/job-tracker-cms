-- Job Application Tracker CMS Database Setup
-- Run this script to create the database and tables

CREATE DATABASE IF NOT EXISTS job_tracker_cms;
USE job_tracker_cms;

-- Users table for authentication
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Job applications table
CREATE TABLE IF NOT EXISTS job_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    company_name VARCHAR(200) NOT NULL,
    job_title VARCHAR(200) NOT NULL,
    job_description TEXT,
    application_date DATE NOT NULL,
    status ENUM('applied', 'interview_scheduled', 'interviewed', 'offer_received', 'rejected', 'withdrawn') DEFAULT 'applied',
    salary_range VARCHAR(100),
    job_location VARCHAR(200),
    job_url VARCHAR(500),
    contact_person VARCHAR(100),
    contact_email VARCHAR(100),
    contact_phone VARCHAR(20),
    follow_up_date DATE,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_status (user_id, status),
    INDEX idx_application_date (application_date),
    INDEX idx_follow_up_date (follow_up_date)
);

-- Application status history table (optional - for tracking status changes)
CREATE TABLE IF NOT EXISTS application_status_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    application_id INT NOT NULL,
    old_status VARCHAR(50),
    new_status VARCHAR(50) NOT NULL,
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notes TEXT,
    FOREIGN KEY (application_id) REFERENCES job_applications(id) ON DELETE CASCADE
);

-- Insert sample user (password: demo123)
INSERT INTO users (username, email, password_hash, first_name, last_name) 
VALUES ('demo_user', 'demo@example.com', '$2y$10$evL0y3Jmdux6V3BcqNZ6RuRcA53fpiNDahMlR2u16pzJvsXaFDJgu', 'John', 'Doe');

-- Insert sample job applications
INSERT INTO job_applications (user_id, company_name, job_title, job_description, application_date, status, salary_range, job_location, job_url, contact_person, contact_email, follow_up_date, notes) VALUES
(1, 'TechCorp Inc.', 'Senior Software Developer', 'Looking for an experienced developer to join our team working on cutting-edge web applications using modern technologies.', '2024-01-15', 'interviewed', '$80,000 - $100,000', 'San Francisco, CA', 'https://techcorp.com/careers/senior-dev', 'Jane Smith', 'jane.smith@techcorp.com', '2024-01-25', 'Great interview, waiting for feedback. Team seems very collaborative.'),
(1, 'StartupXYZ', 'Full Stack Developer', 'Join our fast-growing startup as a full-stack developer. Work with React, Node.js, and cloud technologies.', '2024-01-10', 'offer_received', '$70,000 - $90,000', 'Remote', 'https://startupxyz.com/jobs/fullstack', 'Mike Johnson', 'mike@startupxyz.com', NULL, 'Received offer! Need to negotiate salary and review benefits package.'),
(1, 'BigTech Solutions', 'Frontend Developer', 'Frontend developer position focusing on React and modern JavaScript frameworks. Great benefits and work-life balance.', '2024-01-08', 'rejected', '$75,000 - $95,000', 'New York, NY', 'https://bigtech.com/careers/frontend', 'Sarah Wilson', 'sarah.wilson@bigtech.com', NULL, 'Unfortunately did not move forward. Feedback was positive but they went with someone with more React experience.'),
(1, 'InnovateLabs', 'Backend Developer', 'Backend developer role working with Python, Django, and PostgreSQL. Exciting projects in AI and machine learning.', '2024-01-20', 'applied', '$85,000 - $110,000', 'Austin, TX', 'https://innovatelabs.com/jobs/backend', 'David Brown', 'david.brown@innovatelabs.com', '2024-01-30', 'Just applied. Company looks very promising with great tech stack and interesting projects.'),
(1, 'CloudFirst Technologies', 'DevOps Engineer', 'DevOps engineer position focusing on AWS, Docker, and Kubernetes. Remote-first company with flexible hours.', '2024-01-12', 'interview_scheduled', '$90,000 - $120,000', 'Remote', 'https://cloudfirst.com/careers/devops', 'Lisa Chen', 'lisa.chen@cloudfirst.com', '2024-01-28', 'Phone screening went well. Technical interview scheduled for next week.');

