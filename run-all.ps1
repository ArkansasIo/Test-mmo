$ErrorActionPreference = 'Stop'

Set-Location $PSScriptRoot

Write-Host 'Starting local database...'
.\run-local-db.ps1

Write-Host 'Initializing database schema...'
.\setup-database.ps1

Write-Host 'Starting web server and game tick...'
.\run-dev.ps1

Write-Host ''
Write-Host 'Running post-start health check...'
.\health-check.ps1
