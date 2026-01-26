# Hướng dẫn Upload Theme lên WordPress

## 📋 Tổng quan

Có 4 cách chính để upload theme lên WordPress:
1. **FTP/SFTP** (Khuyên dùng - phổ biến nhất)
2. **cPanel File Manager** (Dễ dàng, không cần phần mềm)
3. **WordPress Admin Upload** (Nếu host cho phép)
4. **SSH/Command Line** (Cho người dùng nâng cao)

---

## 🚀 PHƯƠNG PHÁP 1: Upload qua FTP/SFTP (Khuyên dùng)

### Bước 1: Chuẩn bị
- **Phần mềm FTP Client**: FileZilla (miễn phí), WinSCP, Cyberduck
- **Thông tin FTP** từ hosting:
  - FTP Host/Server
  - FTP Username
  - FTP Password
  - FTP Port (thường là 21 cho FTP, 22 cho SFTP)

### Bước 2: Kết nối FTP
1. Mở FileZilla (hoặc phần mềm FTP khác)
2. Nhập thông tin:
   - **Host**: ftp.yourdomain.com (hoặc IP server)
   - **Username**: Tên đăng nhập FTP
   - **Password**: Mật khẩu FTP
   - **Port**: 21 (FTP) hoặc 22 (SFTP)
3. Click **Quickconnect**

### Bước 3: Upload Theme
1. **Bên trái (Local)**: Điều hướng đến thư mục `c:\Projects\Website\wordpress-theme`
2. **Bên phải (Remote)**: Điều hướng đến `public_html/wp-content/themes/` (hoặc `www/wp-content/themes/`)
3. **Upload toàn bộ thư mục `wordpress-theme`**:
   - Chọn tất cả files và folders trong `wordpress-theme`
   - Kéo thả hoặc click phải > Upload
4. Đợi upload hoàn tất (có thể mất vài phút)

### Bước 4: Đổi tên thư mục (Tùy chọn)
- Trong FTP, đổi tên thư mục `wordpress-theme` thành tên bạn muốn (ví dụ: `dnttvn-theme`)

### Bước 5: Kích hoạt Theme
1. Đăng nhập WordPress Admin
2. Vào **Appearance > Themes**
3. Tìm theme "Cộng đồng Doanh nhân Trí tuệ Việt Nam"
4. Click **Activate**

---

## 🌐 PHƯƠNG PHÁP 2: Upload qua cPanel File Manager

### Bước 1: Đăng nhập cPanel
1. Truy cập: `https://yourdomain.com/cpanel` (hoặc link cPanel từ hosting)
2. Đăng nhập với thông tin hosting

### Bước 2: Mở File Manager
1. Tìm và click **File Manager** trong cPanel
2. Chọn **public_html** (hoặc **www**)

### Bước 3: Tạo thư mục themes (nếu chưa có)
1. Điều hướng đến `wp-content/themes/`
2. Nếu chưa có, tạo thư mục `themes` trong `wp-content`

### Bước 4: Upload Theme
1. Trong File Manager, vào `wp-content/themes/`
2. Click **Upload** (phía trên)
3. Chọn tất cả files trong thư mục `wordpress-theme`:
   - Chọn nhiều files: Giữ Ctrl (Windows) hoặc Cmd (Mac) và click từng file
   - Hoặc upload từng file một
4. Đợi upload hoàn tất

### Bước 5: Tạo cấu trúc thư mục
- Nếu upload từng file, cần tạo đúng cấu trúc:
  ```
  wp-content/themes/wordpress-theme/
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

### Bước 6: Kích hoạt Theme
1. Đăng nhập WordPress Admin
2. Vào **Appearance > Themes**
3. Click **Activate**

---

## 📦 PHƯƠNG PHÁP 3: Upload qua WordPress Admin (Nếu được phép)

### Lưu ý:
- Phương pháp này chỉ hoạt động nếu hosting cho phép upload themes
- Nhiều hosting chặn tính năng này vì lý do bảo mật

### Bước 1: Nén theme thành file ZIP
1. **Windows**: 
   - Click phải vào thư mục `wordpress-theme`
   - Chọn **Send to > Compressed (zipped) folder**
   - Đặt tên: `wordpress-theme.zip`

2. **Mac/Linux**:
   ```bash
   cd c:\Projects\Website
   zip -r wordpress-theme.zip wordpress-theme/
   ```

### Bước 2: Upload qua WordPress Admin
1. Đăng nhập WordPress Admin
2. Vào **Appearance > Themes**
3. Click **Add New**
4. Click **Upload Theme**
5. Chọn file `wordpress-theme.zip`
6. Click **Install Now**
7. Click **Activate**

---

## 💻 PHƯƠNG PHÁP 4: Upload qua SSH/Command Line

### Bước 1: Kết nối SSH
```bash
ssh username@yourdomain.com
# Hoặc
ssh username@IP_ADDRESS
```

### Bước 2: Upload theme
**Cách 1: Sử dụng SCP (từ máy local)**
```bash
# Từ máy Windows (PowerShell hoặc Git Bash)
scp -r wordpress-theme username@yourdomain.com:/path/to/wp-content/themes/
```

**Cách 2: Upload file ZIP rồi giải nén trên server**
```bash
# 1. Upload ZIP qua FTP hoặc SCP
# 2. SSH vào server
cd /path/to/wp-content/themes/
unzip wordpress-theme.zip
mv wordpress-theme dnttvn-theme  # Đổi tên nếu muốn
```

### Bước 3: Set permissions
```bash
cd /path/to/wp-content/themes/wordpress-theme
chmod -R 755 .
chmod 644 *.php
```

---

## ✅ SAU KHI UPLOAD - Thiết lập Theme

### 1. Kích hoạt Theme
- **Appearance > Themes > Activate**

### 2. Tạo Trang Doanh nghiệp
1. Vào **Pages > Add New**
2. Tiêu đề: "Danh sách Doanh nghiệp"
3. **Template**: Chọn "Trang Doanh nghiệp" (nếu có)
4. **Slug**: `page-doanh-nghiep`
5. **Publish**

### 3. Thiết lập Permalinks
1. Vào **Settings > Permalinks**
2. Click **Save Changes** (để flush rewrite rules)

### 4. Thiết lập Menu
1. Vào **Appearance > Menus**
2. Tạo menu mới
3. Thêm các link cần thiết
4. Chọn vị trí: **Menu chính**

### 5. Upload Logo
1. Vào **Appearance > Customize > Site Identity**
2. Upload logo
3. **Publish**

---

## 🔧 Troubleshooting

### Lỗi: "Theme is missing the style.css stylesheet"
**Nguyên nhân**: Cấu trúc thư mục sai hoặc thiếu file `style.css`
**Giải pháp**:
- Kiểm tra file `style.css` có trong thư mục theme
- Đảm bảo cấu trúc: `wp-content/themes/wordpress-theme/style.css`

### Lỗi: "Unable to locate WordPress root directory"
**Nguyên nhân**: Upload vào sai thư mục
**Giải pháp**:
- Theme phải ở: `wp-content/themes/`
- Không phải: `wp-content/` hoặc root directory

### Lỗi: "Permission denied"
**Nguyên nhân**: Quyền truy cập file không đúng
**Giải pháp** (qua SSH):
```bash
chmod -R 755 wp-content/themes/wordpress-theme
chmod 644 wp-content/themes/wordpress-theme/*.php
```

### Theme không hiển thị trong danh sách
**Giải pháp**:
1. Kiểm tra file `style.css` có header đúng:
   ```css
   /*
   Theme Name: Cộng đồng Doanh nhân Trí tuệ Việt Nam
   */
   ```
2. Flush cache (nếu dùng caching plugin)
3. Refresh trang Themes

### Custom Post Types không hiển thị
**Giải pháp**:
1. Vào **Settings > Permalinks**
2. Click **Save Changes**
3. Clear cache

---

## 📝 Checklist Upload

- [ ] Đã có thông tin FTP/cPanel
- [ ] Đã chuẩn bị phần mềm FTP (nếu dùng FTP)
- [ ] Đã upload toàn bộ files vào `wp-content/themes/wordpress-theme/`
- [ ] Đã kiểm tra cấu trúc thư mục đúng
- [ ] Đã kích hoạt theme
- [ ] Đã tạo trang "Danh sách Doanh nghiệp"
- [ ] Đã thiết lập Permalinks
- [ ] Đã thiết lập Menu
- [ ] Đã upload Logo
- [ ] Đã test các chức năng

---

## 🆘 Cần hỗ trợ?

Nếu gặp vấn đề, kiểm tra:
1. File `INSTALL.md` - Hướng dẫn cài đặt chi tiết
2. File `ADMIN_FEATURES.md` - Tính năng quản lý
3. File `README.md` - Tổng quan theme

**Lưu ý**: Nếu hosting yêu cầu "Upgrade to Personal plan" để upload theme, bạn cần:
- Sử dụng FTP/SFTP (Phương pháp 1) - **KHÔNG CẦN** upgrade
- Hoặc sử dụng cPanel File Manager (Phương pháp 2) - **KHÔNG CẦN** upgrade
- Chỉ Phương pháp 3 (WordPress Admin Upload) mới cần upgrade
