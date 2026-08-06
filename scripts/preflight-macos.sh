#!/usr/bin/env bash
set -euo pipefail

required_commands=(php composer node npm git sw_vers xcode-select)
failed=0

for command_name in "${required_commands[@]}"; do
  if ! command -v "$command_name" >/dev/null 2>&1; then
    printf 'NG  %-12s not found\n' "$command_name"
    failed=1
  else
    printf 'OK  %-12s %s\n' "$command_name" "$(command -v "$command_name")"
  fi
done

if [[ "$failed" -ne 0 ]]; then
  echo
  echo '不足コマンドがあります。MAC_SETUP.mdの環境導入手順を確認してください。'
  exit 1
fi

macos_version="$(sw_vers -productVersion)"
macos_major="${macos_version%%.*}"
php_version="$(php -r 'echo PHP_VERSION;')"
node_version="$(node --version)"
composer_version="$(composer --version --no-ansi 2>/dev/null | head -n 1)"
architecture="$(uname -m)"

echo
echo '--- versions ---'
echo "macOS:      $macos_version"
echo "Arch:       $architecture"
echo "PHP:        $php_version"
echo "Node.js:    $node_version"
echo "Composer:   $composer_version"
echo "Git:        $(git --version)"
echo "Xcode CLT:  $(xcode-select -p)"

if (( macos_major < 12 )); then
  echo "NG: macOS 12以上が必要です。Actual: $macos_version" >&2
  failed=1
fi

if [[ "$php_version" != 8.4.* ]]; then
  echo "NG: このPoCではPHP 8.4.xに固定します。Actual: $php_version" >&2
  failed=1
fi

if [[ "$node_version" != v22.* ]]; then
  echo "NG: このPoCではNode.js 22.xに固定します。Actual: $node_version" >&2
  failed=1
fi

for extension_name in zip pdo_sqlite sqlite3 mbstring openssl; do
  if php -r "exit(extension_loaded('$extension_name') ? 0 : 1);"; then
    printf 'OK  PHP ext      %s\n' "$extension_name"
  else
    printf 'NG  PHP ext      %s\n' "$extension_name" >&2
    failed=1
  fi
done

if [[ "$failed" -ne 0 ]]; then
  echo
  echo 'プリフライト不合格です。バージョンまたは拡張を修正してから生成へ進んでください。' >&2
  exit 1
fi

echo
echo 'プリフライト合格。bootstrap-phase1-macos.shを実行できます。'
