# HƯỚNG DẪN LÊN LỊCH VÀ SẮP XẾP THỨ TỰ ĐĂNG BÀI

## ✅ TÍNH NĂNG ĐÃ CÓ

### 1. **Lên lịch đăng bài (Schedule Posts)**

WordPress đã có sẵn tính năng lên lịch đăng bài. Bạn có thể:

#### Cách sử dụng:

1. **Khi đăng Tin tức hoặc Doanh nghiệp:**
   - Ở sidebar bên phải → Tìm phần **"Publish"**
   - Click vào **"Edit"** bên cạnh "Publish immediately"
   - Chọn **ngày và giờ** muốn đăng bài
   - Click **"OK"**
   - Click **"Schedule"** (thay vì "Publish")

2. **Bài viết sẽ tự động đăng** vào đúng thời gian đã chọn!

---

### 2. **Sắp xếp thứ tự đăng bài (Menu Order)**

Bạn có thể đặt thứ tự hiển thị cho từng bài viết:

#### Cách sử dụng:

1. **Khi đăng/chỉnh sửa Tin tức hoặc Doanh nghiệp:**
   - Ở sidebar bên phải → Tab **"Page"** (nếu không thấy, click icon ⚙️ Settings)
   - Scroll xuống → Tìm **"Order"** (Thứ tự)
   - Nhập số thứ tự:
     - **0** = Mặc định (hiển thị theo ngày)
     - **1, 2, 3...** = Thứ tự ưu tiên (số nhỏ hơn hiển thị trước)
   - Click **"Update"** hoặc **"Publish"**

2. **Sắp xếp trong Admin List:**
   - Vào **Tin tức** → **Tất cả Tin tức** (hoặc **Doanh nghiệp** → **Tất cả Doanh nghiệp**)
   - Click vào cột **"Thứ tự"** để sắp xếp
   - Hoặc click vào tiêu đề bài viết → Sửa → Đổi số trong "Order"

3. **Sắp xếp trên Website:**
   - Trang chủ: Dropdown "Sắp xếp tin tức" → Chọn **"Thứ tự đăng bài"**
   - Trang Doanh nghiệp: Dropdown "Sắp xếp theo" → Chọn **"Thứ tự đăng bài"**

---

## 📋 VÍ DỤ SỬ DỤNG

### Ví dụ 1: Lên lịch đăng bài

**Tình huống:** Bạn muốn đăng tin tức vào 8h sáng ngày mai.

**Các bước:**
1. Tạo bài viết Tin tức mới
2. Điền đầy đủ nội dung
3. Ở sidebar → **Publish** → Click **"Edit"** bên cạnh "Publish immediately"
4. Chọn ngày mai, giờ 8:00
5. Click **"Schedule"**
6. ✅ Bài viết sẽ tự động đăng vào 8h sáng ngày mai!

---

### Ví dụ 2: Sắp xếp thứ tự hiển thị

**Tình huống:** Bạn muốn hiển thị 3 tin tức theo thứ tự:
- Tin 1: "Ra mắt Cộng đồng" (hiển thị đầu tiên)
- Tin 2: "Sự kiện tháng 1" (hiển thị thứ 2)
- Tin 3: "Thông báo mới" (hiển thị thứ 3)

**Các bước:**
1. Vào **Tin tức** → Click vào "Ra mắt Cộng đồng"
2. Sidebar → Tab **"Page"** → **"Order"** → Nhập **1**
3. Click **"Update"**
4. Làm tương tự:
   - "Sự kiện tháng 1" → Order: **2**
   - "Thông báo mới" → Order: **3**
5. Trên trang chủ → Chọn **"Thứ tự đăng bài"** trong dropdown sắp xếp
6. ✅ Tin tức sẽ hiển thị đúng thứ tự: 1 → 2 → 3

---

## 🎯 TÓM TẮT

### Lên lịch đăng bài:
- ✅ **Đã có sẵn** trong WordPress
- Vị trí: Sidebar → **Publish** → **Edit** (bên cạnh "Publish immediately")
- Chọn ngày/giờ → Click **"Schedule"**

### Sắp xếp thứ tự:
- ✅ **Đã thêm** tính năng Menu Order
- Vị trí: Sidebar → Tab **"Page"** → **"Order"**
- Nhập số thứ tự (0, 1, 2, 3...)
- Sắp xếp trên website: Chọn **"Thứ tự đăng bài"** trong dropdown

---

## 💡 LƯU Ý

1. **Lên lịch:** WordPress cần chạy cron job để tự động đăng bài. Nếu dùng Local, đảm bảo Local đang chạy.

2. **Thứ tự:** 
   - Số nhỏ hơn = hiển thị trước
   - Số 0 = mặc định (theo ngày)
   - Có thể dùng số âm nếu cần

3. **Kết hợp:** Có thể vừa lên lịch vừa đặt thứ tự!

---

**Chúc bạn sử dụng thành công! 🎉**
