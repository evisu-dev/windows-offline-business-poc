[CmdletBinding()]
param(
    [Parameter(Mandatory = $false)]
    [string]$ProjectRoot = "C:\src\windows-offline-business-poc"
)

$ErrorActionPreference = 'Stop'
Set-StrictMode -Version Latest

function Assert-Command {
    param([Parameter(Mandatory = $true)][string]$Name)

    if (-not (Get-Command $Name -ErrorAction SilentlyContinue)) {
        throw "Required command was not found: $Name"
    }
}

function Add-Or-ReplaceEnvValue {
    param(
        [Parameter(Mandatory = $true)][string]$Path,
        [Parameter(Mandatory = $true)][string]$Key,
        [Parameter(Mandatory = $true)][string]$Value
    )

    $content = Get-Content -Path $Path -Raw
    $escapedKey = [regex]::Escape($Key)

    if ($content -match "(?m)^$escapedKey=") {
        $content = [regex]::Replace($content, "(?m)^$escapedKey=.*$", "$Key=$Value")
    } else {
        $content = $content.TrimEnd() + "`r`n$Key=$Value`r`n"
    }

    Set-Content -Path $Path -Value $content -Encoding UTF8
}

foreach ($command in @('php', 'composer', 'node', 'npm', 'git')) {
    Assert-Command -Name $command
}

$phpVersion = (& php -r "echo PHP_VERSION;").Trim()
$nodeVersion = (& node --version).Trim()
$composerVersion = (& composer --version --no-ansi).Trim()

if (-not $phpVersion.StartsWith('8.4.')) {
    throw "PHP 8.4.x is required for this PoC. Actual: $phpVersion"
}

if (-not $nodeVersion.StartsWith('v22.')) {
    throw "Node.js 22.x is required for this PoC. Actual: $nodeVersion"
}

Write-Host "PHP: $phpVersion"
Write-Host "Node.js: $nodeVersion"
Write-Host "Composer: $composerVersion"

foreach ($extension in @('zip', 'pdo_sqlite', 'sqlite3', 'mbstring', 'openssl')) {
    & php -r "exit(extension_loaded('$extension') ? 0 : 1);"
    if ($LASTEXITCODE -ne 0) {
        throw "Required PHP extension is not loaded: $extension"
    }
}

if (Test-Path $ProjectRoot) {
    if ((Get-ChildItem -Force $ProjectRoot | Measure-Object).Count -gt 0) {
        throw "ProjectRoot must not exist or must be empty: $ProjectRoot"
    }
} else {
    New-Item -ItemType Directory -Path $ProjectRoot -Force | Out-Null
}

$parent = Split-Path -Parent $ProjectRoot
$name = Split-Path -Leaf $ProjectRoot
New-Item -ItemType Directory -Path $parent -Force | Out-Null

# create-project requires the destination not to exist.
Remove-Item -Path $ProjectRoot -Force
Push-Location $parent
try {
    & composer create-project laravel/laravel $name "^12.0" --no-interaction
    if ($LASTEXITCODE -ne 0) { throw "Laravel project creation failed." }
} finally {
    Pop-Location
}

Set-Location $ProjectRoot

& composer require nativephp/desktop:2.2.1 --no-interaction
if ($LASTEXITCODE -ne 0) { throw "NativePHP installation failed." }

& php artisan native:install --no-interaction
if ($LASTEXITCODE -ne 0) { throw "native:install failed." }

$envPath = Join-Path $ProjectRoot '.env'
Add-Or-ReplaceEnvValue -Path $envPath -Key 'APP_NAME' -Value '"Offline Work Order Manager PoC"'
Add-Or-ReplaceEnvValue -Path $envPath -Key 'APP_ENV' -Value 'local'
Add-Or-ReplaceEnvValue -Path $envPath -Key 'APP_DEBUG' -Value 'true'
Add-Or-ReplaceEnvValue -Path $envPath -Key 'DB_CONNECTION' -Value 'sqlite'
Add-Or-ReplaceEnvValue -Path $envPath -Key 'NATIVEPHP_APP_VERSION' -Value '0.1.0'
Add-Or-ReplaceEnvValue -Path $envPath -Key 'NATIVEPHP_APP_ID' -Value 'jp.evisuworks.offlineworkorderpoc'
Add-Or-ReplaceEnvValue -Path $envPath -Key 'NATIVEPHP_APP_AUTHOR' -Value '"Evisu Works"'
Add-Or-ReplaceEnvValue -Path $envPath -Key 'NATIVEPHP_APP_DESCRIPTION' -Value '"Offline Windows business application PoC"'
Add-Or-ReplaceEnvValue -Path $envPath -Key 'NATIVEPHP_UPDATER_ENABLED' -Value 'false'

$migrationTimestamp = Get-Date -Format 'yyyy_MM_dd_HHmmss'
$migrationPath = Join-Path $ProjectRoot "database\migrations\${migrationTimestamp}_create_poc_checks_table.php"
@'
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
'@ | Set-Content -Path $migrationPath -Encoding UTF8

$routePath = Join-Path $ProjectRoot 'routes\web.php'
@'
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
'@ | Set-Content -Path $routePath -Encoding UTF8

$viewPath = Join-Path $ProjectRoot 'resources\views\poc.blade.php'
@'
<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Offline Work Order Manager PoC</title>
    <style>
        body { font-family: "Yu Gothic UI", "Meiryo", sans-serif; margin: 0; background: #f4f5f7; color: #1f2937; }
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
'@ | Set-Content -Path $viewPath -Encoding UTF8

& php artisan optimize:clear
& php artisan migrate --force
if ($LASTEXITCODE -ne 0) { throw "Laravel migration failed." }
& php artisan route:list --path=write-test
& php artisan test
if ($LASTEXITCODE -ne 0) { throw "Laravel tests failed." }

New-Item -ItemType Directory -Path (Join-Path $ProjectRoot 'evidence') -Force | Out-Null

Write-Host ''
Write-Host 'Phase 1 project was created.'
Write-Host "Project: $ProjectRoot"
Write-Host 'Next commands:'
Write-Host '  php artisan native:migrate'
Write-Host '  php artisan native:run'
Write-Host '  php artisan native:build'
