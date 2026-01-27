# HƯỚNG DẪN CHỌN TEMPLATE TRONG WORDPRESS BLOCK EDITOR

## 🎯 VẤN ĐỀ

Trong WordPress Block Editor (Gutenberg), không còn "Page Attributes" như Classic Editor. Các cài đặt nằm ở sidebar bên phải.

---

## ✅ CÁCH CHỌN TEMPLATE "Trang Doanh nghiệp"

### Bước 1: Mở Page Editor

1. Vào **Pages** → **Add New** (hoặc Edit page hiện có)
2. Bạn sẽ thấy WordPress Block Editor

### Bước 2: Tìm Tab "Page" ở Sidebar Phải

1. **Nhìn vào sidebar bên phải** (không phải bên trái)
2. Ở phía trên sidebar, bạn sẽ thấy 2 tab:
   - **"Block"** (mặc định)
   - **"Page"** ← **CLICK VÀO TAB NÀY**

### Bước 3: Tìm "Template" trong Tab "Page"

1. Sau khi click tab **"Page"**, scroll xuống
2. Bạn sẽ thấy các mục:
   - Status
   - Publish
   - Slug
   - Author
   - **Template** ← **ĐÂY LÀ NƠI CẦN CHỌN**
   - Discussion
   - Parent

### Bước 4: Chọn Template "Trang Doanh nghiệp"

1. Tìm dòng **"Template"**
2. Bạn sẽ thấy: **"Default template"** (hoặc tên template hiện tại)
3. **Click vào "Default template"** (hoặc tên template hiện tại)
4. Một dropdown menu sẽ hiện ra với các template:
   - Default template
   - **Trang Doanh nghiệp** ← **CHỌN CÁI NÀY**
5. Click **"Trang Doanh nghiệp"**

### Bước 5: Lưu Page

1. Click nút **"Publish"** (hoặc **"Update"** nếu đang edit)
2. Xong!

---

## 📸 VỊ TRÍ TRONG EDITOR

```
┌─────────────────────────────────────┐
│  WordPress Editor                   │
│                                     │
│  ┌─────────────┐  ┌──────────────┐│
│  │             │  │ [Block] [Page]││ ← Click "Page"
│  │   Content   │  │              ││
│  │   Area      │  │  Template:   ││ ← Tìm dòng này
│  │             │  │  Default     ││ ← Click vào đây
│  │             │  │  template    ││
│  │             │  │              ││
│  └─────────────┘  └──────────────┘│
│                    Sidebar Phải    │
└─────────────────────────────────────┘
```

---

## 🔍 NẾU KHÔNG THẤY TAB "Page"

### Cách 1: Click vào icon Settings
1. Ở góc trên bên phải editor, tìm icon **⚙️ Settings** (bánh răng)
2. Click vào icon đó
3. Sidebar sẽ hiện ra với tab "Block" và "Page"

### Cách 2: Dùng phím tắt
1. Nhấn **Ctrl + Shift + ,** (dấu phẩy)
2. Sidebar sẽ hiện ra

### Cách 3: Click vào 3 chấm (Options)
1. Ở góc trên bên phải, click vào **⋮** (3 chấm dọc)
2. Chọn **"Preferences"** hoặc **"Editor"**
3. Đảm bảo sidebar được bật

---

## 🚨 NẾU KHÔNG THẤY TEMPLATE "Trang Doanh nghiệp"

### Kiểm tra 1: Theme đã được kích hoạt chưa?
- Vào **Appearance** → **Themes**
- Đảm bảo theme "Cộng đồng Doanh nhân Trí tuệ Việt Nam" đã được **Activate**

### Kiểm tra 2: File template có tồn tại không?
- Kiểm tra file `page-doanh-nghiep.php` có trong thư mục theme không
- Đường dẫn: `wp-content/themes/test-theme/page-doanh-nghiep.php`

### Kiểm tra 3: Template header có đúng không?
- Mở file `page-doanh-nghiep.php`
- Dòng đầu phải có: `Template Name: Trang Doanh nghiệp`

### Kiểm tra 4: Refresh trang
- Nhấn **F5** hoặc **Ctrl + F5** để reload
- Thử lại

---

## 📝 TÓM TẮT

**Trong Block Editor:**

1. Sidebar bên phải → Tab **"Page"**
2. Scroll xuống → Tìm **"Template"**
3. Click vào **"Default template"** (hoặc tên hiện tại)
4. Chọn **"Trang Doanh nghiệp"**
5. **Publish/Update**

**Không còn "Page Attributes" nữa - tất cả nằm trong tab "Page"!**

---

## ✅ SAU KHI CHỌN TEMPLATE

1. Đặt **Slug**: `page-doanh-nghiep` (trong tab "Page" → Slug)
2. Click **"Publish"**
3. Truy cập: `http://test.local/page-doanh-nghiep/`
4. Bạn sẽ thấy trang doanh nghiệp với form tìm kiếm!

**Chúc bạn thành công! 🎉**
