#!/usr/bin/env bash
# Shared test runner. Only the exercise test database is recreated.
set -uo pipefail
: "${EXERCISE_DB:?EXERCISE_DB must be set}"
if [[ ! "$EXERCISE_DB" =~ ^ddl_[0-9][0-9]$ ]]; then
    echo 'Invalid exercise database name' >&2
    exit 2
fi
TEST_DB="${EXERCISE_DB}_test"
passed=0
failed=0
mysql_server() {
    mysql --protocol=TCP --host="${MYSQL_HOST:-db}" --user=root --batch --skip-column-names "$@"
}
query() {
    mysql_server --init-command="SET SESSION sql_mode='STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'" "$TEST_DB" -e "$1"
}
pass() { printf 'PASS %s\n' "$1"; passed=$((passed+1)); }
fail() { printf 'FAIL %s\n' "$1"; failed=$((failed+1)); }
assert_equals() {
    local description="$1" sql="$2" expected="$3" actual
    if actual=$(query "$sql" 2>/tmp/ddl-error); then
        if [[ "$actual" == "$expected" ]]; then
            pass "$description"
        else
            fail "$description"
            printf '  expected: %s; actual: %s\n' "$expected" "$actual"
        fi
    else
        fail "$description"
        cat /tmp/ddl-error
    fi
}
assert_accepts() {
    if query "$2" >/dev/null 2>/tmp/ddl-error; then
        pass "$1"
    else
        fail "$1"
        cat /tmp/ddl-error
    fi
}
assert_rejects() {
    local description="$1" code="$2" sql="$3"
    if query "$sql" >/dev/null 2>/tmp/ddl-error; then
        fail "$description"
        echo '  statement unexpectedly succeeded'
    elif grep -q "^ERROR ${code} " /tmp/ddl-error; then
        pass "$description"
    else
        fail "$description"
        printf '  expected MySQL error %s; received:\n' "$code"
        cat /tmp/ddl-error
    fi
}
# Exercise 01 must create its own database. Other exercises receive an empty one.
mysql_server -e "DROP DATABASE IF EXISTS \`$TEST_DB\`;" || exit 1
if [[ "$EXERCISE_DB" != ddl_01 ]]; then
    mysql_server -e "CREATE DATABASE \`$TEST_DB\` CHARACTER SET utf8mb4;" || exit 1
    if [[ -f /work/schema.sql ]]; then
        mysql_server "$TEST_DB" < /work/schema.sql || exit 1
    fi
fi
# The fixed name used in exercise 01 is redirected to the isolated test database.
sed "s/\\b${EXERCISE_DB}\\b/${TEST_DB}/g" "${SOLUTION_FILE:-/work/solution.sql}" > /tmp/ddl-solution.sql
if [[ "$EXERCISE_DB" == ddl_01 ]]; then
    mysql_server < /tmp/ddl-solution.sql || exit 1
else
    mysql_server "$TEST_DB" < /tmp/ddl-solution.sql || exit 1
fi
source /work/tests/cases.sh
printf '\n%s passed; %s failed\n' "$passed" "$failed"
[[ "$failed" -eq 0 ]]
