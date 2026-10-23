assert_equals "appointments exposes the required columns and nullability" "SELECT GROUP_CONCAT(CONCAT(COLUMN_NAME,':',COLUMN_TYPE,':',IS_NULLABLE) ORDER BY COLUMN_NAME SEPARATOR ';') FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='appointments'" "doctor_id:int:NO;id:int:NO;patient_name:varchar(100):NO;starts_at:datetime:NO"
# Test descriptions are in English. SQL assertions use the isolated test database.
assert_equals "appointments has the required primary key" "SELECT GROUP_CONCAT(COLUMN_NAME ORDER BY ORDINAL_POSITION) FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='appointments' AND CONSTRAINT_NAME='PRIMARY'" "id"
assert_accepts "A doctor can have several appointments" "INSERT INTO appointments VALUES(1,1,'2026-11-01 09:00:00','Anna'),(2,1,'2026-11-01 10:00:00','Luca')"
assert_rejects "An unknown doctor is rejected" "1452" "INSERT INTO appointments VALUES(3,99,'2026-11-01 11:00:00','Sara')"
assert_rejects "A missing doctor is rejected" "1048" "INSERT INTO appointments VALUES(3,NULL,'2026-11-01 11:00:00','Sara')"
assert_rejects "An existing appointment cannot become orphaned" "1452" "UPDATE appointments SET doctor_id=99 WHERE id=1"
assert_equals "A doctor can have no appointments" "SELECT COUNT(*) FROM doctors WHERE id=2" "1"
