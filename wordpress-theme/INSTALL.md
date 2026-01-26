# Hướng dẫn cài đặt WordPress Theme

## Yêu cầu
- WordPress 5.0 trở lên
- PHP 7.4 trở lên
- MySQL 5.6 trở lên

## Các bước cài đặt

### 1. Upload Theme

**Cách 1: Upload qua WordPress Admin (Khuyến nghị)**
1. Nén toàn bộ thư mục `wordpress-theme` thành file ZIP
2. Đăng nhập WordPress Admin > **Appearance > Themes**
3. Click **Add New** > **Upload Theme**
4. Chọn file ZIP và click **Install Now**
5. Click **Activate** sau khi cài đặt

**Cách 2: Upload qua FTP/cPanel**
- Copy toàn bộ thư mục `wordpress-theme` vào `wp-content/themes/` của WordPress
- Đổi tên thư mục thành tên bạn muốn (ví dụ: `dnttvn-theme`)

### 2. Kích hoạt Theme
1. Đăng nhập vào WordPress Admin
2. Vào **Appearance > Themes**
3. Tìm theme "Cộng đồng Doanh nhân Trí tuệ Việt Nam"
4. Click **Activate**

### 3. Tạo Trang Doanh nghiệp
1. Vào **Pages > Add New**
2. Đặt tiêu đề: "Danh sách Doanh nghiệp"
3. Chọn **Template**: "Trang Doanh nghiệp"
4. Đặt slug: `page-doanh-nghiep`
5. **Publish**

### 4. Thiết lập Menu
1. Vào **Appearance > Menus**
2. Tạo menu mới hoặc chỉnh sửa menu hiện có
3. Thêm các link:
   - Trang chủ
   - Trang Doanh nghiệp (link đến page-doanh-nghiep)
   - Archive Tin tức
   - Archive Doanh nghiệp
4. Chọn vị trí menu: **Menu chính**
5. **Save Menu**

### 5. Thêm Logo
1. Vào **Appearance > Customize > Site Identity**
2. Upload logo
3. **Publish**

## Sử dụng

### Thêm Tin tức
1. Vào **Tin tức > Thêm mới**
2. Nhập tiêu đề và nội dung
3. Chọn hình ảnh đại diện (Featured Image)
4. **Publish**

### Thêm Doanh nghiệp
1. Vào **Doanh nghiệp > Thêm mới**
2. Nhập tiêu đề và mô tả
3. Điền thông tin trong meta box:
   - **Ngành hàng**: Ví dụ: Công nghệ, Tài chính, Bán lẻ...
   - **Khu vực**: Ví dụ: Hà Nội, TP. Hồ Chí Minh, Đà Nẵng...
   - **Hình ảnh phụ**: URL hoặc ID của hình ảnh
4. Chọn hình ảnh đại diện (Featured Image)
5. Chọn **Ngành hàng** và **Khu vực** từ taxonomies bên phải
6. **Publish**

### Quản lý Ngành hàng và Khu vực
1. Vào **Doanh nghiệp > Ngành hàng** để thêm/sửa ngành hàng
2. Vào **Doanh nghiệp > Khu vực** để thêm/sửa khu vực

## Database

WordPress tự động tạo và quản lý database. Các bảng chính:
- `wp_posts`: Lưu trữ posts (tin tức, doanh nghiệp)
- `wp_postmeta`: Lưu trữ custom fields (ngành hàng, khu vực, hình ảnh phụ)
- `wp_terms`: Lưu trữ terms của taxonomies
- `wp_term_taxonomy`: Định nghĩa taxonomies
- `wp_term_relationships`: Liên kết posts với terms

## Troubleshooting

### Theme không hiển thị đúng
- Kiểm tra file `style-gioi-thieu.css` có trong thư mục `assets/`
- Clear cache nếu đang dùng caching plugin

### Custom post types không hiển thị
- Vào **Settings > Permalinks** và click **Save Changes** để flush rewrite rules

### Menu không hiển thị
- Kiểm tra đã tạo menu và gán vào vị trí "Menu chính" chưa
