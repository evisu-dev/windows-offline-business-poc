# Windows環境セットアップスクリプト
# PowerShellで実行してください
$ErrorActionPreference = 'Stop'

Write-Host "=== Windows環境セットアップ ===" -ForegroundColor Cyan

# PATH設定（セッション内）
$env:Path = "C:\node22\node-v22.16.0-win-x64;C:\php84;" + $env:Path

# PHP動作確認
Write-Host "`n[1/8] PHP確認..." -ForegroundColor Yellow
C:\php84\php.exe -v
C:\php84\php.exe -m | Select-String -Pattern "pdo_sqlite|sqlite3|mbstring|openssl|zip|fileinfo"

# Node.js確認
Write-Host "`n[2/8] Node.js確認..." -ForegroundColor Yellow
& "C:\node22\node-v22.16.0-win-x64\node.exe" --version

# Composer確認
Write-Host "`n[3/8] Composer確認..." -ForegroundColor Yellow
C:\php84\php.exe C:\php84\composer.phar --version

# composer install
Write-Host "`n[4/8] composer install..." -ForegroundColor Yellow
Set-Location "C:\src\windows-offline-business-poc"
C:\php84\php.exe C:\php84\composer.phar install --no-interaction

# .env設定
Write-Host "`n[5/8] .env設定..." -ForegroundColor Yellow
if (-not (Test-Path ".env")) {
    Copy-Item ".env.example" ".env"
    C:\php84\php.exe artisan key:generate
}

# SQLite DB作成とマイグレーション
Write-Host "`n[6/8] データベースセットアップ..." -ForegroundColor Yellow
if (-not (Test-Path "database\database.sqlite")) {
    New-Item -ItemType File -Path "database\database.sqlite" -Force | Out-Null
}
C:\php84\php.exe artisan migrate --force

# npm install & build
Write-Host "`n[7/8] npm install & build..." -ForegroundColor Yellow
& "C:\node22\node-v22.16.0-win-x64\npm.cmd" install
& "C:\node22\node-v22.16.0-win-x64\node.exe" node_modules\vite\bin\vite.js build

# NativePHP install
Write-Host "`n[8/8] NativePHP install..." -ForegroundColor Yellow
C:\php84\php.exe artisan native:install --no-interaction

# テスト実行
Write-Host "`n--- テスト実行 ---" -ForegroundColor Yellow
C:\php84\php.exe artisan test

Write-Host "`n=== セットアップ完了 ===" -ForegroundColor Green
Write-Host ""
Write-Host "ツールの場所:" -ForegroundColor Cyan
Write-Host "  PHP 8.4:    C:\php84\php.exe"
Write-Host "  Composer:   C:\php84\php.exe C:\php84\composer.phar"
Write-Host "  Node.js 22: C:\node22\node-v22.16.0-win-x64\node.exe"
Write-Host "  npm:        C:\node22\node-v22.16.0-win-x64\npm.cmd"
Write-Host ""
Write-Host "PATH設定（毎回のセッションで必要）:" -ForegroundColor Cyan
Write-Host '  $env:Path = "C:\node22\node-v22.16.0-win-x64;C:\php84;" + $env:Path'
Write-Host ""
Write-Host "コマンド:" -ForegroundColor Cyan
Write-Host "  開発サーバー起動:  C:\php84\php.exe artisan serve"
Write-Host "  NativePHP起動:     C:\php84\php.exe artisan native:run"
Write-Host "  NativePHPビルド:   C:\php84\php.exe artisan native:build"
Write-Host "  テスト実行:        C:\php84\php.exe artisan test"
