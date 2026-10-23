# Test descriptions are in English. SQL assertions use the isolated test database.
assert_equals "The database uses utf8mb4" "SELECT DEFAULT_CHARACTER_SET_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME=DATABASE()" "utf8mb4"
assert_equals "Books has exactly three columns" "SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='books'" "3"
assert_equals "books.id has the required type" "SELECT COLUMN_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='books' AND COLUMN_NAME='id'" "int"
assert_equals "books.title has the required type" "SELECT COLUMN_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='books' AND COLUMN_NAME='title'" "varchar(150)"
assert_equals "books.author has the required type" "SELECT COLUMN_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='books' AND COLUMN_NAME='author'" "varchar(100)"
assert_accepts "A book can be stored" "INSERT INTO books VALUES (1,'Il barone rampante','Italo Calvino')"
