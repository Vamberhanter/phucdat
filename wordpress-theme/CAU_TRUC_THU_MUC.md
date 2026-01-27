# CẤU TRÚC THƯ MỤC WORDPRESS VÀ THEME

## 📁 Cấu trúc WordPress Local (Local by Flywheel)

```
C:\Users\[Tên bạn]\Local Sites\dnttvn\app\public\
│
├── wp-admin\              # WordPress Admin files
├── wp-includes\           # WordPress core files
│
├── wp-content\            # ⭐ THƯ MỤC QUAN TRỌNG - Chứa theme, plugins, uploads
│   │
│   ├── themes\           # ⭐ COPY THEME VÀO ĐÂY
│   │   ├── twentytwentyfour\    # Theme mặc định
│   │   ├── twentytwentythree\    # Theme mặc định
│   │   └── dnttvn-theme\        # ⭐ THEME CỦA BẠN (copy vào đây)
│   │       ├── style.css
│   │       ├── functions.php
│   │       ├── header.php
│   │       ├── footer.php
│   │       ├── index.php
│   │       ├── page-doanh-nghiep.php
│   │       ├── single-tin-tuc.php
│   │       ├── single-doanh-nghiep.php
│   │       └── assets\
│   │           ├── style-gioi-thieu.css
│   │           ├── script.js
│   │           └── admin-script.js
│   │
│   ├── plugins\          # WordPress plugins
│   └── uploads\          # Media files (hình ảnh, video)
│
├── wp-config.php          # File cấu hình WordPress
└── index.php             # File index chính
```

## 📁 Cấu trúc Theme (dnttvn-theme)

```
dnttvn-theme/
│
├── style.css                    # ⭐ BẮT BUỘC - Theme header + import CSS
├── functions.php                # ⭐ BẮT BUỘC - Functions và hooks
├── header.php                   # Template header
├── footer.php                   # Template footer
├── index.php                    # Template homepage (Tin tức)
├── page-doanh-nghiep.php        # Template trang Doanh nghiệp
├── single-tin-tuc.php          # Template chi tiết Tin tức
├── single-doanh-nghiep.php     # Template chi tiết Doanh nghiệp
│
├── assets/                      # Thư mục chứa CSS, JS, images
│   ├── style-gioi-thieu.css    # CSS chính
│   ├── script.js               # JavaScript frontend
│   └── admin-script.js         # JavaScript admin
│
├── README.md                    # Tài liệu theme
├── INSTALL.md                   # Hướng dẫn cài đặt
├── ADMIN_FEATURES.md            # Tính năng quản lý
├── HUONG_DAN_TEST_LOCAL.md     # Hướng dẫn test local
└── HUONG_DAN_CAI_LOCAL.md      # Hướng dẫn cài đặt local
```

## 🔍 Cách tìm thư mục wp-content

### Cách 1: Qua Local App
1. Mở Local app
2. Click vào site `dnttvn`
3. Click tab **"Overview"**
4. Xem **"Path"** → Copy đường dẫn
5. Mở File Explorer → Paste đường dẫn
6. Đi tới: `wp-content\themes\`

### Cách 2: Qua "Reveal in Finder"
1. Trong Local, click vào site `dnttvn`
2. Click tab **"Overview"**
3. Click **"Reveal in Finder"** (Windows) hoặc **"Open in Explorer"**
4. Đi tới: `wp-content\themes\`

### Cách 3: Qua "Open Site Shell"
1. Trong Local, click vào site `dnttvn`
2. Click tab **"Overview"**
3. Click **"Open Site Shell"**
4. Gõ lệnh: `cd wp-content/themes`
5. Gõ lệnh: `explorer .` (Windows) để mở File Explorer tại đây

## ✅ Kiểm tra đã copy đúng

### Checklist:
- [ ] Thư mục theme nằm trong: `wp-content\themes\dnttvn-theme\`
- [ ] Có file `style.css` trong thư mục theme (file này bắt buộc!)
- [ ] Có file `functions.php` trong thư mục theme
- [ ] Có thư mục `assets\` với các file CSS, JS
- [ ] Tên thư mục không có khoảng trắng hoặc ký tự đặc biệt

### Kiểm tra bằng lệnh (trong Site Shell):
```bash
# Di chuyển vào thư mục themes
cd wp-content/themes

# Liệt kê các theme
dir

# Kiểm tra cấu trúc theme
cd dnttvn-theme
dir
```

## 🚨 Lỗi thường gặp

### Lỗi: "Theme không hiển thị trong Admin"
**Nguyên nhân**: 
- Thiếu file `style.css`
- Đường dẫn sai
- Tên thư mục có ký tự đặc biệt

**Giải pháp**:
- Kiểm tra file `style.css` có trong thư mục theme
- Đổi tên thư mục thành `dnttvn-theme` (không có khoảng trắng)
- Refresh trang Admin (F5)

### Lỗi: "Không tìm thấy wp-content"
**Nguyên nhân**: 
- WordPress chưa cài đặt xong
- Đường dẫn sai

**Giải pháp**:
- Chờ Local hoàn tất cài đặt (2-5 phút)
- Kiểm tra site có status "Running" trong Local
- Kiểm tra lại đường dẫn Path trong Local

### Lỗi: "Theme không hoạt động"
**Nguyên nhân**: 
- Thiếu file bắt buộc (`style.css`, `functions.php`)
- Lỗi syntax trong PHP

**Giải pháp**:
- Kiểm tra tất cả files đã được copy đầy đủ
- Kiểm tra file `style.css` có theme header đúng
- Xem lỗi trong WordPress Admin → Tools → Site Health

## 📝 Ghi chú

- **wp-content** là thư mục quan trọng nhất, chứa tất cả nội dung tùy chỉnh
- **themes** là nơi chứa các theme WordPress
- Mỗi theme phải có file `style.css` với theme header ở đầu file
- Tên thư mục theme không được có khoảng trắng (dùng dấu gạch ngang `-`)
