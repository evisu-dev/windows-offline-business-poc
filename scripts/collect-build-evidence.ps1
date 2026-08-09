[CmdletBinding()]
param(
    [Parameter(Mandatory = $false)]
    [string]$ProjectRoot = (Get-Location).Path,

    [Parameter(Mandatory = $false)]
    [ValidatePattern('^[A-Za-z0-9._-]+$')]
    [string]$Label = 'current'
)

$ErrorActionPreference = 'Stop'
Set-StrictMode -Version Latest

Set-Location $ProjectRoot
$evidence = Join-Path $ProjectRoot 'evidence'
New-Item -ItemType Directory -Path $evidence -Force | Out-Null

$prefix = "windows-$Label"

# 個人・端末識別情報（PC名、ユーザープロファイル絶対パス）は証跡に含めない。
@(
    "collected_at_jst=$([DateTimeOffset]::Now.ToString('o'))"
    "os=$((Get-CimInstance Win32_OperatingSystem).Caption)"
    "os_version=$((Get-CimInstance Win32_OperatingSystem).Version)"
    "architecture=$env:PROCESSOR_ARCHITECTURE"
    "php=$((& php -r 'echo PHP_VERSION;').Trim())"
    "node=$((& node --version).Trim())"
    "npm=$((& npm --version).Trim())"
    "composer=$((& composer --version --no-ansi).Trim())"
    "git=$((& git --version).Trim())"
    "laravel=$((& php artisan --version --no-ansi).Trim())"
) | Set-Content -Path (Join-Path $evidence "$prefix-environment.txt") -Encoding UTF8

& composer show --locked --no-ansi |
    Set-Content -Path (Join-Path $evidence "$prefix-composer-packages.txt") -Encoding UTF8

& php -m |
    Set-Content -Path (Join-Path $evidence "$prefix-php-modules.txt") -Encoding UTF8

& php artisan about --only=environment --only=drivers |
    Set-Content -Path (Join-Path $evidence "$prefix-laravel-about.txt") -Encoding UTF8

$distPath = Join-Path $ProjectRoot 'nativephp\electron\dist'
if (-not (Test-Path $distPath)) {
    throw "dist directory was not found. Run 'php artisan native:build win' first."
}

# 公開用証跡ではdist配下を再帰走査しない。
# installer / blockmap / top-level metadataのみを対象にし、巨大な全ファイル一覧を作らない。
$artifacts = Get-ChildItem -Path $distPath -File |
    Where-Object { $_.Extension -in @('.exe', '.blockmap', '.yml', '.yaml') }

$artifactSummary = foreach ($file in $artifacts) {
    $hash = (Get-FileHash -Path $file.FullName -Algorithm SHA256).Hash
    [PSCustomObject]@{
        File = $file.Name
        SizeBytes = $file.Length
        SHA256 = $hash
    }
}

$artifactSummary |
    Format-Table -AutoSize |
    Out-String -Width 4096 |
    Set-Content -Path (Join-Path $evidence "$prefix-artifacts.txt") -Encoding UTF8

Write-Host "Evidence collected: $evidence"
