$ErrorActionPreference = 'Stop'

$projectRoot = $PSScriptRoot
$phpExe = 'C:\Users\Shadow\php\php.exe'
$initScript = Join-Path $projectRoot 'Index\database\init.php'

if (-not (Test-Path $phpExe)) {
    throw "PHP executable not found at: $phpExe"
}

if (-not (Test-Path $initScript)) {
    throw "Database init script not found: $initScript"
}

Set-Location $projectRoot
& $phpExe $initScript
if ($LASTEXITCODE -ne 0) {
    throw "Database setup failed with exit code $LASTEXITCODE"
}

Write-Host ''
Write-Host 'Database setup completed successfully.'
