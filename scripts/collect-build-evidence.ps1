[CmdletBinding()]
param(
    [Parameter(Mandatory = $false)]
    [string]$ProjectRoot = (Get-Location).Path
)

$ErrorActionPreference = 'Stop'
Set-StrictMode -Version Latest

Set-Location $ProjectRoot
$evidence = Join-Path $ProjectRoot 'evidence'
New-Item -ItemType Directory -Path $evidence -Force | Out-Null

# --- 環境情報 ---
$environmentFile = Join-Path $evidence 'windows-build-environment-phase1.txt'
@(
    "collected_at_jst=$([DateTimeOffset]::Now.ToString('o'))"
    "computer_name=$env:COMPUTERNAME"
    "os=$((Get-CimInstance Win32_OperatingSystem).Caption)"
    "os_version=$((Get-CimInstance Win32_OperatingSystem).Version)"
    "architecture=$env:PROCESSOR_ARCHITECTURE"
    "php=$((& php -r 'echo PHP_VERSION;').Trim())"
    "node=$((& node --version).Trim())"
    "npm=$((& npm --version).Trim())"
    "composer=$((& composer --version --no-ansi).Trim())"
    "git=$((& git --version).Trim())"
    "laravel=$((& php artisan --version --no-ansi).Trim())"
) | Set-Content -Path $environmentFile -Encoding UTF8

# --- Composer依存一覧 ---
& composer show --locked --no-ansi | Set-Content -Path (Join-Path $evidence 'windows-composer-packages-phase1.txt') -Encoding UTF8

# --- PHP拡張一覧 ---
& php -m | Set-Content -Path (Join-Path $evidence 'windows-php-modules-phase1.txt') -Encoding UTF8

# --- Laravel情報 ---
& php artisan about --only=environment --only=drivers | Set-Content -Path (Join-Path $evidence 'windows-laravel-about-phase1.txt') -Encoding UTF8

# --- ビルド成果物確認 ---
$distPath = Join-Path $ProjectRoot 'nativephp\electron\dist'
if (-not (Test-Path $distPath)) {
    throw "dist directory was not found at '$distPath'. Run 'php artisan native:build' first."
}

# 成果物ファイル一覧
Get-ChildItem -Path $distPath -Recurse -File |
    Select-Object FullName, Length, LastWriteTime |
    Format-Table -AutoSize |
    Out-String -Width 4096 |
    Set-Content -Path (Join-Path $evidence 'windows-build-artifacts-phase1.txt') -Encoding UTF8

# 成果物SHA-256ハッシュ
Get-ChildItem -Path $distPath -Recurse -File |
    Get-FileHash -Algorithm SHA256 |
    Select-Object Path, Hash |
    Format-Table -AutoSize |
    Out-String -Width 4096 |
    Set-Content -Path (Join-Path $evidence 'windows-build-hashes-phase1.txt') -Encoding UTF8

# 成果物合計サイズ
$totalSize = (Get-ChildItem -Path $distPath -Recurse -File | Measure-Object -Property Length -Sum).Sum
"total_size_bytes=$totalSize" | Add-Content -Path (Join-Path $evidence 'windows-build-artifacts-phase1.txt') -Encoding UTF8

# --- Bundled PHP確認 ---
$bundledPhpCandidates = @(
    (Join-Path $ProjectRoot 'nativephp\electron\dist\win-unpacked\resources\app.asar.unpacked\resources\php\php.exe'),
    (Join-Path $ProjectRoot 'vendor\nativephp\electron\resources\js\resources\php\php.exe')
)

$bundledOutput = foreach ($candidate in $bundledPhpCandidates) {
    if (Test-Path $candidate) {
        "path=$candidate"
        & $candidate -v
        ''
    }
}

if (-not $bundledOutput) {
    $bundledOutput = @('Bundled PHP executable was not found in the expected paths.')
}

$bundledOutput | Set-Content -Path (Join-Path $evidence 'windows-bundled-php-phase1.txt') -Encoding UTF8

Write-Host "Evidence collected: $evidence"
