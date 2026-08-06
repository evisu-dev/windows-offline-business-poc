#!/usr/bin/env bash
set -euo pipefail

PROJECT_ROOT="${1:-$(pwd)}"
EVIDENCE_DIR="$PROJECT_ROOT/evidence"
mkdir -p "$EVIDENCE_DIR"

{
  echo "collected_at_jst=$(TZ=Asia/Tokyo date '+%Y-%m-%dT%H:%M:%S%z')"
  echo "macos=$(sw_vers -productVersion)"
  echo "architecture=$(uname -m)"
  echo "php=$(php -r 'echo PHP_VERSION;')"
  echo "node=$(node --version)"
  echo "npm=$(npm --version)"
  echo "composer=$(composer --version --no-ansi 2>/dev/null | head -n 1)"
  echo "git=$(git --version)"
  echo "xcode_clt=$(xcode-select -p)"
} > "$EVIDENCE_DIR/macos-environment.txt"

php -m | sort > "$EVIDENCE_DIR/macos-php-modules.txt"
(
  cd "$PROJECT_ROOT"
  composer show --direct --no-ansi
) > "$EVIDENCE_DIR/macos-composer-packages.txt"

{
  cd "$PROJECT_ROOT"
  for lock_file in composer.lock package-lock.json nativephp/electron/package-lock.json; do
    if [[ -f "$lock_file" ]]; then
      shasum -a 256 "$lock_file"
    fi
  done
} > "$EVIDENCE_DIR/macos-lock-hashes.txt"

echo "Evidence written to: $EVIDENCE_DIR"
