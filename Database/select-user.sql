USE asset_management;

CREATE TABLE IF NOT EXISTS deputies (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(150) NOT NULL,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    CONSTRAINT uq_deputies_name UNIQUE (name)
) ENGINE = InnoDB
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS departments (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    deputy_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    CONSTRAINT uq_departments_deputy_name
        UNIQUE (deputy_id, name),

    CONSTRAINT uq_departments_id_deputy
        UNIQUE (id, deputy_id),

    CONSTRAINT fk_departments_deputy
        FOREIGN KEY (deputy_id)
        REFERENCES deputies (id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE = InnoDB
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS employees (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(150) NOT NULL,

    national_id CHAR(10) NOT NULL,
    mobile CHAR(11) NOT NULL,

    deputy_id BIGINT UNSIGNED NOT NULL,
    department_id BIGINT UNSIGNED NOT NULL,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    CONSTRAINT uq_employees_national_id
        UNIQUE (national_id),

    INDEX idx_employees_mobile (mobile),
    INDEX idx_employees_deputy_id (deputy_id),

    INDEX idx_employees_department_deputy (
        department_id,
        deputy_id
    ),

    CONSTRAINT fk_employees_deputy
        FOREIGN KEY (deputy_id)
        REFERENCES deputies (id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_employees_department_deputy
        FOREIGN KEY (
            department_id,
            deputy_id
        )
        REFERENCES departments (
            id,
            deputy_id
        )
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE = InnoDB
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;


INSERT INTO deputies (name)
VALUES
    ('معاونت ۱'),
    ('معاونت ۲'),
    ('معاونت ۳'),
    ('معاونت ۴')
ON DUPLICATE KEY UPDATE
    name = VALUES(name);


INSERT INTO departments (deputy_id, name)
SELECT id, 'اداره ۱'
FROM deputies
WHERE name = 'معاونت ۱'
ON DUPLICATE KEY UPDATE
    name = VALUES(name);

INSERT INTO departments (deputy_id, name)
SELECT id, 'اداره ۲'
FROM deputies
WHERE name = 'معاونت ۲'
ON DUPLICATE KEY UPDATE
    name = VALUES(name);

INSERT INTO departments (deputy_id, name)
SELECT id, 'اداره ۳'
FROM deputies
WHERE name = 'معاونت ۳'
ON DUPLICATE KEY UPDATE
    name = VALUES(name);

INSERT INTO departments (deputy_id, name)
SELECT id, 'اداره ۴'
FROM deputies
WHERE name = 'معاونت ۴'
ON DUPLICATE KEY UPDATE
    name = VALUES(name);
