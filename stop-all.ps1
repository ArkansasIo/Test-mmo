$ErrorActionPreference = 'SilentlyContinue'

Set-Location $PSScriptRoot

.\stop-dev.ps1
.\stop-local-db.ps1

Write-Host 'All local services stopped.'
