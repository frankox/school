CREATE TABLE books (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    title VARCHAR(150) NOT NULL,
    author VARCHAR(100) NOT NULL,
    price DECIMAL(8,2) NOT NULL CHECK (price > 0),
    stock INT NOT NULL DEFAULT 0 CHECK (stock >= 0),
    published_on DATE NULL DEFAULT NULL
) ENGINE=InnoDB;

-- Existing catalog entry: students must preserve it.
INSERT INTO books (code, title, author, price, stock, published_on)
VALUES ('LIB-000', 'Il sentiero dei nidi di ragno', 'Italo Calvino', 11.00, 3, '1947-10-01');
