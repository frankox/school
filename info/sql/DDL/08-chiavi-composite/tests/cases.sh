assert_equals "seats exposes the required columns and nullability" "SELECT GROUP_CONCAT(CONCAT(COLUMN_NAME,':',COLUMN_TYPE,':',IS_NULLABLE) ORDER BY COLUMN_NAME SEPARATOR ';') FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='seats'" "position_in_row:int:NO;room_code:char(3):NO;row_label:char(1):NO;seat_number:int:NO"
# Test descriptions are in English. SQL assertions use the isolated test database.
assert_equals "seats has the required primary key" "SELECT GROUP_CONCAT(COLUMN_NAME ORDER BY ORDINAL_POSITION) FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='seats' AND CONSTRAINT_NAME='PRIMARY'" "room_code,seat_number"
assert_equals "seats has an index on room_code,row_label,position_in_row" "SELECT COUNT(*) FROM (SELECT INDEX_NAME FROM information_schema.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='seats' AND NON_UNIQUE=0 GROUP BY INDEX_NAME HAVING GROUP_CONCAT(COLUMN_NAME ORDER BY SEQ_IN_INDEX)='room_code,row_label,position_in_row') AS matching_indexes" "1"
assert_accepts "Seat numbers can repeat in different rooms" "INSERT INTO seats VALUES('A01',1,'A',1),('B01',1,'A',1),('A01',2,'A',2)"
assert_rejects "A room cannot repeat a seat number" "1062" "INSERT INTO seats VALUES('A01',1,'B',1)"
assert_rejects "A physical position cannot be reused" "1062" "INSERT INTO seats VALUES('A01',3,'A',1)"
assert_rejects "A seat number must be positive" "3819" "INSERT INTO seats VALUES('A01',0,'B',1)"
assert_rejects "A row position must be positive" "3819" "INSERT INTO seats VALUES('A01',3,'B',0)"
