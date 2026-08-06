#!/usr/bin/env bash
set -euo pipefail

PROJECT_ROOT="${1:-$HOME/Development/windows-offline-business-poc}"
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

"$SCRIPT_DIR/preflight-macos.sh"

set_env_value() {
  local path="$1"
  local key="$2"
  local value="$3"

  php -r '
    [$script, $path, $key, $value] = $argv;
    $content = file_get_contents($path);
    if ($content === false) {
        fwrite(STDERR, "Unable to read {$path}\n");
        exit(1);
    }
    $pattern = "/^" . preg_quote($key, "/") . "=.*$/m";
    $line = $key . "=" . $value;
    if (preg_match($pattern, $content) === 1) {
        $content = preg_replace($pattern, $line, $content, 1);
    } else {
        $content = rtrim($content) . PHP_EOL . $line . PHP_EOL;
    }
    if (file_put_contents($path, $content) === false) {
        fwrite(STDERR, "Unable to write {$path}\n");
        exit(1);
    }
  ' "$path" "$key" "$value"
}

if [[ -e "$PROJECT_ROOT" ]]; then
  if [[ ! -d "$PROJECT_ROOT" ]]; then
    echo "Project path exists and is not a directory: $PROJECT_ROOT" >&2
    exit 1
  fi
  if [[ -n "$(find "$PROJECT_ROOT" -mindepth 1 -maxdepth 1 -print -quit)" ]]; then
    echo "Project directory must not exist or must be empty: $PROJECT_ROOT" >&2
    exit 1
  fi
  rmdir "$PROJECT_ROOT"
fi

mkdir -p "$(dirname "$PROJECT_ROOT")"

echo
echo 'Laravel 12 projectを作成します。'
composer create-project laravel/laravel "$PROJECT_ROOT" '^12.0' --no-interaction
cd "$PROJECT_ROOT"

composer require nativephp/desktop:2.2.1 --no-interaction
php artisan native:install --no-interaction

ENV_PATH="$PROJECT_ROOT/.env"
set_env_value "$ENV_PATH" APP_NAME '"Offline Work Order Manager PoC"'
set_env_value "$ENV_PATH" APP_ENV local
set_env_value "$ENV_PATH" APP_DEBUG true
set_env_value "$ENV_PATH" DB_CONNECTION sqlite
set_env_value "$ENV_PATH" NATIVEPHP_APP_VERSION 0.1.0
set_env_value "$ENV_PATH" NATIVEPHP_APP_ID jp.evisuworks.offlineworkorderpoc
set_env_value "$ENV_PATH" NATIVEPHP_APP_AUTHOR '"Evisu Works"'
set_env_value "$ENV_PATH" NATIVEPHP_APP_DESCRIPTION '"Offline Windows business application PoC"'
set_env_value "$ENV_PATH" NATIVEPHP_UPDATER_ENABLED false

touch "$PROJECT_ROOT/database/database.sqlite"

MIGRATION_TIMESTAMP="$(date '+%Y_%m_%d_%H%M%S')"
cat > "$PROJECT_ROOT/database/migrations/${MIGRATION_TIMESTAMP}_create_poc_checks_table.php" <<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('poc_checks', function (Blueprint $table): void {
            $table->id();
            $table->string('message');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('poc_checks');
    }
};
PHP

cat > "$PROJECT_ROOT/routes/web.php" <<'PHP'
<?php

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

Route::get('/', function (): View {
    return view('poc', [
        'count' => DB::table('poc_checks')->count(),
        'databasePath' => DB::connection()->getDatabaseName(),
    ]);
});

Route::post('/write-test', function (): RedirectResponse {
    DB::table('poc_checks')->insert([
        'message' => 'NativePHP SQLite write test',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return redirect('/')->with('status', 'SQLiteへの書き込みに成功しました。');
});
PHP

cat > "$PROJECT_ROOT/resources/views/poc.blade.php" <<'BLADE'
<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Offline Work Order Manager PoC</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Yu Gothic UI", "Meiryo", sans-serif; margin: 0; background: #f4f5f7; color: #1f2937; }
        main { max-width: 760px; margin: 48px auto; padding: 32px; background: #fff; border: 1px solid #d1d5db; border-radius: 12px; }
        h1 { margin-top: 0; font-size: 28px; }
        .status { padding: 12px; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; }
        dl { display: grid; grid-template-columns: 180px 1fr; gap: 12px; }
        dt { font-weight: 700; }
        dd { margin: 0; overflow-wrap: anywhere; }
        button { padding: 10px 16px; font: inherit; cursor: pointer; }
    </style>
</head>
<body>
<main>
    <h1>Offline Work Order Manager PoC</h1>
    <p>Windowsインストール型・オフライン業務アプリの成立確認画面です。</p>

    @if (session('status'))
        <p class="status">{{ session('status') }}</p>
    @endif

    <dl>
        <dt>SQLite書き込み件数</dt>
        <dd>{{ $count }}</dd>
        <dt>データベース</dt>
        <dd>{{ $databasePath }}</dd>
        <dt>アプリ版</dt>
        <dd>{{ config('nativephp.version') }}</dd>
    </dl>

    <form method="post" action="/write-test">
        @csrf
        <button type="submit">SQLiteへテストデータを書き込む</button>
    </form>
</main>
</body>
</html>
BLADE

cat > "$PROJECT_ROOT/tests/Feature/PocCheckTest.php" <<'PHP'
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PocCheckTest extends TestCase
{
    use RefreshDatabase;

    public function test_poc_screen_is_available(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Offline Work Order Manager PoC');
    }

    public function test_sqlite_write_route_adds_a_record(): void
    {
        $this->post('/write-test')->assertRedirect('/');

        $this->assertDatabaseCount('poc_checks', 1);
    }
}
PHP

php artisan optimize:clear
php artisan migrate --force
php artisan route:list --path=write-test
php artisan test

mkdir -p "$PROJECT_ROOT/evidence"
"$SCRIPT_DIR/collect-macos-evidence.sh" "$PROJECT_ROOT"

if [[ ! -d "$PROJECT_ROOT/.git" ]]; then
  git init -b main "$PROJECT_ROOT" >/dev/null
fi

echo
echo 'Mac側フェーズ1プロジェクトの生成が完了しました。'
echo "Project: $PROJECT_ROOT"
echo
echo '次の順序で確認してください。'
echo "  cd \"$PROJECT_ROOT\""
echo '  php artisan serve'
echo '  # ブラウザ確認後に停止し、次を実行'
echo '  php artisan native:run'
echo
echo '初回のnative:runが成功した後、migration変更時は次を実行します。'
echo '  php artisan native:migrate'
echo
echo 'WindowsビルドはMacでは実行せず、composer.lockを含めてWindowsへ引き渡します。'
