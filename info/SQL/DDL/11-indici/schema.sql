CREATE TABLE orders(id INT PRIMARY KEY,customer_id INT NOT NULL,created_at DATETIME NOT NULL,reference VARCHAR(30) NOT NULL,status VARCHAR(20) NOT NULL);
CREATE INDEX idx_status ON orders(status);
INSERT INTO orders VALUES(1,10,'2026-10-01 09:00:00','ORD-001','paid'),(2,10,'2026-10-02 09:00:00','ORD-002','paid');
