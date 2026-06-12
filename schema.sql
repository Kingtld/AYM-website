-- AYM Admin Database Schema
-- Run this in phpMyAdmin after creating your MySQL database

CREATE TABLE IF NOT EXISTS posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  caption TEXT,
  media_type VARCHAR(20) DEFAULT 'image',
  media_url VARCHAR(500) DEFAULT '',
  thumbnail_url VARCHAR(500) DEFAULT '',
  event_date VARCHAR(100) DEFAULT '',
  event_time VARCHAR(100) DEFAULT '',
  location VARCHAR(255) DEFAULT '',
  published TINYINT DEFAULT 1,
  sort_order INT DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) DEFAULT '',
  event_type VARCHAR(100) DEFAULT '',
  event_date VARCHAR(100) DEFAULT '',
  message TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS feedback (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  surname VARCHAR(255) DEFAULT '',
  rating INT DEFAULT 0,
  message TEXT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS settings (
  setting_key VARCHAR(100) PRIMARY KEY,
  setting_value TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default settings
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES
  ('events_count', '24'),
  ('members_count', '1200'),
  ('followers_count', '5600'),
  ('profile_bio', 'O MOHAU WA MODIMO');
