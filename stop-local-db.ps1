$ErrorActionPreference = 'SilentlyContinue'

$procs = Get-Process mariadbd,mysqld
if ($procs) {
    $procs | Stop-Process -Force
    Write-Host 'Stopped MariaDB local process(es).'
} else {
    Write-Host 'No MariaDB process found.'
}
