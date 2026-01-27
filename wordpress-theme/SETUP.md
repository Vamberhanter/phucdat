# HƯỚNG DẪN SETUP 2 TRANG

## 📋 TRANG 1: Trang chủ (Tin tức)

**Tự động hiển thị** khi vào trang chủ website.

**URL**: `http://test.local/` (hoặc domain của bạn)

**Template**: `index.php` - Hiển thị danh sách tin tức từ Custom Post Type "Tin tức"

---

## 📋 TRANG 2: Trang Doanh nghiệp

**Cần tạo Page trong WordPress Admin:**

### Bước 1: Tạo Page "Danh sách Doanh nghiệp"

1. Vào **WordPress Admin** → **Pages** → **Add New**
2. **Tiêu đề**: "Danh sách Doanh nghiệp"
3. Bên phải, tìm **Page Attributes** → **Template**
4. Chọn: **"Trang Doanh nghiệp"**
5. Bên phải, tìm **Permalink** (hoặc click "Edit" bên cạnh URL)
6. Đặt slug: `page-doanh-nghiep` (hoặc `danh-sach-doanh-nghiep`)
7. Click **"Publish"**

### Bước 2: Truy cập Trang Doanh nghiệp

**URL**: `http://test.local/page-doanh-nghiep/` (hoặc slug bạn đã đặt)

**Template**: `page-doanh-nghiep.php` - Hiển thị danh sách doanh nghiệp với tìm kiếm

---

## ✅ KIỂM TRA

### Trang 1 (Trang chủ):
- ✅ Vào `http://test.local/` → Thấy danh sách tin tức
- ✅ Có sidebar trái: "Về Cộng đồng DNTTVN"
- ✅ Có sidebar phải: "Website liên kết" với link đến trang doanh nghiệp

### Trang 2 (Trang Doanh nghiệp):
- ✅ Vào `http://test.local/page-doanh-nghiep/` → Thấy trang doanh nghiệp
- ✅ Có form tìm kiếm: Tên, Khu vực, Ngành hàng
- ✅ Có danh sách business cards
- ✅ Có sidebar phải: "Theo ngành hàng" với ad blocks

---

## 🔗 LINK GIỮA 2 TRANG

Trong **Trang 1** (sidebar phải):
- Có link "Danh sách Doanh nghiệp" → Click vào sẽ chuyển sang **Trang 2**

Trong menu navigation:
- Có thể thêm link đến cả 2 trang

---

## 🚨 NẾU KHÔNG THẤY TRANG 2

### Vấn đề: Không tìm thấy trang doanh nghiệp
**Giải pháp**:
1. Kiểm tra đã tạo Page chưa: **Pages** → **All Pages**
2. Kiểm tra đã chọn Template "Trang Doanh nghiệp" chưa
3. Kiểm tra slug/permalink đúng chưa
4. Vào **Settings** → **Permalinks** → Click **Save Changes** (quan trọng!)

### Vấn đề: Trang hiển thị sai template
**Giải pháp**:
1. Vào **Pages** → Click vào page "Danh sách Doanh nghiệp"
2. Bên phải → **Page Attributes** → **Template**
3. Chọn lại **"Trang Doanh nghiệp"**
4. Click **Update**

### Vấn đề: Link không hoạt động
**Giải pháp**:
1. Vào **Settings** → **Permalinks**
2. Chọn **"Post name"**
3. Click **Save Changes**
4. Clear cache (nếu có)

---

## 📝 TÓM TẮT

**Trang 1 (Trang chủ)**: Tự động hiển thị - không cần tạo gì

**Trang 2 (Trang Doanh nghiệp)**: 
1. Tạo Page mới
2. Chọn Template "Trang Doanh nghiệp"
3. Đặt slug: `page-doanh-nghiep`
4. Publish

**Xong!**
