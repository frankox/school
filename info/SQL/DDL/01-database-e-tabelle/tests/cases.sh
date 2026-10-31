# Test descriptions are in English. SQL assertions use the isolated test database.
assert_equals "Books has exactly three columns" "SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='books'" "3"
assert_type "books" "id" "integer"
assert_type "books" "title" "text"
assert_type "books" "author" "text"
assert_accepts "A book can be stored" "INSERT INTO books VALUES (1,'Il barone rampante','Italo Calvino')"
assert_accepts "Titles and authors accept Unicode characters" "INSERT INTO books VALUES (2,'Storie 本 📚','Niccolò')"
assert_equals "Unicode characters are preserved exactly" "SELECT HEX(CONVERT(title USING utf8mb4))=HEX('Storie 本 📚') AND HEX(CONVERT(author USING utf8mb4))=HEX('Niccolò') FROM books WHERE id=2" "1"
