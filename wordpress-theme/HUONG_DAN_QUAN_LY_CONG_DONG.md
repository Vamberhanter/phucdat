# HƯỚNG DẪN QUẢN LÝ CỘNG ĐỒNG

## 📝 CÁCH ĐĂNG BÀI CỘNG ĐỒNG

### Bước 1: Vào Trang Quản lý Cộng đồng

1. Đăng nhập WordPress Admin: `http://test.local/wp-admin`
2. Ở menu bên trái, tìm **"Cộng đồng"** (biểu tượng nhóm người)
3. Click **"Cộng đồng"** → **"Thêm mới"**

### Bước 2: Nhập Thông tin Bài viết

1. **Tiêu đề**: Nhập tiêu đề bài viết (ví dụ: "Điều lệ tổ chức hoạt động", "Quy trình gia nhập Cộng đồng")
 
2. **Nội dung**: 
   - Nhập nội dung khung chung trong editor (mở bài, nội dung tổng quát)
   - Có thể format text, thêm hình ảnh, links, v.v.

3. **Nội dung có cấu trúc (mục lớn / nội dung nhỏ)**:
   - Scroll xuống, tìm box **"Nội dung có cấu trúc"**
   - Mỗi mục gồm:
     - **Mục lớn**: tiêu đề (sẽ là phần mục in đậm trong bài)
     - **Nội dung nhỏ**: chi tiết cho từng mục
   - Dùng tốt cho các bài dài kiểu: Điều lệ, Quy trình, Hỏi đáp, v.v.
   - Có thể:
     - Nhấn **"+ Thêm mục mới"** để tách nội dung thành nhiều phần rõ ràng
     - Kéo icon **☰** để sắp xếp lại thứ tự mục
     - Xóa mục không cần dùng

4. **Mô tả ngắn** (Meta Box):\
   - Scroll xuống, tìm box **"Thông tin Bài Cộng đồng"**
   - Nhập mô tả ngắn gọn (hiển thị ở danh sách nếu không có Excerpt)
   - Hoặc sử dụng **"Excerpt"** ở sidebar bên phải

4. **Hình ảnh đại diện** (Tùy chọn):
   - Bên phải → **"Featured image"**
   - Click **"Set featured image"**
   - Upload hoặc chọn hình ảnh
   - Click **"Set featured image"**

5. **Đánh dấu Nổi bật** (Tùy chọn):
   - Trong box **"Thông tin Bài Cộng đồng"**
   - Tick vào checkbox **"Đánh dấu bài này là bài nổi bật"**
   - Bài nổi bật sẽ có class `highlight-item` trong sidebar

### Bước 3: Lên lịch đăng bài

1. Bên phải, tìm box **"Lên lịch đăng bài"**
2. Nhập **Ngày đăng** nếu muốn đặt lịch cụ thể
3. Hoặc sử dụng chức năng **"Publish"** của WordPress để đăng ngay hoặc schedule

### Bước 4: Sắp xếp thứ tự hiển thị

1. Bên phải, tìm **"Page Attributes"**
2. Nhập số vào **"Order"**:
   - **0** = hiển thị đầu tiên
   - **1, 2, 3...** = thứ tự tiếp theo
   - Số càng nhỏ càng ưu tiên hiển thị trước

### Bước 5: Xuất bản

1. Click nút **"Publish"** (màu xanh) ở góc trên bên phải
2. Bài viết sẽ hiển thị ngay trên:
   - **Sidebar bên trái** của trang chủ (index)
   - **Trang Cộng đồng** (`/cong-dong/`)

---

## 📋 QUẢN LÝ DANH SÁCH BÀI CỘNG ĐỒNG

### Xem Tất cả Bài

1. **Cộng đồng** → **"Tất cả bài"**
2. Bạn sẽ thấy danh sách với các cột:
   - **Title**: Tiêu đề bài
   - **Thứ tự**: Số order (menu_order)
   - **Nổi bật**: Có dấu ⭐ nếu là bài nổi bật
   - **Date**: Ngày đăng

3. **Sắp xếp**: Click vào tên cột để sắp xếp
   - Click **"Thứ tự"** → sắp xếp theo order
   - Click **"Nổi bật"** → bài nổi bật lên đầu
   - Click **"Date"** → sắp xếp theo ngày

### Chỉnh sửa Bài

1. Hover vào bài muốn sửa
2. Click **"Edit"** hoặc click vào tiêu đề
3. Chỉnh sửa thông tin
4. Click **"Update"** để lưu

### Xóa Bài

1. Hover vào bài muốn xóa
2. Click **"Trash"**
3. Bài sẽ chuyển vào thùng rác

---

## 🎯 HIỂN THỊ TRÊN WEBSITE

### Xem Trang Cộng đồng

- URL: `http://test.local/cong-dong/`
- Trang này được tự động tạo khi activate theme
- Template: `page-cong-dong.php`

### Cách Hoạt Động

1. **Sidebar bên trái (index.php)**:
   - Hiển thị danh sách tất cả bài Cộng đồng
   - Sắp xếp theo thứ tự (menu_order) hoặc ngày
   - Click vào bài → chuyển đến trang Cộng đồng với nội dung chi tiết

2. **Trang Cộng đồng (page-cong-dong.php)**:
   - **Sidebar bên trái**: Danh sách bài (giống index)
   - **Center**: Hiển thị nội dung chi tiết bài được chọn
   - **Sorting**: Dropdown để sắp xếp theo thứ tự, ngày, tiêu đề, nổi bật

3. **URL Structure**:
   - Trang chính: `/cong-dong/`
   - Xem bài cụ thể: `/cong-dong/?post_id=123`

---

## 📊 SẮP XẾP VÀ LỌC

### Sắp xếp trên Frontend

Người dùng có thể chọn cách sắp xếp:
- **Thứ tự đăng bài**: Theo menu_order
- **Mới nhất**: Bài mới nhất trước
- **Cũ nhất**: Bài cũ nhất trước
- **Tiêu đề A-Z**: Sắp xếp theo alphabet
- **Tiêu đề Z-A**: Sắp xếp ngược
- **Bài nổi bật trước**: Ưu tiên bài có đánh dấu nổi bật

### Sắp xếp trong Admin

- Click vào tên cột để sắp xếp
- **Thứ tự** và **Nổi bật** columns đều có thể sort

---

## ✅ CHECKLIST ĐĂNG BÀI CỘNG ĐỒNG

- [ ] Tiêu đề đã nhập (rõ ràng, ngắn gọn)
- [ ] Nội dung đã nhập (đầy đủ, có format)
- [ ] Mô tả ngắn hoặc Excerpt đã nhập
- [ ] Hình ảnh đại diện đã chọn (nếu có)
- [ ] Đánh dấu Nổi bật (nếu cần)
- [ ] Order number đã đặt (để sắp xếp thứ tự)
- [ ] Ngày đăng đã chọn (nếu cần lên lịch)
- [ ] Click "Publish"

---

## 🔍 KIỂM TRA BÀI ĐÃ ĐĂNG

### Kiểm tra trên Trang chủ

1. Vào trang chủ: `http://test.local/`
2. Xem **Sidebar bên trái** "Về Cộng đồng DNTTVN"
3. Bài vừa đăng sẽ xuất hiện trong danh sách
4. Click vào bài → chuyển đến trang Cộng đồng

### Kiểm tra trên Trang Cộng đồng

1. Vào: `http://test.local/cong-dong/`
2. Xem **Sidebar bên trái**: Có danh sách bài
3. Xem **Center**: Nội dung bài được chọn hoặc danh sách tất cả bài
4. Test sorting dropdown

---

## 🚀 GỢI Ý NỘI DUNG

Một số ý tưởng cho bài viết Cộng đồng:

1. **Điều lệ tổ chức hoạt động** (Order: 0)
2. **Danh sách thành viên sáng lập** (Order: 1)
3. **Cấu trúc Cộng đồng** (Order: 2)
4. **Danh sách Lãnh đạo điều hành** (Order: 3)
5. **Tìm hiểu trở thành thành viên mới** (Order: 4, Nổi bật: ✓)
6. **Giá trị nhận được của thành viên** (Order: 5)
7. **Quy trình gia nhập Cộng đồng** (Order: 6)
8. **Hỏi đáp về Cộng đồng** (Order: 7)

---

## 📝 TÓM TẮT

**Đăng Bài Cộng đồng:**
1. **Cộng đồng** → **Thêm mới**
2. Nhập tiêu đề, nội dung, mô tả ngắn
3. Chọn Featured Image (nếu có)
4. Đặt Order number (Page Attributes)
5. Đánh dấu Nổi bật (nếu cần)
6. **Publish**

**Xem Bài:**
- Trang chủ: Sidebar bên trái
- Trang Cộng đồng: `/cong-dong/`

**Xong! Bài sẽ hiển thị ngay trên website! 🎉**
