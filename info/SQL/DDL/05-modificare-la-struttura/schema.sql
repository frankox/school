CREATE TABLE products(id INT PRIMARY KEY,name VARCHAR(40) NOT NULL,cost DECIMAL(8,2) NOT NULL,legacy_code VARCHAR(20));
INSERT INTO products VALUES(1,'Chitarra',129.90,'old-1'),(2,'Piano',799.00,'old-2');
CREATE TABLE import_buffer(raw_line TEXT);
