-- =========================================================
-- OTP Login Database for XAMPP / phpMyAdmin
-- Database: otp_login_db
-- Purpose: Login-only system with prepared users + OTP + login logs
-- =========================================================

CREATE DATABASE IF NOT EXISTS otp_login_test
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE otp_login_test;

-- برای اجرای مجدد فایل بدون خطای Foreign Key
SET FOREIGN_KEY_CHECKS = 0;

DROP VIEW IF EXISTS v_login_history;
DROP TABLE IF EXISTS login_logs;
DROP TABLE IF EXISTS otp_codes;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

-- =========================================================
-- 1) Users Table
-- کاربران از قبل آماده هستند و Register نداریم
-- =========================================================

CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    mobile VARCHAR(20) NOT NULL UNIQUE,

    is_active TINYINT(1) NOT NULL DEFAULT 1,

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 2) OTP Codes Table
-- کدهای OTP فقط برای کاربران موجود ساخته می‌شوند
-- =========================================================

CREATE TABLE otp_codes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    user_id BIGINT UNSIGNED NOT NULL,

    otp_hash VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    is_used TINYINT(1) NOT NULL DEFAULT 0,
    attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_otp_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    INDEX idx_otp_user_id (user_id),
    INDEX idx_otp_expires_at (expires_at),
    INDEX idx_otp_is_used (is_used)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 3) Login Logs Table
-- هر ورود موفق کاربر اینجا ثبت می‌شود
-- =========================================================

CREATE TABLE login_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    user_id BIGINT UNSIGNED NOT NULL,

    mobile_snapshot VARCHAR(20) NOT NULL,

    login_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_login_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    INDEX idx_login_user_id (user_id),
    INDEX idx_login_at (login_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- Sample Prepared Users
-- این‌ها کاربران آماده هستند؛ سایت Register ندارد
-- =========================================================

INSERT INTO users (first_name, last_name, mobile, is_active)
VALUES
('محمد علی', 'مزدارانی', '091231224505', 1),
('مجتبئ', 'ارزیده', '09127093607', 1),
('نوید', 'احمدزاده', '09122950681', 1),

