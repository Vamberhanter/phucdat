# HƯỚNG DẪN CÀI ĐẶT WORDPRESS LOCAL (WINDOWS)

## 🎯 PHƯƠNG PHÁP 1: Local by Flywheel (KHUYÊN DÙNG - Dễ nhất)

### Bước 1: Tải và Cài đặt Local

1. **Tải Local by Flywheel**:
   - Truy cập: https://localwp.com/
   - Click **"Download"** (miễn phí)
   - Chọn **Windows**
   - Tải file `.exe` về máy

2. **Cài đặt Local**:
   - Chạy file `.exe` vừa tải
   - Click **"Install"** và làm theo hướng dẫn
   - Chờ cài đặt hoàn tất (có thể mất 5-10 phút)

### Bước 2: Tạo Site WordPress mới

1. **Mở Local**:
   - Mở ứng dụng Local từ Desktop hoặc Start Menu
   - Lần đầu mở sẽ có hướng dẫn

2. **Tạo Site mới**:
   - Click nút **"Create a new site"** (màu xanh)
   - Chọn **"Create a new site"**
   - Đặt tên site: `dnttvn` (hoặc tên bạn muốn)
   - Click **"Continue"**

3. **Chọn Environment**:
   - Chọn **"Preferred"** (WordPress mới nhất, PHP 8.0, MySQL 8.0)
   - Click **"Continue"**

4. **Thiết lập Admin**:
   - **WordPress username**: `admin` (hoặc tên bạn muốn)
   - **WordPress password**: Đặt password mạnh (ghi nhớ!)
   - **WordPress email**: Email của bạn
   - Click **"Add Site"**

5. **Chờ cài đặt**:
   - Local sẽ tự động tải và cài đặt WordPress
   - Mất khoảng 2-5 phút
   - Khi xong, bạn sẽ thấy site trong danh sách

### Bước 3: Upload Theme vào WordPress

1. **Tìm thư mục WordPress**:
   - Trong Local, click vào site `dnttvn`
   - Click tab **"Overview"**
   - Click nút **"Open Site Shell"** (hoặc tìm đường dẫn trong "Path")
   - Thường là: `C:\Users\[YourName]\Local Sites\dnttvn\app\public\`

2. **Copy Theme**:
   - Mở File Explorer
   - Đi tới: `C:\Users\[YourName]\Local Sites\dnttvn\app\public\wp-content\themes\`
   - Copy toàn bộ thư mục `wordpress-theme` từ project của bạn
   - Paste vào thư mục `themes` này
   - **Đổi tên** thư mục thành: `dnttvn-theme` (hoặc tên bạn muốn)

3. **Kích hoạt Theme**:
   - Trong Local, click nút **"WP Admin"** (mở WordPress Admin)
   - Đăng nhập với username/password bạn đã tạo ở Bước 2.4
   - Vào **Appearance > Themes**
   - Tìm theme **"Cộng đồng Doanh nhân Trí tuệ Việt Nam"**
   - Click **"Activate"**

### Bước 4: Truy cập Trang Quản lý

- **URL Trang quản lý**: Click nút **"WP Admin"** trong Local
- **Hoặc**: `http://dnttvn.local/wp-admin`
- **Username**: (username bạn đã tạo)
- **Password**: (password bạn đã tạo)

---

## 🛠️ PHƯƠNG PHÁP 2: XAMPP (Nếu không dùng được Local)

### Bước 1: Cài đặt XAMPP

1. **Tải XAMPP**:
   - Truy cập: https://www.apachefriends.org/download.html
   - Chọn **XAMPP for Windows** (phiên bản mới nhất)
   - Tải file `.exe` về máy

2. **Cài đặt XAMPP**:
   - Chạy file `.exe`
   - Chọn components: **Apache**, **MySQL**, **PHP**, **phpMyAdmin**
   - Chọn thư mục cài đặt: `C:\xampp` (mặc định)
   - Click **"Install"**
   - Chờ cài đặt hoàn tất

3. **Khởi động Services**:
   - Mở **XAMPP Control Panel**
   - Click **"Start"** cho **Apache**
   - Click **"Start"** cho **MySQL**
   - Nếu có cảnh báo Windows Firewall, chọn **"Allow access"**

### Bước 2: Tải và Cài đặt WordPress

1. **Tải WordPress**:
   - Truy cập: https://wordpress.org/download/
   - Click **"Download WordPress"**
   - Giải nén file `.zip`

2. **Copy WordPress**:
   - Copy thư mục `wordpress` từ file đã giải nén
   - Paste vào: `C:\xampp\htdocs\`
   - Đổi tên thành: `dnttvn` (hoặc tên bạn muốn)

### Bước 3: Tạo Database

1. **Mở phpMyAdmin**:
   - Mở trình duyệt
   - Truy cập: http://localhost/phpmyadmin

2. **Tạo Database**:
   - Click tab **"Databases"**
   - Database name: `wordpress_db`
   - Collation: `utf8mb4_unicode_ci`
   - Click **"Create"**

### Bước 4: Cài đặt WordPress

1. **Mở WordPress Installer**:
   - Truy cập: http://localhost/dnttvn
   - Chọn ngôn ngữ: **Tiếng Việt** (hoặc English)
   - Click **"Continue"**

2. **Thông tin Database**:
   - **Database Name**: `wordpress_db`
   - **Username**: `root`
   - **Password**: (để trống)
   - **Database Host**: `localhost`
   - **Table Prefix**: `wp_` (giữ nguyên)
   - Click **"Submit"**

3. **Thông tin Site**:
   - **Site Title**: "Cộng đồng Doanh nhân Trí tuệ Việt Nam"
   - **Username**: `admin` (ghi nhớ!)
   - **Password**: Đặt password mạnh (ghi nhớ!)
   - **Your Email**: Email của bạn
   - Click **"Install WordPress"**

4. **Hoàn tất**:
   - Click **"Log In"**
   - Đăng nhập với username/password vừa tạo

### Bước 5: Upload Theme

1. **Copy Theme**:
   - Copy thư mục `wordpress-theme` từ project
   - Paste vào: `C:\xampp\htdocs\dnttvn\wp-content\themes\`
   - Đổi tên thành: `dnttvn-theme`

2. **Kích hoạt Theme**:
   - Vào: http://localhost/dnttvn/wp-admin
   - **Appearance > Themes**
   - Activate theme **"Cộng đồng Doanh nhân Trí tuệ Việt Nam"**

### Bước 6: Truy cập Trang Quản lý

- **URL**: http://localhost/dnttvn/wp-admin
- **Username**: (username bạn đã tạo)
- **Password**: (password bạn đã tạo)

---

## ✅ THIẾT LẬP BAN ĐẦU (Sau khi kích hoạt theme)

### 1. Tạo Trang "Danh sách Doanh nghiệp"

1. Vào **Pages > Add New**
2. **Tiêu đề**: "Danh sách Doanh nghiệp"
3. Bên phải, tìm **Page Attributes > Template**
4. Chọn: **"Trang Doanh nghiệp"**
5. Bên phải, tìm **Permalink**
6. Đặt slug: `page-doanh-nghiep`
7. Click **"Publish"**

### 2. Cấu hình Permalinks (QUAN TRỌNG!)

1. Vào **Settings > Permalinks**
2. Chọn **"Post name"**
3. Click **"Save Changes"**

### 3. Thiết lập Menu (Tùy chọn)

1. Vào **Appearance > Menus**
2. Tạo menu mới: "Menu chính"
3. Thêm items:
   - Trang chủ
   - Trang Doanh nghiệp
   - Tin tức
   - Doanh nghiệp
4. Chọn vị trí: **Menu chính**
5. **Save Menu**

---

## 🎯 KIỂM TRA ĐÃ CÀI ĐẶT THÀNH CÔNG

### Checklist:

- [ ] WordPress đã cài đặt và chạy được
- [ ] Có thể đăng nhập vào wp-admin
- [ ] Theme đã được kích hoạt
- [ ] Trang "Danh sách Doanh nghiệp" đã được tạo
- [ ] Permalinks đã được cấu hình
- [ ] Có thể thấy menu "Tin tức" và "Doanh nghiệp" trong Admin

### Test các tính năng:

1. **Thêm Tin tức**:
   - Vào **Tin tức > Thêm mới**
   - Tạo 1 tin tức test
   - **Publish**

2. **Thêm Doanh nghiệp**:
   - Vào **Doanh nghiệp > Thêm mới**
   - Tạo 1 doanh nghiệp test
   - Điền Ngành hàng, Khu vực
   - **Publish**

3. **Kiểm tra Dashboard**:
   - Vào **Dashboard** (trang chủ Admin)
   - Xem widget **"Thống kê Cộng đồng"**

4. **Kiểm tra Quản lý**:
   - Vào **Doanh nghiệp > Tất cả Doanh nghiệp**
   - Kiểm tra có các cột: Ngành hàng, Khu vực
   - Kiểm tra có Filters phía trên
   - Test Quick Edit, Search

---

## 🔧 TROUBLESHOOTING

### Local không chạy được
- Kiểm tra Windows version (Windows 10/11)
- Tắt antivirus tạm thời khi cài
- Chạy Local với quyền Administrator

### XAMPP Apache không start
- Kiểm tra port 80 có bị chiếm không (thường do Skype, IIS)
- Đổi port Apache: XAMPP Control Panel > Config > Apache > httpd.conf > đổi Listen 80 thành 8080
- Sau đó truy cập: http://localhost:8080/dnttvn

### XAMPP MySQL không start
- Kiểm tra port 3306 có bị chiếm không
- Đổi port MySQL: XAMPP Control Panel > Config > MySQL > my.ini > đổi port 3306 thành 3307

### Không vào được wp-admin
- Kiểm tra URL đúng chưa
- Kiểm tra Apache/MySQL đã start chưa
- Clear cache trình duyệt (Ctrl + Shift + Delete)

### Theme không hiển thị
- Kiểm tra file `style-gioi-thieu.css` có trong `assets/` chưa
- Kiểm tra tất cả files trong theme đã được copy đầy đủ
- Vào **Settings > Permalinks** và click **Save Changes**

### Media Uploader không hoạt động
- Kiểm tra file `admin-script.js` có trong `assets/` chưa
- Kiểm tra WordPress version >= 5.0
- Clear cache trình duyệt

---

## 📞 HỖ TRỢ

Nếu gặp vấn đề:
1. Kiểm tra lại các bước trên
2. Xem file `HUONG_DAN_CHAY_QUAN_LY.md` để biết cách sử dụng
3. Kiểm tra file `ADMIN_FEATURES.md` để xem các tính năng

**Chúc bạn cài đặt thành công! 🎉**
