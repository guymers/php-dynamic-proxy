#!/bin/bash
set -e
set -o pipefail

readonly DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

php "$DIR/../vendor/bin/bugfree" lint "$DIR/../src/main/php"
php "$DIR/../vendor/bin/phpcs" -n --standard="$DIR/../src/main/resources/coding-standard/Custom/" --extensions=php "$DIR/../src"
