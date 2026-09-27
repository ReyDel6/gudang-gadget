param(
    [string]$OutputDir = (Join-Path $PSScriptRoot 'hasil'),
    [int]$Keep = 10
)

# Backup database Gudang Gadget (Aiven MySQL, wajib SSL).
# Jalankan: powershell -ExecutionPolicy Bypass -File backup\backup-aiven.ps1

$ErrorActionPreference = 'Stop'

$mysqldump = 'C:\Program Files\FlyEnv-Data\app\mysql-26.7.0\mysql-26.7.0-winx64\bin\mysqldump.exe'
$host_db   = 'mysql-39793ada-delphianor-2088.j.aivencloud.com'
$port      = 13348
$user      = 'avnadmin'
$pass = $env:GG_DB_PASS
if (-not $pass) { $pass = [Environment]::GetEnvironmentVariable('GG_DB_PASS', 'User') }
if (-not $pass) {
    $sec = Read-Host -Prompt 'Password DB (www)' -AsSecureString
    $pass = [System.Runtime.InteropServices.Marshal]::PtrToStringBSTR([System.Runtime.InteropServices.Marshal]::SecureStringToBSTR($sec))
    if (-not $pass) { throw 'Password DB kosong. Isi variabel lingkungan GG_DB_PASS agar tidak diminta tiap kali.' }
}
$db        = 'defaultdb'

if (-not (Test-Path $OutputDir)) { New-Item -ItemType Directory -Path $OutputDir | Out-Null }

$stamp = Get-Date -Format 'yyyyMMdd-HHmmss'
$base  = Join-Path $OutputDir "gudanggadget-$stamp"
$sql   = "$base.sql"
$gz    = "$base.sql.gz"

Write-Host "Dumping $db -> $sql ..."
& $mysqldump --ssl-mode=REQUIRED --single-transaction --skip-lock-tables `
    --host=$host_db --port=$port --user=$user --password=$pass $db | Out-File -FilePath $sql -Encoding utf8

if ($LASTEXITCODE -ne 0) {
    Remove-Item $sql -ErrorAction SilentlyContinue
    throw "mysqldump gagal (exit $LASTEXITCODE)."
}

# Kompres ke .gz lalu bersihkan file sql mentah.
$in  = [System.IO.File]::OpenRead($sql)
$out = [System.IO.File]::Create($gz)
$gzs = New-Object System.IO.Compression.GZipStream($out, [System.IO.Compression.CompressionMode]::Compress)
$in.CopyTo($gzs)
$gzs.Dispose(); $out.Dispose(); $in.Dispose()
Remove-Item $sql

$size = [math]::Round((Get-Item $gz).Length / 1KB, 1)
Write-Host "OK: $gz ($size KB)"

# Pertahankan hanya $Keep file terbaru.
Get-ChildItem -Path $OutputDir -Filter 'gudanggadget-*.sql.gz' | Sort-Object Name -Descending | Select-Object -Skip $Keep | ForEach-Object {
    Write-Host "Hapus lama: $($_.Name)"
    Remove-Item $_.FullName
}