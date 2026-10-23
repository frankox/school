assert_equals "parcels exposes the required columns and nullability" "SELECT GROUP_CONCAT(CONCAT(COLUMN_NAME,':',COLUMN_TYPE,':',IS_NULLABLE) ORDER BY COLUMN_NAME SEPARATOR ';') FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='parcels'" "bin_code:char(3):NO;id:int:NO;label:varchar(100):NO;warehouse_id:int:NO"
# Test descriptions are in English. SQL assertions use the isolated test database.
assert_accepts "An existing location accepts parcels" "INSERT INTO parcels VALUES(1,1,'A01','Pacco libri'),(2,1,'A01','Pacco spartiti')"
assert_rejects "A mixed but nonexistent pair is rejected" "1452" "INSERT INTO parcels VALUES(3,1,'B01','Non valido')"
assert_rejects "A missing warehouse is rejected" "1048" "INSERT INTO parcels VALUES(3,NULL,'A01','Non valido')"
assert_rejects "A missing bin is rejected" "1048" "INSERT INTO parcels VALUES(3,1,NULL,'Non valido')"
assert_rejects "An occupied bin cannot be removed" "1451" "DELETE FROM bins WHERE warehouse_id=1 AND bin_code='A01'"
assert_rejects "An occupied bin key cannot change" "1451" "UPDATE bins SET bin_code='C01' WHERE warehouse_id=1 AND bin_code='A01'"
