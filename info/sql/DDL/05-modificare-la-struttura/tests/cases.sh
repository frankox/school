assert_equals "catalog_products exposes the required columns and nullability" "SELECT GROUP_CONCAT(CONCAT(COLUMN_NAME,':',COLUMN_TYPE,':',IS_NULLABLE) ORDER BY COLUMN_NAME SEPARATOR ';') FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='catalog_products'" "id:int:NO;name:varchar(120):NO;price:decimal(8,2):NO;stock:int:NO"
# Test descriptions are in English. SQL assertions use the isolated test database.
assert_equals "catalog_products.name has the required type" "SELECT COLUMN_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='catalog_products' AND COLUMN_NAME='name'" "varchar(120)"
assert_equals "catalog_products.price has the required type" "SELECT COLUMN_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='catalog_products' AND COLUMN_NAME='price'" "decimal(8,2)"
assert_equals "Original products and prices are preserved" "SELECT GROUP_CONCAT(CONCAT(id,':',name,':',price) ORDER BY id) FROM catalog_products" "1:Chitarra:129.90,2:Piano:799.00"
assert_equals "Existing products receive zero stock" "SELECT SUM(stock) FROM catalog_products" "0"
assert_equals "Old tables are removed" "SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME IN ('products','import_buffer')" "0"
assert_equals "The obsolete column is removed" "SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='catalog_products' AND COLUMN_NAME='legacy_code'" "0"
assert_rejects "Negative stock is rejected" "3819" "UPDATE catalog_products SET stock=-1 WHERE id=1"
