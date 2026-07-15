CREATE DATABASE IF NOT EXISTS user_management
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE user_management;


-- =====================================
-- جدول کاربران
-- =====================================
CREATE TABLE users (
    national_id VARCHAR(10) NOT NULL,
    first_name  VARCHAR(100) NOT NULL,
    last_name   VARCHAR(100) NOT NULL,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (national_id),

    CONSTRAINT chk_users_national_id
        CHECK (national_id REGEXP '^[0-9]{10}$')
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;


-- =====================================
-- جدول اطلاعات ورود کاربر
-- ارتباط یک‌به‌یک با جدول users
-- =====================================
CREATE TABLE user_credentials (
    id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    national_id   VARCHAR(10) NOT NULL,
    username      VARCHAR(100) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP NOT NULL
                  DEFAULT CURRENT_TIMESTAMP
                  ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    UNIQUE KEY uq_user_credentials_national_id (national_id),
    UNIQUE KEY uq_user_credentials_username (username),

    CONSTRAINT fk_user_credentials_user
        FOREIGN KEY (national_id)
        REFERENCES users (national_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;


-- =====================================
-- جدول OTP
-- هر کاربر می‌تواند چند OTP داشته باشد
-- =====================================
CREATE TABLE user_otps (
    id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    national_id VARCHAR(10) NOT NULL,
    mobile      VARCHAR(11) NOT NULL,
    otp_code    VARCHAR(10) NOT NULL,
    expired_at  DATETIME NOT NULL,
    is_used     TINYINT(1) NOT NULL DEFAULT 0,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    KEY idx_user_otps_national_id (national_id),
    KEY idx_user_otps_mobile (mobile),
    KEY idx_user_otps_expired_at (expired_at),

    CONSTRAINT fk_user_otps_user
        FOREIGN KEY (national_id)
        REFERENCES users (national_id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT chk_user_otps_mobile
        CHECK (mobile REGEXP '^09[0-9]{9}$')
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;