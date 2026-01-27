# HƯỚNG DẪN SYNC CODE TỪ PROJECT SANG WORDPRESS LOCAL

## ⚠️ QUAN TRỌNG

**Khi sửa code trong Cursor (`c:\Projects\Website\wordpress-theme`), WordPress Local KHÔNG tự động cập nhật!**

Bạn cần **copy/sync code thủ công** hoặc dùng script tự động.

---

## 🔄 CÁCH 1: DÙNG SCRIPT TỰ ĐỘNG (KHUYẾN NGHỊ)

### Bước 1: Chỉnh đường dẫn trong script

1. Mở file `sync-to-local.ps1`
2. Sửa đường dẫn `$destPath` cho đúng với WordPress Local của bạn:
   ```powershell
   $destPath = "C:\Users\Admin\Local Sites\test\app\public\wp-content\themes\test-theme"
   ```
   (Thay `test` và `test-theme` bằng tên site và theme của bạn)

### Bước 2: Chạy script

**Cách 1: Click đúp file**
- Click đúp vào `sync-to-local.ps1`
- Nếu bị chặn, click phải → "Run with PowerShell"

**Cách 2: Chạy từ PowerShell**
```powershell
cd c:\Projects\Website
.\sync-to-local.ps1
```

### Bước 3: Refresh WordPress

- Mở trình duyệt: `http://test.local/page-doanh-nghiep/`
- Nhấn **F5** hoặc **Ctrl + F5** để refresh

---

## 📋 CÁCH 2: COPY THỦ CÔNG

### Mỗi lần sửa code:

1. Mở 2 cửa sổ File Explorer:
   - **Cửa sổ 1**: `c:\Projects\Website\wordpress-theme`
   - **Cửa sổ 2**: `C:\Users\Admin\Local Sites\test\app\public\wp-content\themes\test-theme`

2. Copy tất cả files từ cửa sổ 1 sang cửa sổ 2
   - Chọn tất cả (Ctrl + A)
   - Copy (Ctrl + C)
   - Paste vào cửa sổ 2 (Ctrl + V)
   - Chọn "Replace" khi được hỏi

3. Refresh trang WordPress

---

## 🔗 CÁCH 3: TẠO SYMLINK (TỰ ĐỘNG HOÀN TOÀN)

### Lưu ý: Cách này chỉ làm 1 lần, sau đó tự động sync!

### Bước 1: Xóa thư mục theme cũ trong Local

1. Vào: `C:\Users\Admin\Local Sites\test\app\public\wp-content\themes\`
2. **Xóa** hoặc **đổi tên** thư mục `test-theme` (ví dụ: `test-theme-backup`)

### Bước 2: Tạo Symlink

Mở PowerShell **với quyền Administrator** và chạy:

```powershell
cd "C:\Users\Admin\Local Sites\test\app\public\wp-content\themes"
New-Item -ItemType SymbolicLink -Path "test-theme" -Target "c:\Projects\Website\wordpress-theme"
```

### Bước 3: Kiểm tra

- Vào WordPress Admin → Appearance → Themes
- Theme "test-theme" vẫn hoạt động bình thường
- **Từ giờ, mọi thay đổi trong `c:\Projects\Website\wordpress-theme` sẽ TỰ ĐỘNG áp dụng!**

---

## ✅ KHUYẾN NGHỊ

- **Lần đầu setup**: Dùng **Cách 3 (Symlink)** - tự động hoàn toàn
- **Nếu không dùng được Symlink**: Dùng **Cách 1 (Script)** - nhanh và tiện
- **Nếu chỉ sửa 1-2 file**: Dùng **Cách 2 (Copy thủ công)**

---

## 🚨 LƯU Ý

1. **Luôn backup** trước khi sync (nếu có code quan trọng)
2. **Kiểm tra đường dẫn** cho đúng với Local site của bạn
3. **Refresh trình duyệt** sau mỗi lần sync để xem thay đổi

---

## 📝 TÓM TẮT

**Câu trả lời ngắn gọn:**
- ❌ **KHÔNG tự động** - phải sync thủ công
- ✅ **Dùng script** `sync-to-local.ps1` để tự động copy
- ✅ **Hoặc tạo Symlink** để tự động hoàn toàn

**Sau khi sync → Refresh WordPress → Xem thay đổi!**
