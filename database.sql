CREATE DATABASE IF NOT EXISTS secure_file_vault
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE secure_file_vault;

CREATE TABLE IF NOT EXISTS users (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(30)  NOT NULL UNIQUE,
    email         VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS files (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id       INT UNSIGNED NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    stored_name   VARCHAR(255) NOT NULL UNIQUE,   
    iv            VARCHAR(64)  NOT NULL,          
    filesize      BIGINT UNSIGNED NOT NULL,
    mime_type     VARCHAR(150) NOT NULL,
    uploaded_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_files_user FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE,
    INDEX idx_files_user (user_id)
) ENGINE=InnoDB;
