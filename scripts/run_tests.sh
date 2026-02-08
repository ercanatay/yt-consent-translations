#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

echo "[1/4] Running PHP syntax checks..."
find . -name '*.php' -not -path './.git/*' -print0 | xargs -0 -n1 php -l > /tmp/ytct-php-lint.log
cat /tmp/ytct-php-lint.log

echo "[2/4] Running lightweight PHP tests..."
php tests/run.php

echo "[3/4] Validating JSON language files..."
python3 scripts/validate_json.py

echo "[4/4] Running quality/security scan..."
python3 scripts/scan_code.py

echo "All checks passed."
