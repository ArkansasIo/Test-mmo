$ErrorActionPreference = 'Stop'

$dbPort = 3306
$appUrl = 'http://localhost:8000/Index/index.php'
$mariaCli = 'C:\Program Files\MariaDB 12.1\bin\mariadb.exe'

Write-Host 'Running local health checks...'
Write-Host ''

$dbProcess = Get-Process -Name 'mariadbd' -ErrorAction SilentlyContinue
if ($null -ne $dbProcess) {
    Write-Host '[OK] MariaDB process is running.'
}
else {
    Write-Host '[WARN] MariaDB process is not running.'
}

$dbListen = Get-NetTCPConnection -LocalPort $dbPort -State Listen -ErrorAction SilentlyContinue
if ($null -ne $dbListen) {
    Write-Host ("[OK] Port {0} is listening." -f $dbPort)
}
else {
    Write-Host ("[WARN] Port {0} is not listening." -f $dbPort)
}

if (Test-Path -Path $mariaCli) {
    $dbResult = & $mariaCli -h 127.0.0.1 -P $dbPort -u root --skip-ssl -e "SHOW DATABASES LIKE 'scifi_conquest';" 2>$null
    if ($LASTEXITCODE -eq 0 -and ($dbResult -match 'scifi_conquest')) {
        Write-Host '[OK] Database scifi_conquest is reachable.'
    }
    else {
        Write-Host '[WARN] Database connectivity check failed.'
    }
}
else {
    Write-Host '[WARN] MariaDB CLI not found; skipped DB query check.'
}

$serverJob = Get-Job -Name 'ScifiPhpServer' -ErrorAction SilentlyContinue
$tickJob = Get-Job -Name 'ScifiGameTick' -ErrorAction SilentlyContinue

if ($null -ne $serverJob -and $serverJob.State -eq 'Running') {
    Write-Host '[OK] ScifiPhpServer job is running.'
}
else {
    Write-Host '[WARN] ScifiPhpServer job is not running.'
}

if ($null -ne $tickJob -and $tickJob.State -eq 'Running') {
    Write-Host '[OK] ScifiGameTick job is running.'
}
else {
    Write-Host '[WARN] ScifiGameTick job is not running.'
}

try {
    $response = Invoke-WebRequest -Uri $appUrl -TimeoutSec 10
    if ($response.Content -match 'Sci-Fi Conquest: Awakening') {
        Write-Host '[OK] App endpoint is responding.'
    }
    else {
        Write-Host '[WARN] App endpoint responded but content check failed.'
    }
}
catch {
    Write-Host '[WARN] App endpoint is not reachable.'
}

Write-Host ''
Write-Host 'Health check completed.'
