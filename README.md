# Trang WordPress - Cộng đồng Doanh nhân Trí tuệ Việt Nam

## Mô tả
Thiết kế trang WordPress theo wireframe với cấu trúc 3 cột, header với logo/banner/kênh liên kết, và footer.

## Cấu trúc Files

### 1. `page-template.php`
- Template WordPress đầy đủ với PHP hooks
- Sử dụng trong WordPress theme
- Có thể đặt trong thư mục theme và chọn làm Page Template

### 2. `page-standalone.html`
- File HTML độc lập
- Có thể sử dụng trực tiếp hoặc copy vào WordPress page builder
- Cần file `style.css` để hiển thị đúng

### 3. `style.css`
- File CSS riêng biệt
- Responsive design
- Có thể import vào WordPress theme

## Cách sử dụng

### Cách 1: Sử dụng như WordPress Template
1. Copy `page-template.php` vào thư mục theme của bạn (ví dụ: `wp-content/themes/your-theme/`)
2. Trong WordPress Admin, tạo Page mới
3. Chọn Template: "Trang Cộng đồng DNTTVN"
4. Publish page

### Cách 2: Sử dụng với Page Builder (Elementor, Gutenberg, etc.)
1. Mở `page-standalone.html` và copy nội dung HTML
2. Tạo page mới trong WordPress
3. Sử dụng Custom HTML block hoặc Code widget
4. Paste HTML vào
5. Thêm CSS từ `style.css` vào Customizer > Additional CSS

### Cách 3: Tích hợp vào Theme
1. Copy `style.css` vào theme folder
2. Enqueue CSS trong `functions.php`:
```php
function enqueue_community_styles() {
    wp_enqueue_style('community-style', get_template_directory_uri() . '/style.css');
}
add_action('wp_enqueue_scripts', 'enqueue_community_styles');
```

## Cấu trúc Layout

### Header
- Logo (trái)
- Banner (giữa)
- Kênh liên kết: Facebook, TikTok, Zalo, YouTube (phải)

### Navigation
- Menu ngang với các mục: Trang chủ, Giới thiệu, Tin tức, Thành viên, Sự kiện, Liên hệ

### Main Content (3 cột)
1. **Cột trái**: Về Cộng đồng DNTTVN
   - Điều lệ tổ chức hoạt động
   - Danh sách thành viên sáng lập
   - Cấu trúc Cộng đồng
   - Danh sách Lãnh đạo điều hành
   - Tìm hiểu trở thành thành viên mới (highlighted)
   - Giá trị nhận được của thành viên
   - Quy trình gia nhập Cộng đồng
   - Hỏi đáp về Cộng đồng

2. **Cột giữa**: Tin tức Cộng đồng
   - 4 tin tức mới nhất
   - Có ngày đăng và mô tả ngắn

3. **Cột phải**: Website liên kết
   - Danh sách Doanh nghiệp
   - Cộng đồng

### Footer
- Thông tin chân trang
- Copyright

## Tùy chỉnh

### Thay đổi màu sắc
Chỉnh sửa trong `style.css`:
- Header gradient: `.banner-section` (dòng ~70)
- Column header: `.column-header` (dòng ~130)
- Link hover: `#667eea`

### Thêm nội dung
- Cập nhật links trong HTML
- Thêm tin tức trong cột giữa
- Thêm doanh nghiệp trong cột phải

## Responsive
- Desktop: 3 cột ngang
- Tablet (< 968px): 1 cột dọc
- Mobile: Header stack, menu dọc

## Lưu ý
- Thay thế placeholder links (#) bằng links thực tế
- Thêm logo và banner thực tế
- Cập nhật thông tin footer (địa chỉ, email, hotline)
- Tích hợp với WordPress menu system nếu cần
