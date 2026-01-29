# HƯỚNG DẪN CẤU HÌNH SOCIAL MEDIA LINKS

## 📱 QUẢN LÝ LIÊN KẾT SOCIAL MEDIA

WordPress admin đã được tích hợp trang quản lý các liên kết social media hiển thị trên header website.

---

## 🚀 CÁCH SỬ DỤNG

### Bước 1: Truy cập Admin Page

1. Đăng nhập WordPress Admin: `http://test.local/wp-admin`
2. Ở menu bên trái, tìm **"Social Media"** (biểu tượng chia sẻ)
3. Click vào **"Social Media"**

### Bước 2: Cấu hình URLs

Bạn sẽ thấy form với 4 trường:

#### 🔵 Facebook URL
- **Placeholder**: `https://facebook.com/yourpage`
- **Ví dụ**: `https://facebook.com/congdongdnttvn`
- Nhập URL trang Facebook của cộng đồng/doanh nghiệp

#### ⚫ TikTok URL
- **Placeholder**: `https://tiktok.com/@yourpage`
- **Ví dụ**: `https://tiktok.com/@congdongdnttvn`
- Nhập URL trang TikTok

#### 🔵 Zalo URL
- **Placeholder**: `https://zalo.me/yourpage`
- **Ví dụ**: `https://zalo.me/0912345678`
- Nhập URL Zalo (có thể là zalo.me hoặc số điện thoại)

#### 🔴 YouTube URL
- **Placeholder**: `https://youtube.com/@yourchannel`
- **Ví dụ**: `https://youtube.com/@congdongdnttvn`
- Nhập URL kênh YouTube

### Bước 3: Lưu Cài đặt

1. Nhập URLs cho các platform muốn hiển thị
2. **Để trống** nếu không muốn hiển thị nút đó
3. Click nút **"Lưu cài đặt"** màu xanh
4. Thông báo "Cài đặt đã được lưu thành công!" sẽ hiện ra

### Bước 4: Kiểm tra

1. **Preview trong Admin**:
   - Scroll xuống phần "Preview"
   - Xem các link đã cấu hình
   - Click vào link để test

2. **Kiểm tra Frontend**:
   - Truy cập trang chủ: `http://test.local/`
   - Xem phần **"KÊNH LIÊN KẾT"** ở header
   - Các nút đã cấu hình sẽ hiển thị
   - Các nút chưa cấu hình sẽ KHÔNG hiển thị

---

## ✅ VALIDATION & QUY TẮC

### URL Format
- **Phải bắt đầu với**: `http://` hoặc `https://`
- **URL hợp lệ**: `https://facebook.com/page`
- **URL không hợp lệ**: `facebook.com/page` (thiếu https://)

### Conditional Display
| Trạng thái | Hiển thị trên website |
|------------|----------------------|
| ✅ Có URL   | Nút hiển thị, click mở link |
| ❌ Trống    | Nút KHÔNG hiển thị |
| ⚠️ URL lỗi | Không lưu được, hiện lỗi |

---

## 📋 VÍ DỤ CẤU HÌNH

### Cấu hình đầy đủ 4 platforms:

```
Facebook:  https://facebook.com/congdongdoanhnhan
TikTok:    https://tiktok.com/@dnttvn
Zalo:      https://zalo.me/0912345678
YouTube:   https://youtube.com/@channeldnttvn
```

**Kết quả**: Tất cả 4 nút hiển thị trên header

### Cấu hình chỉ 2 platforms:

```
Facebook:  https://facebook.com/congdongdoanhnhan
TikTok:    (để trống)
Zalo:      (để trống)
YouTube:   https://youtube.com/@channeldnttvn
```

**Kết quả**: Chỉ 2 nút Facebook và YouTube hiển thị

---

## 🎯 TESTING

### Test Save/Load
1. Nhập URLs vào form
2. Click "Lưu cài đặt"
3. Reload trang admin
4. Verify URLs vẫn còn trong form

### Test Frontend Display
1. Vào trang chủ
2. Xem header phần "KÊNH LIÊN KẾT"
3. Verify các nút hiển thị đúng
4. Click vào từng nút
5. Verify mở đúng link trong tab mới

### Test Conditional Display
1. Xóa 1 URL trong admin (để trống)
2. Lưu cài đặt
3. Reload trang chủ
4. Verify nút đó không còn hiển thị

---

## 🔧 TROUBLESHOOTING

### Lỗi: "URL không hợp lệ"
**Nguyên nhân**: URL thiếu `https://` hoặc format sai

**Giải pháp**:
- Đảm bảo URL bắt đầu với `https://`
- Ví dụ đúng: `https://facebook.com/page`
- Ví dụ sai: `facebook.com/page`

### Nút không hiển thị trên website
**Nguyên nhân 1**: URL chưa được cấu hình (trống)

**Giải pháp**: Vào admin, nhập URL và lưu

**Nguyên nhân 2**: Cache browser

**Giải pháp**: Ctrl+F5 để hard refresh

### URL đã lưu nhưng mất khi reload
**Nguyên nhân**: Validation failed (URL không hợp lệ)

**Giải pháp**: Nhập lại URL đúng format

---

## 💡 TIPS

1. **Copy URL chính xác**: Copy URL từ thanh địa chỉ browser khi đang ở trang social
2. **Test links sau khi lưu**: Luôn click vào Preview links để test
3. **Không bắt buộc cấu hình tất cả**: Chỉ cấu hình platforms đang sử dụng
4. **Update dễ dàng**: Có thể thay đổi URLs bất cứ lúc nào
5. **Mobile friendly**: Links tự động responsive trên mobile

---

## 📊 TECHNICAL INFO

### Lưu trữ dữ liệu
- **Table**: `wp_options`
- **Option Names**:
  - `dnttvn_facebook_url`
  - `dnttvn_tiktok_url`
  - `dnttvn_zalo_url`
  - `dnttvn_youtube_url`

### Files liên quan
- **Admin Page**: `functions.php` (dòng 1478+)
- **Frontend Display**: `header.php` (dòng 26-88)
- **API**: WordPress Settings API

---

## 🎨 PLATFORM GUIDELINES

### Facebook
- **URL Format**: `https://facebook.com/pagename` hoặc `https://fb.com/pagename`
- **Tìm Page URL**: Vào trang Facebook → Copy URL từ browser

### TikTok
- **URL Format**: `https://tiktok.com/@username`
- **Lưu ý**: Phải có `@` trước username

### Zalo
- **URL Format**: 
  - `https://zalo.me/0912345678` (số điện thoại)
  - `https://zalo.me/pageid` (page ID)
- **Tìm Zalo Link**: Vào Zalo → Cài đặt → Liên kết

### YouTube
- **URL Format**: 
  - `https://youtube.com/@channelname` (channel handle)
  - `https://youtube.com/channel/UCxxxxxx` (channel ID)
- **Tìm Channel URL**: Vào kênh YouTube → Copy URL

---

## ✅ CHECKLIST

Sau khi cấu hình, đảm bảo:

- [ ] Đã nhập URLs cho platforms muốn sử dụng
- [ ] URLs bắt đầu với https://
- [ ] Đã click "Lưu cài đặt"
- [ ] Thấy thông báo "Cài đặt đã được lưu thành công!"
- [ ] Preview trong admin hiển thị đúng links
- [ ] Frontend header hiển thị đúng số lượng nút
- [ ] Click vào từng nút mở đúng trang
- [ ] Các nút không cấu hình không hiển thị

---

## 📞 HỖ TRỢ

Nếu gặp vấn đề:
1. Kiểm tra format URL
2. Clear browser cache (Ctrl+F5)
3. Kiểm tra Preview trong admin
4. Test trên incognito mode

**Done! Social media links đã sẵn sàng! 🎉**
