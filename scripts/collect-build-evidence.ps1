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

$environmentFile = Join-Path $evidence 'environment.txt'
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
) | Set-Content -Path $environmentFile -Encoding UTF8

& composer show --locked --no-ansi | Set-Content -Path (Join-Path $evidence 'composer-packages.txt') -Encoding UTF8
& php -m | Set-Content -Path (Join-Path $evidence 'php-modules.txt') -Encoding UTF8
& php artisan about --only=environment --only=drivers | Set-Content -Path (Join-Path $evidence 'laravel-about.txt') -Encoding UTF8

$distPath = Join-Path $ProjectRoot 'dist'
if (-not (Test-Path $distPath)) {
    throw "dist directory was not found. Run php artisan native:build first."
}

Get-ChildItem -Path $distPath -Recurse -File |
    Select-Object FullName, Length, LastWriteTime |
    Format-Table -AutoSize |
    Out-String -Width 4096 |
    Set-Content -Path (Join-Path $evidence 'dist-files.txt') -Encoding UTF8

Get-ChildItem -Path $distPath -Recurse -File |
    Get-FileHash -Algorithm SHA256 |
    Select-Object Path, Hash |
    Format-Table -AutoSize |
    Out-String -Width 4096 |
    Set-Content -Path (Join-Path $evidence 'dist-hashes.txt') -Encoding UTF8

$bundledPhpCandidates = @(
    (Join-Path $ProjectRoot 'vendor\nativephp\electron\resources\js\resources\php\php.exe'),
    (Join-Path $ProjectRoot 'dist\win-unpacked\resources\app.asar.unpacked\resources\php\php.exe')
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

$bundledOutput | Set-Content -Path (Join-Path $evidence 'bundled-php.txt') -Encoding UTF8

Write-Host "Evidence collected: $evidence"
