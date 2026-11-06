CREATE DATABASE IF NOT EXISTS hotel_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE hotel_db;

-- 1. Этажи гостиницы
CREATE TABLE floors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    floor_number TINYINT NOT NULL UNIQUE,
    CONSTRAINT chk_floor_number
        CHECK (floor_number BETWEEN 1 AND 5)
) ENGINE=InnoDB;

-- 2. Категории номеров
CREATE TABLE room_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    capacity TINYINT NOT NULL,
    CONSTRAINT chk_capacity CHECK (capacity > 0)
) ENGINE=InnoDB;

-- 3. Статусы номеров
CREATE TABLE room_statuses (
    code VARCHAR(30) PRIMARY KEY,
    name VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

-- 4. Статусы бронирований
CREATE TABLE booking_statuses (
    code VARCHAR(30) PRIMARY KEY,
    name VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

-- 5. История стоимости категорий
CREATE TABLE category_prices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    valid_from DATE NOT NULL,
    valid_to DATE DEFAULT NULL,

    CONSTRAINT fk_prices_category
        FOREIGN KEY (category_id)
        REFERENCES room_categories(id)
        ON DELETE RESTRICT,

    CONSTRAINT chk_price CHECK (price > 0),

    CONSTRAINT chk_price_dates
        CHECK (valid_to IS NULL OR valid_to > valid_from),

    INDEX idx_prices_category_dates
        (category_id, valid_from, valid_to)
) ENGINE=InnoDB;

-- 6. Номерной фонд
CREATE TABLE rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_number VARCHAR(10) NOT NULL UNIQUE,
    floor_id INT NOT NULL,
    category_id INT NOT NULL,
    status_code VARCHAR(30) NOT NULL,
    description TEXT,

    CONSTRAINT fk_rooms_floor
        FOREIGN KEY (floor_id)
        REFERENCES floors(id)
        ON DELETE RESTRICT,

    CONSTRAINT fk_rooms_category
        FOREIGN KEY (category_id)
        REFERENCES room_categories(id)
        ON DELETE RESTRICT,

    CONSTRAINT fk_rooms_status
        FOREIGN KEY (status_code)
        REFERENCES room_statuses(code)
        ON DELETE RESTRICT,

    INDEX idx_rooms_floor (floor_id),
    INDEX idx_rooms_category (category_id),
    INDEX idx_rooms_status (status_code)
) ENGINE=InnoDB;

-- 7. Сотрудники
CREATE TABLE employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    position VARCHAR(100) NOT NULL,
    phone VARCHAR(30),
    email VARCHAR(150),
    hire_date DATE,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,

    UNIQUE KEY uq_employee_email (email),
    INDEX idx_employee_active (is_active)
) ENGINE=InnoDB;

-- 8. Учётные записи сотрудников
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL UNIQUE,
    username VARCHAR(60) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM(
        'administrator',
        'manager',
        'housekeeping'
    ) NOT NULL DEFAULT 'housekeeping',
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_users_employee
        FOREIGN KEY (employee_id)
        REFERENCES employees(id)
        ON DELETE RESTRICT,

    INDEX idx_users_active (is_active)
) ENGINE=InnoDB;

-- 9. Гости
CREATE TABLE guests (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    email VARCHAR(150),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_guests_name (full_name),
    INDEX idx_guests_phone (phone)
) ENGINE=InnoDB;

-- 10. Бронирования
CREATE TABLE bookings (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    booking_number VARCHAR(30) NOT NULL UNIQUE,

    guest_id BIGINT NOT NULL,
    room_id INT DEFAULT NULL,
    category_id INT NOT NULL,

    check_in DATE NOT NULL,
    check_out DATE NOT NULL,

    price_per_night DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,

    status_code VARCHAR(30) NOT NULL,

    created_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_bookings_guest
        FOREIGN KEY (guest_id)
        REFERENCES guests(id)
        ON DELETE RESTRICT,

    CONSTRAINT fk_bookings_room
        FOREIGN KEY (room_id)
        REFERENCES rooms(id)
        ON DELETE RESTRICT,

    CONSTRAINT fk_bookings_category
        FOREIGN KEY (category_id)
        REFERENCES room_categories(id)
        ON DELETE RESTRICT,

    CONSTRAINT fk_bookings_status
        FOREIGN KEY (status_code)
        REFERENCES booking_statuses(code)
        ON DELETE RESTRICT,

    CONSTRAINT fk_bookings_user
        FOREIGN KEY (created_by)
        REFERENCES users(id)
        ON DELETE SET NULL,

    CONSTRAINT chk_booking_dates
        CHECK (check_out > check_in),

    CONSTRAINT chk_booking_prices
        CHECK (
            price_per_night > 0
            AND total_price >= 0
        ),

    INDEX idx_bookings_guest (guest_id),
    INDEX idx_bookings_room (room_id),
    INDEX idx_bookings_dates (check_in, check_out),
    INDEX idx_bookings_status (status_code)
) ENGINE=InnoDB;

-- 11. Журнал действий
CREATE TABLE audit_logs (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(100),
    entity_id BIGINT,
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_audit_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE SET NULL,

    INDEX idx_audit_user (user_id),
    INDEX idx_audit_created (created_at),
    INDEX idx_audit_entity (entity_type, entity_id)
) ENGINE=InnoDB;
