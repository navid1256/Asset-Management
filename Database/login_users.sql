CREATE DATABASE IF NOT EXISTS asset_managment
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE asset_managment;


-- =========================================
-- جدول اطلاعات اصلی کاربران
-- =========================================
CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    national_id VARCHAR(10) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    UNIQUE KEY uq_users_national_id (national_id)
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


-- =========================================
-- جدول اطلاعات ورود کاربران
-- ارتباط یک‌به‌یک با users
-- =========================================
CREATE TABLE IF NOT EXISTS user_credentials (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    national_id VARCHAR(10) NOT NULL,
    username VARCHAR(100) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL
        DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    UNIQUE KEY uq_credentials_national_id (national_id),
    UNIQUE KEY uq_credentials_username (username),

    CONSTRAINT fk_credentials_national_id
        FOREIGN KEY (national_id)
        REFERENCES users (national_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


-- =========================================
-- جدول کدهای OTP
-- هر کاربر می‌تواند چند OTP داشته باشد
-- =========================================
CREATE TABLE IF NOT EXISTS user_otps (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    national_id VARCHAR(10) NOT NULL,
    mobile VARCHAR(11) NOT NULL,
    otp_code VARCHAR(10) NOT NULL,
    expired_at DATETIME NOT NULL,
    is_used TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    KEY idx_otps_national_id (national_id),
    KEY idx_otps_mobile (mobile),
    KEY idx_otps_expired_at (expired_at),

    CONSTRAINT fk_otps_national_id
        FOREIGN KEY (national_id)
        REFERENCES users (national_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;