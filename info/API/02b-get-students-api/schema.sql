CREATE DATABASE IF NOT EXISTS school_ex02_get
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE school_ex02_get;

CREATE TABLE IF NOT EXISTS students (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    age TINYINT UNSIGNED NOT NULL,
    student_code VARCHAR(30) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- La GET espone i codici nel campo studentCode di StudentDto.
INSERT INTO students (first_name, last_name, age, student_code) VALUES
    ('Giulia', 'Bianchi', 16, 'ST-001'),
    ('Marco', 'Rossi', 17, 'ST-002'),
    ('Sara', 'Verdi', 18, 'ST-003'),
    ('Luca', 'Neri', 18, 'ST-004'),
    ('Amina', 'Conti', 19, 'ST-005'),
    ('Paolo', 'Gallo', 21, 'ST-006');
