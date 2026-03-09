$ErrorActionPreference = 'Stop'

$phpBin = 'C:\Users\Shadow\php\php.exe'
$projectRoot = (Get-Location).Path
$indexDir = Join-Path $projectRoot 'Index'

$valid = 0
$invalid = 0

Write-Host ''
Write-Host 'Validating PHP Syntax' -ForegroundColor Cyan
Write-Host '===========================================' -ForegroundColor Cyan

$phpFiles = Get-ChildItem -Path $indexDir -Filter '*.php' -Recurse
Write-Host ("Checking {0} PHP files" -f $phpFiles.Count) -ForegroundColor Cyan

foreach ($file in $phpFiles) {
    $shortPath = $file.FullName.Replace($projectRoot, '.')
    $null = & $phpBin -l $file.FullName 2>&1

    if ($LASTEXITCODE -eq 0) {
        Write-Host ("OK {0}" -f $shortPath) -ForegroundColor Green
        $valid = $valid + 1
    }
    else {
        Write-Host ("FAIL {0}" -f $shortPath) -ForegroundColor Red
        $invalid = $invalid + 1
    }
}

Write-Host ''
Write-Host '==========================================' -ForegroundColor Cyan
Write-Host ("Valid: {0}  Invalid: {1}" -f $valid, $invalid) -ForegroundColor Cyan
Write-Host '==========================================' -ForegroundColor Cyan
Write-Host ''

if ($invalid -gt 0) {
    exit 1
}

exit 0
