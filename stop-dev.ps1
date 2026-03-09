$ErrorActionPreference = 'SilentlyContinue'

$jobNames = @('ScifiPhpServer', 'ScifiGameTick')
foreach ($name in $jobNames) {
    $job = Get-Job -Name $name
    if ($job) {
        Stop-Job -Job $job -Force
        Remove-Job -Job $job -Force
        Write-Host "Stopped and removed job: $name"
    } else {
        Write-Host "Job not found: $name"
    }
}
