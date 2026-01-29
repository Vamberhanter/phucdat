# Script de sync code tu Project sang WordPress Local
# Chay script nay sau moi lan sua code

$sourcePath = "c:\Projects\Website\wordpress-theme"
$destPath = "C:\Users\Admin\Local Sites\test\app\public\wp-content\themes\test-theme"

Write-Host "Dang sync code tu Project sang WordPress Local..." -ForegroundColor Yellow
Write-Host "Nguon: $sourcePath" -ForegroundColor Cyan
Write-Host "Dich: $destPath" -ForegroundColor Cyan

# Kiem tra thu muc nguon
if (-not (Test-Path $sourcePath)) {
    Write-Host "LOI: Khong tim thay thu muc nguon: $sourcePath" -ForegroundColor Red
    exit 1
}

# Kiem tra thu muc dich
if (-not (Test-Path $destPath)) {
    Write-Host "LOI: Khong tim thay thu muc dich: $destPath" -ForegroundColor Red
    Write-Host "Vui long kiem tra duong dan WordPress Local cua ban!" -ForegroundColor Yellow
    exit 1
}

# Copy files (giu nguyen cau truc)
Write-Host "`nDang copy files..." -ForegroundColor Green
Copy-Item -Path "$sourcePath\*" -Destination $destPath -Recurse -Force

Write-Host "`nHoan thanh! Code da duoc sync sang WordPress Local." -ForegroundColor Green
Write-Host "Refresh trang WordPress de xem thay doi!" -ForegroundColor Yellow
