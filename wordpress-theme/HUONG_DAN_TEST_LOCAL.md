# HƯỚNG DẪN TEST LOCAL VỚI DATABASE

## 🎯 MỤC TIÊU
Chạy WordPress local với database để test trang quản lý nội dung trước khi deploy lên hosting.

---

## ⚡ CÁCH NHANH NHẤT: Local by Flywheel

### Bước 1: Cài đặt Local (5 phút)

1. **Tải Local**:
   - Truy cập: https://localwp.com/
   - Download cho Windows
   - Cài đặt (chọn "Install" và chờ)

2. **Tạo Site WordPress**:
   - Mở Local app
   - Click **"Create a new site"**
   - Tên site: `dnttvn`
   - Environment: Chọn **"Preferred"** (WordPress mới nhất)
   - Username: `admin`
   - Password: `admin123` (hoặc password bạn muốn - **GHI NHỚ!**)
   - Email: email của bạn
   - Click **"Add Site"**
   - Chờ 2-5 phút (Local tự động cài WordPress + Database)

### Bước 2: Copy Theme (2 phút)

1. **Tìm thư mục WordPress**:
   - Trong Local, click vào site `dnttvn`
   - Click tab **"Overview"**
   - Xem **"Path"** (ví dụ: `C:\Users\[Tên bạn]\Local Sites\dnttvn\app\public\`)
   - Hoặc click **"Open Site Shell"** để mở terminal tại thư mục WordPress

2. **Cấu trúc thư mục WordPress**:
   ```
   C:\Users\[Tên bạn]\Local Sites\dnttvn\app\public\
   ├── wp-admin\
   ├── wp-includes\
   ├── wp-content\          ← THƯ MỤC NÀY CHỨA THEME
   │   ├── themes\          ← COPY THEME VÀO ĐÂY
   │   ├── plugins\
   │   └── uploads\
   ├── wp-config.php
   └── index.php
   ```

3. **Copy Theme** (CÁCH 1 - Qua File Explorer):
   - Mở File Explorer (Windows + E)
   - Đi tới: `C:\Users\[Tên bạn]\Local Sites\dnttvn\app\public\wp-content\themes\`
   - **LƯU Ý**: Nếu không thấy thư mục `wp-content`, có thể:
     - WordPress chưa cài đặt xong → Chờ Local hoàn tất
     - Đường dẫn sai → Kiểm tra lại Path trong Local
   - Copy thư mục `wordpress-theme` từ project của bạn
   - Paste vào thư mục `themes` này
   - Đổi tên thành: `dnttvn-theme`

4. **Copy Theme** (CÁCH 2 - Qua Local):
   - Trong Local, click vào site `dnttvn`
   - Click tab **"Overview"**
   - Click **"Reveal in Finder"** (hoặc "Open in Explorer") để mở thư mục WordPress
   - Đi tới: `wp-content\themes\`
   - Copy thư mục `wordpress-theme` vào đây
   - Đổi tên thành: `dnttvn-theme`

5. **Kiểm tra đã copy đúng**:
   - Thư mục cuối cùng phải là: `C:\Users\[Tên bạn]\Local Sites\dnttvn\app\public\wp-content\themes\dnttvn-theme\`
   - Bên trong phải có các file:
     - `style.css`
     - `functions.php`
     - `header.php`
     - `footer.php`
     - `index.php`
     - `page-doanh-nghiep.php`
     - Thư mục `assets\` (chứa CSS, JS)

### Bước 3: Kích hoạt Theme (1 phút)

1. **Vào WordPress Admin**:
   - Trong Local, click nút **"WP Admin"**
   - Đăng nhập:
     - Username: `admin`
     - Password: `admin123` (hoặc password bạn đã đặt)

2. **Kích hoạt Theme**:
   - Vào **Appearance > Themes**
   - Tìm theme **"Cộng đồng Doanh nhân Trí tuệ Việt Nam"**
   - Click **"Activate"**

### Bước 4: Thiết lập ban đầu (3 phút)

1. **Tạo Trang "Danh sách Doanh nghiệp"**:
   - Vào **Pages > Add New**
   - Tiêu đề: **"Danh sách Doanh nghiệp"**
   - Bên phải: **Page Attributes > Template** → Chọn **"Trang Doanh nghiệp"**
   - Permalink: Đặt slug là `page-doanh-nghiep`
   - Click **"Publish"**

2. **Cấu hình Permalinks** (QUAN TRỌNG!):
   - Vào **Settings > Permalinks**
   - Chọn **"Post name"**
   - Click **"Save Changes"**

### Bước 5: Test Database và Quản lý (5 phút)

1. **Kiểm tra Database**:
   - Trong Local, click vào site `dnttvn`
   - Click tab **"Database"**
   - Xem thông tin:
     - **Database Name**: `local` (hoặc tên tự động)
     - **Username**: `root`
     - **Password**: `root` (mặc định)
   - Click **"Open Adminer"** hoặc **"Open TablePlus"** để xem database

2. **Test Thêm Tin tức**:
   - Vào **Tin tức > Thêm mới**
   - Tiêu đề: "Tin tức test 1"
   - Nội dung: "Đây là tin tức test"
   - Chọn Featured Image (nếu có)
   - Click **"Publish"**
   - Kiểm tra: Vào **Tin tức > Tất cả Tin tức** → Xem tin vừa tạo

3. **Test Thêm Doanh nghiệp**:
   - Vào **Doanh nghiệp > Thêm mới**
   - Tiêu đề: "Công ty ABC"
   - Mô tả: "Mô tả công ty"
   - **Meta Box "Thông tin Doanh nghiệp"**:
     - Ngành hàng: `Công nghệ`
     - Khu vực: `Hà Nội`
     - Hình ảnh phụ: Click "Chọn hình ảnh" để test media uploader
   - Chọn Featured Image
   - Bên phải: Chọn **Ngành hàng** và **Khu vực** từ taxonomies
   - Click **"Publish"**

4. **Test Quản lý Database**:
   - Vào **Doanh nghiệp > Tất cả Doanh nghiệp**
   - Kiểm tra:
     - ✅ Có cột "Ngành hàng", "Khu vực"
     - ✅ Có Filters phía trên (dropdown Ngành hàng, Khu vực)
     - ✅ Có thể Search
     - ✅ Có thể Quick Edit (hover vào item → Quick Edit)
     - ✅ Có thể Bulk Edit (chọn nhiều items → Bulk Actions → Edit)

5. **Kiểm tra Dashboard**:
   - Vào **Dashboard** (trang chủ Admin)
   - Xem widget **"Thống kê Cộng đồng"**:
     - Số lượng Tin tức
     - Số lượng Doanh nghiệp
     - Số lượng Ngành hàng, Khu vực

---

## 🗄️ THÔNG TIN DATABASE

### Truy cập Database

**Cách 1: Qua Local**
- Trong Local app → Click site `dnttvn` → Tab **"Database"**
- Click **"Open Adminer"** hoặc **"Open TablePlus"**

**Cách 2: Qua phpMyAdmin (nếu dùng XAMPP)**
- URL: http://localhost/phpmyadmin
- Username: `root`
- Password: (để trống hoặc `root`)

### Các bảng Database quan trọng

1. **`wp_posts`**: Lưu tất cả posts (Tin tức, Doanh nghiệp)
   - `post_type = 'tin_tuc'` → Tin tức
   - `post_type = 'doanh_nghiep'` → Doanh nghiệp

2. **`wp_postmeta`**: Lưu custom fields
   - `meta_key = '_nganh_hang'` → Ngành hàng
   - `meta_key = '_khu_vuc'` → Khu vực
   - `meta_key = '_hinh_anh_phu'` → Hình ảnh phụ

3. **`wp_terms`**: Lưu terms của taxonomies
   - Ngành hàng, Khu vực

4. **`wp_term_taxonomy`**: Định nghĩa taxonomies
   - `taxonomy = 'nganh_hang'`
   - `taxonomy = 'khu_vuc'`

5. **`wp_term_relationships`**: Liên kết posts với terms

### Test Query Database

**Xem tất cả Doanh nghiệp**:
```sql
SELECT * FROM wp_posts WHERE post_type = 'doanh_nghiep' AND post_status = 'publish';
```

**Xem custom fields của Doanh nghiệp**:
```sql
SELECT p.post_title, pm.meta_key, pm.meta_value 
FROM wp_posts p
LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id
WHERE p.post_type = 'doanh_nghiep' 
AND pm.meta_key IN ('_nganh_hang', '_khu_vuc', '_hinh_anh_phu');
```

**Xem Ngành hàng và Khu vực (taxonomies)**:
```sql
SELECT t.name, tt.taxonomy
FROM wp_terms t
JOIN wp_term_taxonomy tt ON t.term_id = tt.term_id
WHERE tt.taxonomy IN ('nganh_hang', 'khu_vuc');
```

---

## ✅ CHECKLIST TEST

- [ ] WordPress local đã chạy được
- [ ] Có thể đăng nhập wp-admin
- [ ] Theme đã được kích hoạt
- [ ] Database đã được tạo tự động
- [ ] Có thể thêm Tin tức và lưu vào database
- [ ] Có thể thêm Doanh nghiệp và lưu vào database
- [ ] Custom fields (Ngành hàng, Khu vực) được lưu vào `wp_postmeta`
- [ ] Taxonomies (Ngành hàng, Khu vực) được lưu vào `wp_terms`
- [ ] Admin columns hiển thị đúng
- [ ] Filters hoạt động
- [ ] Quick Edit hoạt động
- [ ] Bulk Edit hoạt động
- [ ] Search hoạt động
- [ ] Dashboard widget hiển thị thống kê
- [ ] Media uploader hoạt động

---

## 🔧 TROUBLESHOOTING

### Không tìm thấy thư mục wp-content
- **Nguyên nhân**: WordPress chưa cài đặt xong
- **Giải pháp**: 
  - Chờ Local hoàn tất cài đặt (2-5 phút)
  - Kiểm tra trong Local: Site phải có status "Running" (màu xanh)
  - Nếu vẫn không có: Xóa site và tạo lại

### Local không tạo được site
- Chạy Local với quyền Administrator
- Tắt antivirus tạm thời
- Kiểm tra Windows version (Windows 10/11)

### Không vào được wp-admin
- Kiểm tra Local app đã start site chưa (nút "Start" phải màu xanh)
- Click nút "WP Admin" trong Local
- Hoặc truy cập: `http://dnttvn.local/wp-admin`

### Database không có dữ liệu
- Kiểm tra đã Publish posts chưa (không phải Draft)
- Kiểm tra trong Admin: Posts phải có status "Published"
- Vào Database → `wp_posts` → Kiểm tra `post_status = 'publish'`

### Custom fields không lưu
- Kiểm tra đã điền Meta Box "Thông tin Doanh nghiệp" chưa
- Kiểm tra trong Database: `wp_postmeta` → Tìm `meta_key = '_nganh_hang'`
- Vào **Settings > Permalinks** → Click **Save Changes**

### Theme không hiển thị trong Admin
- **Kiểm tra đường dẫn**: Theme phải ở `wp-content\themes\dnttvn-theme\`
- **Kiểm tra file style.css**: Phải có file `style.css` trong thư mục theme (file này chứa thông tin theme header)
- **Kiểm tra tên thư mục**: Không được có khoảng trắng hoặc ký tự đặc biệt
- **Refresh trang Admin**: F5 hoặc Ctrl + F5 để reload

### Theme không hiển thị đúng
- Kiểm tra file `style-gioi-thieu.css` có trong `assets/` chưa
- Kiểm tra tất cả files đã được copy đầy đủ
- Kiểm tra cấu trúc thư mục:
  ```
  dnttvn-theme/
  ├── style.css          ← PHẢI CÓ FILE NÀY
  ├── functions.php
  ├── header.php
  ├── footer.php
  ├── index.php
  ├── page-doanh-nghiep.php
  └── assets/
      ├── style-gioi-thieu.css
      ├── script.js
      └── admin-script.js
  ```
- Clear cache trình duyệt

---

## 📍 URL QUAN TRỌNG

- **Trang chủ**: `http://dnttvn.local` (hoặc URL trong Local)
- **Admin**: `http://dnttvn.local/wp-admin`
- **Database Adminer**: Click "Open Adminer" trong Local → Tab Database

---

## 🎯 BƯỚC TIẾP THEO

Sau khi test xong local:
1. Test tất cả tính năng quản lý
2. Thêm nhiều dữ liệu test (Tin tức, Doanh nghiệp)
3. Test các tính năng: Filter, Search, Quick Edit, Bulk Edit
4. Kiểm tra database có lưu đúng không
5. Khi sẵn sàng → Deploy lên hosting

**Chúc bạn test thành công! 🚀**
