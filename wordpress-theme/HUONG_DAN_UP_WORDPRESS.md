# Hướng dẫn Upload Theme WordPress lên Server

## Phương pháp 1: Upload qua WordPress Admin (Dễ nhất)

### Bước 1: Chuẩn bị file
1. Nén toàn bộ thư mục `wordpress-theme` thành file ZIP
   - Windows: Right-click vào thư mục `wordpress-theme` > Send to > Compressed (zipped) folder
   - Hoặc dùng WinRAR/7-Zip để nén

### Bước 2: Upload qua WordPress Admin
1. Đăng nhập vào WordPress Admin (`yourdomain.com/wp-admin`)
2. Vào **Appearance > Themes**
3. Click **Add New**
4. Click **Upload Theme**
5. Click **Choose File** và chọn file ZIP vừa nén
6. Click **Install Now**
7. Sau khi cài đặt xong, click **Activate** để kích hoạt theme

### Bước 3: Thiết lập sau khi kích hoạt
1. Vào **Settings > Permalinks** và click **Save Changes** (quan trọng!)
2. Vào **Appearance > Customize > Site Identity** để upload Logo
3. Vào **Appearance > Menus** để tạo menu

---

## Phương pháp 2: Upload qua FTP/SFTP (Nâng cao)

### Bước 1: Kết nối FTP
1. Dùng FTP client (FileZilla, WinSCP, Cyberduck...)
2. Kết nối với thông tin:
   - Host: `ftp.yourdomain.com` hoặc IP server
   - Username: Tài khoản FTP
   - Password: Mật khẩu FTP
   - Port: 21 (FTP) hoặc 22 (SFTP)

### Bước 2: Upload theme
1. Navigate đến thư mục: `/wp-content/themes/`
2. Upload toàn bộ thư mục `wordpress-theme` vào đây
3. Đổi tên thư mục thành tên bạn muốn (ví dụ: `dnttvn-theme`)

### Bước 3: Kích hoạt theme
1. Đăng nhập WordPress Admin
2. Vào **Appearance > Themes**
3. Tìm theme "Cộng đồng Doanh nhân Trí tuệ Việt Nam"
4. Click **Activate**

---

## Phương pháp 3: Upload qua cPanel File Manager

### Bước 1: Đăng nhập cPanel
1. Truy cập `yourdomain.com/cpanel`
2. Đăng nhập với thông tin hosting

### Bước 2: Upload theme
1. Mở **File Manager**
2. Navigate đến: `public_html/wp-content/themes/`
3. Click **Upload** (hoặc drag & drop)
4. Upload file ZIP của theme
5. Right-click file ZIP > **Extract**
6. Xóa file ZIP sau khi giải nén

### Bước 3: Kích hoạt theme
1. Đăng nhập WordPress Admin
2. Vào **Appearance > Themes**
3. Click **Activate** cho theme mới

---

## Phương pháp 4: Upload qua SSH (Command Line)

### Bước 1: Kết nối SSH
```bash
ssh username@yourdomain.com
```

### Bước 2: Upload và giải nén
```bash
# Di chuyển đến thư mục themes
cd /path/to/wordpress/wp-content/themes/

# Upload file ZIP (dùng SCP từ máy local)
# scp wordpress-theme.zip username@yourdomain.com:/path/to/wordpress/wp-content/themes/

# Giải nén
unzip wordpress-theme.zip

# Đổi tên nếu cần
mv wordpress-theme dnttvn-theme

# Set permissions
chmod -R 755 dnttvn-theme
chown -R www-data:www-data dnttvn-theme
```

---

## Sau khi Upload - Thiết lập bắt buộc

### 1. Flush Rewrite Rules (QUAN TRỌNG!)
```
Vào Settings > Permalinks > Click "Save Changes"
```
Điều này giúp WordPress nhận diện Custom Post Types và Taxonomies.

### 2. Tạo Trang "Danh sách Doanh nghiệp"
1. Vào **Pages > Add New**
2. Tiêu đề: "Danh sách Doanh nghiệp"
3. Template: Chọn "Trang Doanh nghiệp" (nếu có)
4. Slug: `page-doanh-nghiep`
5. **Publish**

### 3. Thiết lập Menu
1. Vào **Appearance > Menus**
2. Tạo menu mới hoặc chỉnh sửa menu hiện có
3. Thêm các link:
   - Trang chủ
   - Trang Doanh nghiệp
   - Archive Tin tức
   - Archive Doanh nghiệp
4. Chọn vị trí: **Menu chính**
5. **Save Menu**

### 4. Upload Logo
1. Vào **Appearance > Customize > Site Identity**
2. Upload logo
3. **Publish**

### 5. Kiểm tra Permissions (nếu dùng FTP)
```bash
# Theme folder
chmod 755 wp-content/themes/your-theme-name

# Files trong theme
find wp-content/themes/your-theme-name -type f -exec chmod 644 {} \;

# Folders trong theme
find wp-content/themes/your-theme-name -type d -exec chmod 755 {} \;
```

---

## Cấu trúc thư mục sau khi upload

```
wp-content/
  themes/
    wordpress-theme/  (hoặc tên bạn đặt)
      ├── style.css
      ├── functions.php
      ├── header.php
      ├── footer.php
      ├── index.php
      ├── page-doanh-nghiep.php
      ├── single-tin-tuc.php
      ├── single-doanh-nghiep.php
      ├── assets/
      │   ├── style-gioi-thieu.css
      │   ├── script.js
      │   └── admin-script.js
      ├── README.md
      ├── INSTALL.md
      └── ADMIN_FEATURES.md
```

---

## Troubleshooting (Xử lý lỗi)

### Lỗi: Theme không hiển thị
- ✅ Kiểm tra file `style.css` có đúng format header
- ✅ Kiểm tra permissions (755 cho folders, 644 cho files)
- ✅ Clear cache nếu dùng caching plugin

### Lỗi: Custom Post Types không hiển thị
- ✅ Vào **Settings > Permalinks** > Click **Save Changes**
- ✅ Kiểm tra `functions.php` có đúng code register post type

### Lỗi: CSS/JS không load
- ✅ Kiểm tra đường dẫn trong `functions.php`
- ✅ Kiểm tra file có tồn tại trong thư mục `assets/`
- ✅ Clear browser cache

### Lỗi: Media Uploader không hoạt động
- ✅ Kiểm tra file `admin-script.js` có trong `assets/`
- ✅ Kiểm tra `wp_enqueue_media()` được gọi trong `functions.php`
- ✅ Kiểm tra jQuery đã được load

### Lỗi: 404 cho Custom Post Types
- ✅ Vào **Settings > Permalinks** > Click **Save Changes**
- ✅ Kiểm tra `.htaccess` có quyền ghi
- ✅ Kiểm tra permalink structure (không dùng Plain)

---

## Checklist sau khi upload

- [ ] Theme đã được upload vào `/wp-content/themes/`
- [ ] Theme đã được kích hoạt
- [ ] Đã flush rewrite rules (Settings > Permalinks > Save)
- [ ] Đã tạo trang "Danh sách Doanh nghiệp"
- [ ] Đã thiết lập menu
- [ ] Đã upload logo
- [ ] Đã test thêm Tin tức mới
- [ ] Đã test thêm Doanh nghiệp mới
- [ ] Đã test search và filter
- [ ] Đã test responsive trên mobile

---

## Lưu ý quan trọng

1. **Backup trước khi upload**: Luôn backup database và files trước khi thay đổi theme
2. **Permissions**: Đảm bảo permissions đúng (755 cho folders, 644 cho files)
3. **PHP Version**: Cần PHP 7.4 trở lên
4. **WordPress Version**: Cần WordPress 5.0 trở lên
5. **Flush Rewrite Rules**: Luôn làm sau khi kích hoạt theme

---

## Hỗ trợ

Nếu gặp vấn đề, kiểm tra:
1. WordPress Debug: Thêm vào `wp-config.php`:
   ```php
   define('WP_DEBUG', true);
   define('WP_DEBUG_LOG', true);
   ```
2. Xem log: `/wp-content/debug.log`
3. Kiểm tra PHP errors trong hosting control panel
