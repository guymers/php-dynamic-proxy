#!/bin/bash
set -e
set -o pipefail

readonly DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

"$DIR/test.sh" --coverage-html coverage
