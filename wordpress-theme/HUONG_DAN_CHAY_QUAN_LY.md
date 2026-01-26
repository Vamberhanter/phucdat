# HƯỚNG DẪN CHẠY TRANG QUẢN LÝ NỘI DUNG

## 🚀 CÁCH 1: Cài đặt WordPress trên máy tính (Local - Khuyên dùng để test)

### Bước 1: Cài đặt Local WordPress Environment

#### Option A: Sử dụng XAMPP (Windows)
1. **Tải XAMPP**: https://www.apachefriends.org/download.html
2. **Cài đặt XAMPP**:
   - Chọn Apache, MySQL, PHP
   - Cài đặt vào `C:\xampp`
3. **Khởi động XAMPP**:
   - Mở XAMPP Control Panel
   - Start **Apache** và **MySQL**
4. **Tải WordPress**:
   - Tải từ: https://wordpress.org/download/
   - Giải nén vào `C:\xampp\htdocs\wordpress`
5. **Tạo Database**:
   - Mở: http://localhost/phpmyadmin
   - Tạo database mới: `wordpress_db`
6. **Cài đặt WordPress**:
   - Mở: http://localhost/wordpress
   - Làm theo hướng dẫn cài đặt
   - Database name: `wordpress_db`
   - Username: `root`
   - Password: (để trống)
   - Database Host: `localhost`

#### Option B: Sử dụng Local by Flywheel (Dễ nhất)
1. **Tải Local**: https://localwp.com/
2. **Cài đặt Local**
3. **Tạo site mới**:
   - Click "Create a new site"
   - Chọn "Create a new site"
   - Đặt tên: `dnttvn`
   - Chọn WordPress version mới nhất
   - Tạo username/password admin
4. **WordPress sẽ tự động cài đặt**

### Bước 2: Upload Theme vào WordPress

1. **Copy theme**:
   - Copy toàn bộ thư mục `wordpress-theme` từ project của bạn
   - Paste vào:
     - **XAMPP**: `C:\xampp\htdocs\wordpress\wp-content\themes\`
     - **Local**: Click "Open Site Shell" > `cd wp-content/themes` > paste theme vào
   - Đổi tên thư mục thành: `dnttvn-theme` (hoặc tên bạn muốn)

2. **Kích hoạt Theme**:
   - Mở WordPress Admin: http://localhost/wordpress/wp-admin (XAMPP) hoặc click "WP Admin" trong Local
   - Đăng nhập với username/password bạn đã tạo
   - Vào **Appearance > Themes**
   - Tìm theme "Cộng đồng Doanh nhân Trí tuệ Việt Nam"
   - Click **Activate**

### Bước 3: Truy cập Trang Quản lý

**URL Trang quản lý**: 
- XAMPP: `http://localhost/wordpress/wp-admin`
- Local: Click nút "WP Admin" trong Local app

**Đăng nhập với**:
- Username: (username bạn đã tạo khi cài WordPress)
- Password: (password bạn đã tạo)

---

## 🌐 CÁCH 2: Cài đặt trên Hosting (Production)

### Bước 1: Chuẩn bị Hosting

**Yêu cầu Hosting**:
- PHP 7.4 trở lên
- MySQL 5.6 trở lên
- Apache hoặc Nginx

### Bước 2: Upload WordPress lên Hosting

1. **Tải WordPress**: https://wordpress.org/download/
2. **Upload lên hosting**:
   - Sử dụng FTP (FileZilla) hoặc File Manager trong cPanel
   - Upload vào thư mục `public_html` hoặc `www`
3. **Tạo Database**:
   - Vào cPanel > MySQL Databases
   - Tạo database mới: `wordpress_db`
   - Tạo user mới và gán quyền cho database
4. **Cài đặt WordPress**:
   - Truy cập: `https://yourdomain.com`
   - Làm theo hướng dẫn cài đặt
   - Nhập thông tin database vừa tạo

### Bước 3: Upload Theme

1. **Upload theme**:
   - Vào cPanel > File Manager
   - Đi tới: `public_html/wp-content/themes/`
   - Upload thư mục `wordpress-theme` (đổi tên thành `dnttvn-theme`)

2. **Kích hoạt Theme**:
   - Vào: `https://yourdomain.com/wp-admin`
   - **Appearance > Themes > Activate**

---

## 📋 THIẾT LẬP BAN ĐẦU

### 1. Tạo Trang "Danh sách Doanh nghiệp"

1. Vào **Pages > Add New**
2. Tiêu đề: **"Danh sách Doanh nghiệp"**
3. Ở bên phải, tìm **Page Attributes > Template**
4. Chọn: **"Trang Doanh nghiệp"**
5. Ở bên phải, tìm **Permalink**
6. Đặt slug: `page-doanh-nghiep`
7. Click **Publish**

### 2. Thiết lập Menu

1. Vào **Appearance > Menus**
2. Tạo menu mới: "Menu chính"
3. Thêm các items:
   - **Trang chủ** (Home)
   - **Trang Doanh nghiệp** (link đến page-doanh-nghiep)
   - **Tin tức** (Custom Link: `/tin-tuc/` hoặc archive link)
   - **Doanh nghiệp** (Custom Link: `/doanh-nghiep/` hoặc archive link)
4. Chọn vị trí: **Menu chính**
5. **Save Menu**

### 3. Thêm Logo

1. Vào **Appearance > Customize > Site Identity**
2. Click **Select Logo**
3. Upload logo của bạn
4. **Publish**

### 4. Cấu hình Permalinks (Quan trọng!)

1. Vào **Settings > Permalinks**
2. Chọn **Post name** (hoặc Custom Structure: `/%postname%/`)
3. Click **Save Changes**

---

## 🎯 SỬ DỤNG TRANG QUẢN LÝ

### Truy cập Trang Quản lý

**URL**: `http://localhost/wordpress/wp-admin` (local) hoặc `https://yourdomain.com/wp-admin` (hosting)

### Các Menu Quản lý Chính

#### 1. **Tin tức** (Menu bên trái)
- **Tất cả Tin tức**: Xem danh sách tất cả tin tức
- **Thêm mới**: Tạo tin tức mới
- **Danh mục**: Quản lý categories
- **Thẻ**: Quản lý tags

#### 2. **Doanh nghiệp** (Menu bên trái)
- **Tất cả Doanh nghiệp**: Xem danh sách với đầy đủ tính năng:
  - ✅ **Admin Columns**: Xem Ngành hàng, Khu vực
  - ✅ **Filters**: Lọc theo Ngành hàng, Khu vực
  - ✅ **Quick Edit**: Sửa nhanh từ danh sách
  - ✅ **Bulk Edit**: Sửa nhiều items cùng lúc
  - ✅ **Search**: Tìm kiếm theo tên, ngành hàng, khu vực
- **Thêm mới**: Tạo doanh nghiệp mới
- **Ngành hàng**: Quản lý taxonomy Ngành hàng
- **Khu vực**: Quản lý taxonomy Khu vực

#### 3. **Dashboard** (Trang chủ Admin)
- Widget **"Thống kê Cộng đồng"**:
  - Số lượng Tin tức (Đã xuất bản, Bản nháp, Trong thùng rác)
  - Số lượng Doanh nghiệp
  - Số lượng Ngành hàng và Khu vực
  - Quick links đến trang quản lý

---

## 📝 HƯỚNG DẪN THÊM NỘI DUNG

### Thêm Tin tức

1. Vào **Tin tức > Thêm mới**
2. Nhập **Tiêu đề**
3. Nhập **Nội dung** trong editor
4. Chọn **Featured Image** (hình ảnh đại diện) ở bên phải
5. Chọn **Danh mục** và **Thẻ** (nếu cần)
6. Click **Publish**

### Thêm Doanh nghiệp

1. Vào **Doanh nghiệp > Thêm mới**
2. Nhập **Tiêu đề** (tên doanh nghiệp)
3. Nhập **Mô tả** trong editor
4. Điền **Meta Box "Thông tin Doanh nghiệp"**:
   - **Ngành hàng**: Ví dụ: Công nghệ, Tài chính, Bán lẻ...
   - **Khu vực**: Ví dụ: Hà Nội, TP. Hồ Chí Minh, Đà Nẵng...
   - **Hình ảnh phụ**: 
     - Click **"Chọn hình ảnh"** để mở Media Library
     - Hoặc nhập URL/ID trực tiếp
5. Chọn **Featured Image** (hình ảnh đại diện)
6. Chọn **Ngành hàng** và **Khu vực** từ taxonomies bên phải
7. Click **Publish**

### Quản lý từ Danh sách

#### Quick Edit (Sửa nhanh)
1. Vào **Doanh nghiệp > Tất cả Doanh nghiệp**
2. Hover vào một doanh nghiệp
3. Click **Quick Edit**
4. Sửa Ngành hàng, Khu vực
5. Click **Update**

#### Bulk Edit (Sửa hàng loạt)
1. Chọn nhiều doanh nghiệp (checkbox)
2. Chọn **Edit** từ dropdown "Bulk Actions"
3. Click **Apply**
4. Sửa Ngành hàng, Khu vực cho tất cả items đã chọn
5. Click **Update**

#### Filter (Lọc)
1. Ở phía trên danh sách, có 2 dropdown:
   - **Tất cả Ngành hàng**
   - **Tất cả Khu vực**
2. Chọn filter và click **Filter**

#### Search (Tìm kiếm)
1. Gõ từ khóa vào ô search
2. Tìm theo: Tiêu đề, Nội dung, Ngành hàng, Khu vực

---

## 🔧 TROUBLESHOOTING

### Không vào được wp-admin
- Kiểm tra URL: `http://localhost/wordpress/wp-admin` (đúng path chưa?)
- Kiểm tra Apache/MySQL đã start chưa (XAMPP)
- Clear cache trình duyệt

### Theme không hiển thị đúng
- Kiểm tra file `style-gioi-thieu.css` có trong `assets/` chưa
- Vào **Settings > Permalinks** và click **Save Changes**
- Clear cache (nếu dùng caching plugin)

### Custom Post Types không hiển thị
- Vào **Settings > Permalinks**
- Click **Save Changes** (để flush rewrite rules)

### Media Uploader không hoạt động
- Kiểm tra file `admin-script.js` có trong `assets/` chưa
- Kiểm tra WordPress version >= 5.0
- Clear cache trình duyệt

### Menu không hiển thị
- Kiểm tra đã tạo menu và gán vào vị trí "Menu chính" chưa
- Vào **Appearance > Menus** để kiểm tra

---

## 📞 HỖ TRỢ

Nếu gặp vấn đề:
1. Kiểm tra file `INSTALL.md` và `ADMIN_FEATURES.md`
2. Kiểm tra WordPress version (>= 5.0)
3. Kiểm tra PHP version (>= 7.4)
4. Kiểm tra tất cả files trong theme đã được upload đầy đủ

---

## ✅ CHECKLIST CÀI ĐẶT

- [ ] Đã cài WordPress (local hoặc hosting)
- [ ] Đã upload theme vào `wp-content/themes/`
- [ ] Đã kích hoạt theme
- [ ] Đã tạo trang "Danh sách Doanh nghiệp"
- [ ] Đã thiết lập menu
- [ ] Đã cấu hình Permalinks
- [ ] Đã test thêm Tin tức
- [ ] Đã test thêm Doanh nghiệp
- [ ] Đã test các tính năng quản lý (filter, search, quick edit, bulk edit)

**Chúc bạn thành công! 🎉**
