#!/usr/bin/env bash
# Each run recreates only the isolated test database.
set -uo pipefail
: "${EXERCISE_DB:?EXERCISE_DB must be set}"
if [[ ! "$EXERCISE_DB" =~ ^dml_[0-9][0-9]$ ]]; then
    echo 'Invalid exercise database name' >&2
    exit 2
fi
TEST_DB="${EXERCISE_DB}_test"
passed=0
failed=0
mysql_server() {
    mysql --protocol=TCP --host="${MYSQL_HOST:-db}" --user=root \
        --default-character-set=utf8mb4 --batch --skip-column-names \
        --init-command="SET SESSION sql_mode='STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'" "$@"
}
query() { mysql_server "$TEST_DB" -e "$1"; }
assert_equals() {
    local description="$1" sql="$2" expected="$3" actual
    if actual=$(query "$sql" 2>/tmp/dml-error); then
        if [[ "$actual" == "$expected" ]]; then
            printf 'PASS %s\n' "$description"
            passed=$((passed+1))
            return
        fi
        printf 'FAIL %s\n  expected: %s; actual: %s\n' "$description" "$expected" "$actual"
    else
        printf 'FAIL %s\n' "$description"
        cat /tmp/dml-error
    fi
    failed=$((failed+1))
}
mysql_server -e "DROP DATABASE IF EXISTS \`$TEST_DB\`; CREATE DATABASE \`$TEST_DB\` CHARACTER SET utf8mb4;" || exit 1
mysql_server "$TEST_DB" < /work/schema.sql || exit 1
mysql_server "$TEST_DB" < "${SOLUTION_FILE:-/work/solution.sql}" || exit 1
source /work/tests/cases.sh
printf '\n%s passed; %s failed\n' "$passed" "$failed"
[[ "$failed" -eq 0 ]]
