$ErrorActionPreference = 'Stop'

$projectRoot = $PSScriptRoot
$dbDataDir = Join-Path $projectRoot 'local-db\data'
$dbPort = 3306
$mariadbd = 'C:\Program Files\MariaDB 12.1\bin\mariadbd.exe'
$installer = 'C:\Program Files\MariaDB 12.1\bin\mariadb-install-db.exe'

if (-not (Test-Path $mariadbd)) {
    throw "MariaDB server binary not found at: $mariadbd"
}

if (-not (Test-Path $dbDataDir)) {
    New-Item -ItemType Directory -Path $dbDataDir -Force | Out-Null
}

# Initialize data directory if it does not contain mysql system tables.
$mysqlSystemDir = Join-Path $dbDataDir 'mysql'
if (-not (Test-Path $mysqlSystemDir)) {
    if (-not (Test-Path $installer)) {
        throw "MariaDB install-db tool not found at: $installer"
    }

    & $installer --datadir="$dbDataDir" --port=$dbPort
    if ($LASTEXITCODE -ne 0) {
        throw "Failed to initialize local MariaDB data directory"
    }
}

$running = Get-Process mariadbd -ErrorAction SilentlyContinue
if ($running) {
    Write-Host 'MariaDB is already running.'
    exit 0
}

Start-Process -FilePath $mariadbd -ArgumentList @(
    "--datadir=$dbDataDir",
    "--port=$dbPort",
    '--bind-address=127.0.0.1',
    '--console'
) -WindowStyle Hidden

Start-Sleep -Seconds 2
$listening = Get-NetTCPConnection -LocalPort $dbPort -State Listen -ErrorAction SilentlyContinue
if (-not $listening) {
    throw "MariaDB did not start on port $dbPort"
}

Write-Host "MariaDB running on 127.0.0.1:$dbPort"
