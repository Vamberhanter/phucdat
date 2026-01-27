# HƯỚNG DẪN NÉN FILE (Nếu cần)

## ⚠️ LƯU Ý QUAN TRỌNG

**BẠN KHÔNG CẦN NÉN FILE!**

Chỉ cần copy thư mục `wordpress-theme` trực tiếp vào `wp-content\themes\` là được.

---

## ✅ CÁCH ĐƠN GIẢN NHẤT: Copy trực tiếp (KHUYÊN DÙNG)

**KHÔNG CẦN NÉN!** Chỉ cần:

1. Mở File Explorer
2. Đi tới thư mục `wordpress-theme` trong project của bạn
3. Copy toàn bộ thư mục này
4. Paste vào: `C:\Users\[Tên bạn]\Local Sites\dnttvn\app\public\wp-content\themes\`
5. Đổi tên thành: `dnttvn-theme`

**Xong! Không cần nén gì cả.**

---

## 📦 NẾU MUỐN NÉN ĐỂ DỄ COPY (Tùy chọn)

Nếu bạn muốn tạo file zip để dễ dàng copy hoặc chia sẻ, thì:

### Nén Theme để Copy

1. **Chọn thư mục cần nén**:
   - Thư mục: `wordpress-theme` (trong project của bạn)
   - Đường dẫn: `C:\Projects\Website\wordpress-theme\`

2. **Nén thành file zip**:
   - Click chuột phải vào thư mục `wordpress-theme`
   - Chọn **"Send to" > "Compressed (zipped) folder"**
   - Hoặc chọn **"Compress to ZIP"** (Windows 11)
   - File zip sẽ được tạo: `wordpress-theme.zip`

3. **Giải nén vào Local**:
   - Copy file `wordpress-theme.zip` vào Desktop hoặc vị trí dễ tìm
   - Đi tới: `C:\Users\[Tên bạn]\Local Sites\dnttvn\app\public\wp-content\themes\`
   - Paste file zip vào đây
   - Click chuột phải vào file zip → **"Extract All..."**
   - Đổi tên thư mục giải nén thành: `dnttvn-theme`

**Lưu ý**: Cách này phức tạp hơn, chỉ nên dùng nếu bạn muốn chia sẻ theme qua email/cloud.

---

## 🗄️ NẾU MUỐN EXPORT/BACKUP SITE (Sau này)

Nếu sau này bạn muốn export toàn bộ WordPress site để backup hoặc chuyển hosting, thì cần nén:

### Export Site từ Local

1. **Trong Local app**:
   - Click chuột phải vào site `dnttvn`
   - Chọn **"Export"**
   - Local sẽ tự động tạo file zip chứa:
     - Toàn bộ `wp-content` folder (themes, plugins, uploads)
     - Database SQL file
   - Lưu file zip vào Desktop hoặc vị trí bạn muốn

2. **File zip này có thể**:
   - Import vào Local khác
   - Import vào hosting khác
   - Backup để lưu trữ

### Cấu trúc file zip export từ Local:

```
dnttvn-export.zip
├── wp-content\
│   ├── themes\
│   │   └── dnttvn-theme\
│   ├── plugins\
│   └── uploads\
└── database.sql
```

---

## 📋 SO SÁNH CÁC CÁCH

### 1. Copy trực tiếp (KHUYÊN DÙNG)
```
✅ Đơn giản nhất
✅ Nhanh nhất
✅ Không cần nén/giải nén
❌ Chỉ dùng khi copy trong cùng máy
```

### 2. Nén theme rồi copy
```
✅ Dễ chia sẻ qua email/cloud
✅ File nhỏ hơn
❌ Phức tạp hơn (nén + giải nén)
❌ Không cần thiết nếu copy trong cùng máy
```

### 3. Export site từ Local
```
✅ Backup toàn bộ site
✅ Có cả database
✅ Có thể import vào hosting
❌ Chỉ dùng khi muốn backup/export site
```

---

## 🎯 KHUYẾN NGHỊ

### Để test local:
**→ Dùng cách 1: Copy trực tiếp** (không cần nén)

### Để chia sẻ theme:
**→ Dùng cách 2: Nén theme** (nếu cần gửi qua email/cloud)

### Để backup site:
**→ Dùng cách 3: Export từ Local** (sau khi đã setup xong)

---

## ✅ CHECKLIST

### Nếu copy trực tiếp (KHUYÊN DÙNG):
- [ ] Mở File Explorer
- [ ] Copy thư mục `wordpress-theme`
- [ ] Paste vào `wp-content\themes\`
- [ ] Đổi tên thành `dnttvn-theme`
- [ ] Xong!

### Nếu nén theme:
- [ ] Nén thư mục `wordpress-theme` thành zip
- [ ] Copy file zip vào `wp-content\themes\`
- [ ] Giải nén file zip
- [ ] Đổi tên thư mục thành `dnttvn-theme`
- [ ] Xóa file zip (nếu muốn)

---

## 📝 TÓM TẮT

**Để test local: KHÔNG CẦN NÉN!**

1. Copy thư mục `wordpress-theme` trực tiếp
2. Paste vào `wp-content\themes\`
3. Đổi tên thành `dnttvn-theme`
4. Kích hoạt theme trong Admin
5. Xong!

**Chỉ nén khi:**
- Muốn chia sẻ theme qua email/cloud
- Muốn backup/export toàn bộ site (dùng Export trong Local)

**Chúc bạn thành công! 🎉**
