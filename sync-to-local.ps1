# Script để sync code từ Project sang WordPress Local
# Chạy script này sau mỗi lần sửa code

$sourcePath = "c:\Projects\Website\wordpress-theme"
$destPath = "C:\Users\Admin\Local Sites\test\app\public\wp-content\themes\test-theme"

Write-Host "Đang sync code từ Project sang WordPress Local..." -ForegroundColor Yellow
Write-Host "Nguồn: $sourcePath" -ForegroundColor Cyan
Write-Host "Đích: $destPath" -ForegroundColor Cyan

# Kiểm tra thư mục nguồn
if (-not (Test-Path $sourcePath)) {
    Write-Host "LỖI: Không tìm thấy thư mục nguồn: $sourcePath" -ForegroundColor Red
    exit 1
}

# Kiểm tra thư mục đích
if (-not (Test-Path $destPath)) {
    Write-Host "LỖI: Không tìm thấy thư mục đích: $destPath" -ForegroundColor Red
    Write-Host "Vui lòng kiểm tra đường dẫn WordPress Local của bạn!" -ForegroundColor Yellow
    exit 1
}

# Copy files (giữ nguyên cấu trúc)
Write-Host "`nĐang copy files..." -ForegroundColor Green
Copy-Item -Path "$sourcePath\*" -Destination $destPath -Recurse -Force

Write-Host "`n✅ Hoàn thành! Code đã được sync sang WordPress Local." -ForegroundColor Green
Write-Host "Refresh trang WordPress để xem thay đổi!" -ForegroundColor Yellow
