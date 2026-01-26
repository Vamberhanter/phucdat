# WordPress Theme - Cộng đồng Doanh nhân Trí tuệ Việt Nam

## Cài đặt

1. Copy thư mục `wordpress-theme` vào thư mục `wp-content/themes/` của WordPress
2. Đổi tên thư mục thành tên theme (ví dụ: `dnttvn-theme`)
3. Kích hoạt theme trong WordPress Admin: Appearance > Themes

## Tính năng

### Custom Post Types

1. **Tin tức** (`tin_tuc`)
   - Quản lý tin tức của cộng đồng
   - Có thể thêm từ WordPress Admin: Tin tức > Thêm mới

2. **Doanh nghiệp** (`doanh_nghiep`)
   - Quản lý danh sách doanh nghiệp
   - Có thể thêm từ WordPress Admin: Doanh nghiệp > Thêm mới
   - Custom fields: Ngành hàng, Khu vực, Hình ảnh phụ

### Custom Taxonomies

- **Ngành hàng**: Phân loại doanh nghiệp theo ngành hàng
- **Khu vực**: Phân loại doanh nghiệp theo khu vực

### Templates

- `index.php`: Trang chủ hiển thị tin tức
- `page-doanh-nghiep.php`: Trang danh sách doanh nghiệp với tìm kiếm
- `single-tin-tuc.php`: Trang chi tiết tin tức
- `single-doanh-nghiep.php`: Trang chi tiết doanh nghiệp

## Database

WordPress tự động tạo các bảng trong database:
- `wp_posts`: Lưu trữ posts (tin tức, doanh nghiệp)
- `wp_postmeta`: Lưu trữ custom fields
- `wp_terms`, `wp_term_taxonomy`, `wp_term_relationships`: Lưu trữ taxonomies

## Quản lý nội dung

### Thêm Tin tức:
1. Vào WordPress Admin
2. Chọn "Tin tức" > "Thêm mới"
3. Nhập tiêu đề, nội dung
4. Chọn hình ảnh đại diện (Featured Image)
5. Publish

### Thêm Doanh nghiệp:
1. Vào WordPress Admin
2. Chọn "Doanh nghiệp" > "Thêm mới"
3. Nhập tiêu đề, mô tả
4. Điền thông tin trong meta box:
   - Ngành hàng
   - Khu vực
   - Hình ảnh phụ
5. Chọn hình ảnh đại diện (Featured Image)
6. Chọn Ngành hàng và Khu vực từ taxonomies
7. Publish

## Tùy chỉnh

- Logo: Appearance > Customize > Site Identity
- Menu: Appearance > Menus
- Widgets: Appearance > Widgets
