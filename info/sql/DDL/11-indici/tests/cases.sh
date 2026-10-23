# Test descriptions are in English. SQL assertions use the isolated test database.
assert_equals "orders has the required primary key" "SELECT GROUP_CONCAT(COLUMN_NAME ORDER BY ORDINAL_POSITION) FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='orders' AND CONSTRAINT_NAME='PRIMARY'" "id"
assert_equals "orders has an index on customer_id,created_at" "SELECT COUNT(*) FROM (SELECT INDEX_NAME FROM information_schema.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='orders'  GROUP BY INDEX_NAME HAVING GROUP_CONCAT(COLUMN_NAME ORDER BY SEQ_IN_INDEX)='customer_id,created_at') AS matching_indexes" "1"
assert_equals "orders has an index on reference" "SELECT COUNT(*) FROM (SELECT INDEX_NAME FROM information_schema.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='orders' AND NON_UNIQUE=0 GROUP BY INDEX_NAME HAVING GROUP_CONCAT(COLUMN_NAME ORDER BY SEQ_IN_INDEX)='reference') AS matching_indexes" "1"
assert_equals "The low selectivity index is removed" "SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='orders' AND INDEX_NAME='idx_status'" "0"
assert_equals "Original orders are retained" "SELECT COUNT(*) FROM orders" "2"
assert_accepts "Customer and timestamp can repeat" "INSERT INTO orders VALUES(3,10,'2026-10-01 09:00:00','ORD-003','paid')"
assert_rejects "A duplicate public reference is rejected" "1062" "INSERT INTO orders VALUES(4,20,'2026-10-03 09:00:00','ORD-001','paid')"
