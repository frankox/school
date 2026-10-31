CREATE DATABASE IF NOT EXISTS school_lab
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE school_lab;

DROP TABLE IF EXISTS students;
DROP TABLE IF EXISTS classes;

CREATE TABLE classes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(20) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE students (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    age TINYINT UNSIGNED NOT NULL,
    class_id INT UNSIGNED NOT NULL,
    CONSTRAINT students_class_fk
        FOREIGN KEY (class_id) REFERENCES classes (id)
) ENGINE=InnoDB;

INSERT INTO classes (id, name) VALUES (1, '5A INF'), (2, '5B INF');
INSERT INTO students (first_name, last_name, age, class_id) VALUES
    ('Mario', 'Rossi', 18, 1),
    ('Anna', 'Verdi', 19, 1),
    ('Luca', 'Bianchi', 18, 2);
