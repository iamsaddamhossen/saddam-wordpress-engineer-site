#!/usr/bin/env bash
set -euo pipefail

DB_NAME=${1-wordpress_test}
DB_USER=${2-root}
DB_PASS=${3-root}
DB_HOST=${4-localhost}
WP_VERSION=${5-7.0.1}
WP_TESTS_DIR=${WP_TESTS_DIR-/tmp/wordpress-tests-lib}
WP_CORE_DIR=${WP_CORE_DIR-/tmp/wordpress}

if [[ ! -d "$WP_CORE_DIR" ]]; then
  mkdir -p "$WP_CORE_DIR"
  curl -sSL "https://wordpress.org/wordpress-${WP_VERSION}.tar.gz" | tar --strip-components=1 -xz -C "$WP_CORE_DIR"
fi

if [[ ! -d "$WP_TESTS_DIR" ]]; then
  mkdir -p "$WP_TESTS_DIR"
  svn export --quiet "https://develop.svn.wordpress.org/tags/${WP_VERSION}/tests/phpunit/includes/" "$WP_TESTS_DIR/includes"
  svn export --quiet "https://develop.svn.wordpress.org/tags/${WP_VERSION}/tests/phpunit/data/" "$WP_TESTS_DIR/data"
fi

curl -sSL "https://develop.svn.wordpress.org/tags/${WP_VERSION}/wp-tests-config-sample.php" > "$WP_TESTS_DIR/wp-tests-config.php"
sed -i "s/youremptytestdbnamehere/${DB_NAME}/" "$WP_TESTS_DIR/wp-tests-config.php"
sed -i "s/yourusernamehere/${DB_USER}/" "$WP_TESTS_DIR/wp-tests-config.php"
sed -i "s/yourpasswordhere/${DB_PASS}/" "$WP_TESTS_DIR/wp-tests-config.php"
sed -i "s|localhost|${DB_HOST}|" "$WP_TESTS_DIR/wp-tests-config.php"
sed -i "s|dirname( __FILE__ ) . '/src/'|'${WP_CORE_DIR}/'|" "$WP_TESTS_DIR/wp-tests-config.php"

mysqladmin create "$DB_NAME" --user="$DB_USER" --password="$DB_PASS" --host="${DB_HOST%%:*}" --force >/dev/null 2>&1 || true
printf 'WordPress test suite installed at %s\n' "$WP_TESTS_DIR"
