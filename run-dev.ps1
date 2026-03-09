$ErrorActionPreference = 'Stop'

$projectRoot = $PSScriptRoot
$phpExe = 'C:\Users\Shadow\php\php.exe'
$port = 8000
$hostName = 'localhost'
$tickIntervalSeconds = 60

if (-not (Test-Path $phpExe)) {
    throw "PHP executable not found at: $phpExe"
}

$logsDir = Join-Path $projectRoot 'logs'
if (-not (Test-Path $logsDir)) {
    New-Item -Path $logsDir -ItemType Directory | Out-Null
}

$serverJobName = 'ScifiPhpServer'
$tickJobName = 'ScifiGameTick'

$existingServer = Get-Job -Name $serverJobName -ErrorAction SilentlyContinue
if ($existingServer) {
    Write-Host "Server job already running: $serverJobName"
} else {
    Start-Job -Name $serverJobName -ScriptBlock {
        param($root, $php, $hostArg, $portArg)
        Set-Location $root
        & $php -S "${hostArg}:${portArg}" -t .
    } -ArgumentList $projectRoot, $phpExe, $hostName, $port | Out-Null
    Write-Host "Started server job: $serverJobName"
}

$existingTick = Get-Job -Name $tickJobName -ErrorAction SilentlyContinue
if ($existingTick) {
    Write-Host "Tick job already running: $tickJobName"
} else {
    Start-Job -Name $tickJobName -ScriptBlock {
        param($root, $php, $interval)
        Set-Location $root
        while ($true) {
            & $php 'Index/cron/game_tick.php' | Out-Null
            Start-Sleep -Seconds $interval
        }
    } -ArgumentList $projectRoot, $phpExe, $tickIntervalSeconds | Out-Null
    Write-Host "Started tick job: $tickJobName"
}

Start-Sleep -Seconds 1
Get-Job -Name $serverJobName, $tickJobName | Select-Object Name, State
Write-Host ""
Write-Host "Game URL: http://localhost:$port/Index/index.php"
Write-Host "Installer URL (redirects): http://localhost:$port/install.php"
