#!/bin/bash
set -e
set -o pipefail

readonly DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

php "$DIR/../vendor/bin/phpunit" -c "$DIR/../src/test/resources/phpunit.xml" "$@"
