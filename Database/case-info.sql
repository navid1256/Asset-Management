USE asset_management;

SET NAMES utf8mb4;

-- Run login_users.sql and select-user.sql before this file.

CREATE TABLE case_numbers (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    it_number VARCHAR(4) NOT NULL,
    asset_number VARCHAR(5) NULL,
    receiver_employee_id BIGINT UNSIGNED NOT NULL,
    created_by_user_id BIGINT UNSIGNED NOT NULL,
    delivery_sheet_path VARCHAR(500) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    CONSTRAINT uq_case_numbers_it_number
        UNIQUE (it_number),

    CONSTRAINT uq_case_numbers_asset_number
        UNIQUE (asset_number),

    INDEX idx_case_numbers_receiver (receiver_employee_id),
    INDEX idx_case_numbers_creator (created_by_user_id),

    CONSTRAINT fk_case_numbers_receiver
        FOREIGN KEY (receiver_employee_id)
        REFERENCES employees (id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_case_numbers_creator
        FOREIGN KEY (created_by_user_id)
        REFERENCES users (id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT chk_case_numbers_it_number
        CHECK (it_number REGEXP '^[0-9]{1,4}$'),

    CONSTRAINT chk_case_numbers_asset_number
        CHECK (
            asset_number IS NULL
            OR asset_number REGEXP '^[0-9]{1,5}$'
        )
) ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;


CREATE TABLE case_cpus (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    it_number VARCHAR(4) NOT NULL,
    brand VARCHAR(30) NOT NULL,
    generation TINYINT UNSIGNED NOT NULL,
    model VARCHAR(100) NOT NULL,
    speed_ghz DECIMAL(3,1) NOT NULL,
    warranty_file_path VARCHAR(500) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    CONSTRAINT uq_case_cpus_it_number
        UNIQUE (it_number),

    CONSTRAINT fk_case_cpus_it_number
        FOREIGN KEY (it_number)
        REFERENCES case_numbers (it_number)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT chk_case_cpus_generation
        CHECK (generation BETWEEN 1 AND 50),

    CONSTRAINT chk_case_cpus_speed
        CHECK (speed_ghz > 0)
) ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;


CREATE TABLE case_motherboards (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    it_number VARCHAR(4) NOT NULL,
    brand VARCHAR(50) NOT NULL,
    model VARCHAR(100) NOT NULL,
    warranty_file_path VARCHAR(500) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    CONSTRAINT uq_case_motherboards_it_number
        UNIQUE (it_number),

    CONSTRAINT fk_case_motherboards_it_number
        FOREIGN KEY (it_number)
        REFERENCES case_numbers (it_number)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;


CREATE TABLE case_gpus (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    it_number VARCHAR(4) NOT NULL,
    gpu_type ENUM('onboard', 'internal') NOT NULL,
    brand VARCHAR(50) NULL,
    model VARCHAR(100) NULL,
    memory_gb TINYINT UNSIGNED NULL,
    warranty_file_path VARCHAR(500) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    CONSTRAINT uq_case_gpus_it_number
        UNIQUE (it_number),

    CONSTRAINT fk_case_gpus_it_number
        FOREIGN KEY (it_number)
        REFERENCES case_numbers (it_number)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT chk_case_gpus_fields
        CHECK (
            (
                gpu_type = 'onboard'
                AND brand IS NULL
                AND model IS NULL
                AND memory_gb IS NULL
                AND warranty_file_path IS NULL
            )
            OR
            (
                gpu_type = 'internal'
                AND brand IS NOT NULL
                AND model IS NOT NULL
                AND memory_gb > 0
            )
        )
) ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;


CREATE TABLE case_rams (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    it_number VARCHAR(4) NOT NULL,
    ram_count TINYINT UNSIGNED NOT NULL,
    brand VARCHAR(50) NOT NULL,
    model VARCHAR(100) NOT NULL,
    ram_type ENUM('DDR4', 'DDR5') NOT NULL,
    module_capacity_gb TINYINT UNSIGNED NOT NULL,
    speed_mhz INT UNSIGNED NOT NULL,
    warranty_file_path VARCHAR(500) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    CONSTRAINT uq_case_rams_configuration
        UNIQUE (
            it_number,
            brand,
            model,
            ram_type,
            module_capacity_gb,
            speed_mhz
        ),

    CONSTRAINT fk_case_rams_it_number
        FOREIGN KEY (it_number)
        REFERENCES case_numbers (it_number)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT chk_case_rams_count
        CHECK (ram_count BETWEEN 1 AND 4),

    CONSTRAINT chk_case_rams_capacity
        CHECK (module_capacity_gb > 0),

    CONSTRAINT chk_case_rams_speed
        CHECK (speed_mhz > 0)
) ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;


CREATE TABLE case_storage_groups (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    it_number VARCHAR(4) NOT NULL,
    storage_count TINYINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    CONSTRAINT uq_case_storage_groups_it_number
        UNIQUE (it_number),

    CONSTRAINT fk_case_storage_groups_it_number
        FOREIGN KEY (it_number)
        REFERENCES case_numbers (it_number)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT chk_case_storage_groups_count
        CHECK (storage_count BETWEEN 1 AND 3)
) ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;


CREATE TABLE case_storage_devices (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    it_number VARCHAR(4) NOT NULL,
    device_number TINYINT UNSIGNED NOT NULL,
    storage_type ENUM('NVMe', 'M.2 SATA', 'SATA') NOT NULL,
    brand VARCHAR(50) NOT NULL,
    model VARCHAR(100) NOT NULL,
    capacity_gb SMALLINT UNSIGNED NOT NULL,
    warranty_file_path VARCHAR(500) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    CONSTRAINT uq_case_storage_devices_number
        UNIQUE (it_number, device_number),

    CONSTRAINT fk_case_storage_devices_it_number
        FOREIGN KEY (it_number)
        REFERENCES case_numbers (it_number)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT chk_case_storage_devices_number
        CHECK (device_number BETWEEN 1 AND 3),

    CONSTRAINT chk_case_storage_devices_capacity
        CHECK (capacity_gb > 0)
) ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;


CREATE TABLE case_writers (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    it_number VARCHAR(4) NOT NULL,
    writer_enabled TINYINT UNSIGNED NOT NULL DEFAULT 0,
    writer_type ENUM('CD Writer', 'DVD Writer') NULL,
    brand VARCHAR(50) NULL,
    model VARCHAR(100) NULL,
    warranty_file_path VARCHAR(500) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    CONSTRAINT uq_case_writers_it_number
        UNIQUE (it_number),

    CONSTRAINT fk_case_writers_it_number
        FOREIGN KEY (it_number)
        REFERENCES case_numbers (it_number)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT chk_case_writers_enabled
        CHECK (writer_enabled IN (0, 1)),

    CONSTRAINT chk_case_writers_fields
        CHECK (
            (
                writer_enabled = 0
                AND writer_type IS NULL
                AND brand IS NULL
                AND model IS NULL
                AND warranty_file_path IS NULL
            )
            OR
            (
                writer_enabled = 1
                AND writer_type IS NOT NULL
                AND brand IS NOT NULL
                AND model IS NOT NULL
            )
        )
) ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;


CREATE TABLE case_power_supplies (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    it_number VARCHAR(4) NOT NULL,
    brand VARCHAR(50) NOT NULL,
    model VARCHAR(100) NOT NULL,
    wattage_w SMALLINT UNSIGNED NOT NULL,
    warranty_file_path VARCHAR(500) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    CONSTRAINT uq_case_power_supplies_it_number
        UNIQUE (it_number),

    CONSTRAINT fk_case_power_supplies_it_number
        FOREIGN KEY (it_number)
        REFERENCES case_numbers (it_number)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT chk_case_power_supplies_wattage
        CHECK (wattage_w > 0)
) ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;


CREATE TABLE case_chassis (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    it_number VARCHAR(4) NOT NULL,
    brand VARCHAR(50) NOT NULL,
    model VARCHAR(100) NOT NULL,
    warranty_file_path VARCHAR(500) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    CONSTRAINT uq_case_chassis_it_number
        UNIQUE (it_number),

    CONSTRAINT fk_case_chassis_it_number
        FOREIGN KEY (it_number)
        REFERENCES case_numbers (it_number)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;


CREATE TABLE case_statuses (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    it_number VARCHAR(4) NOT NULL,
    case_type ENUM('new', 'old') NOT NULL,
    case_status ENUM('in_use', 'unused', 'retired') NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    CONSTRAINT uq_case_statuses_it_number
        UNIQUE (it_number),

    CONSTRAINT fk_case_statuses_it_number
        FOREIGN KEY (it_number)
        REFERENCES case_numbers (it_number)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT chk_case_statuses_value
        CHECK (
            (
                case_type = 'new'
                AND case_status IN ('in_use', 'unused')
            )
            OR
            (
                case_type = 'old'
                AND case_status IN ('in_use', 'retired')
            )
        )
) ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
