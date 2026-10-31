# Test descriptions are in English. Only the isolated test database is queried.
assert_equals "The catalog contains exactly five books" \
    "SELECT COUNT(*) FROM books" "5"
assert_equals "The existing catalog entry is preserved" \
    "SELECT COUNT(*) FROM books WHERE id=1 AND BINARY code='LIB-000' AND BINARY title='Il sentiero dei nidi di ragno' AND BINARY author='Italo Calvino' AND price=11.00 AND stock=3 AND published_on='1947-10-01'" "1"
assert_equals "The single-row insert stores LIB-001 with default stock and its publication date" \
    "SELECT COUNT(*) FROM books WHERE BINARY code='LIB-001' AND BINARY title='Il barone rampante' AND BINARY author='Italo Calvino' AND price=12.50 AND stock=0 AND published_on='1957-06-15'" "1"
assert_equals "LIB-002 preserves the apostrophe and an unknown publication date" \
    "SELECT COUNT(*) FROM books WHERE BINARY code='LIB-002' AND BINARY title='L''isola di Arturo' AND BINARY author='Elsa Morante' AND price=14.00 AND stock=5 AND published_on IS NULL" "1"
assert_equals "LIB-003 stores Unicode text with default stock and a NULL date" \
    "SELECT COUNT(*) FROM books WHERE BINARY code='LIB-003' AND BINARY title='Storie 📚' AND BINARY author='Niccolò Rossi' AND price=9.90 AND stock=0 AND published_on IS NULL" "1"
assert_equals "LIB-004 keeps an explicit zero stock and its publication date" \
    "SELECT COUNT(*) FROM books WHERE BINARY code='LIB-004' AND BINARY title='Le città invisibili' AND BINARY author='Italo Calvino' AND price=13.25 AND stock=0 AND published_on='1972-11-03'" "1"
assert_equals "All new books have distinct positive identifiers different from the seed identifier" \
    "SELECT COUNT(DISTINCT id) FROM books WHERE code IN ('LIB-001','LIB-002','LIB-003','LIB-004') AND id>1" "4"
