# CÁCH TÌM ĐƯỜNG DẪN THƯ MỤC WORDPRESS TRONG LOCAL

## 🎯 ĐƯỜNG DẪN CẦN TÌM

```
C:\Users\[Tên bạn]\Local Sites\dnttvn\app\public\wp-content\themes\
```

**Ví dụ thực tế:**
```
C:\Users\Admin\Local Sites\dnttvn\app\public\wp-content\themes\
C:\Users\Nguyen Van A\Local Sites\dnttvn\app\public\wp-content\themes\
```

---

## ✅ CÁCH 1: Qua Local App (Dễ nhất)

### Bước 1: Mở Local App
1. Mở ứng dụng **Local** (từ Desktop hoặc Start Menu)
2. Bạn sẽ thấy danh sách các site WordPress

### Bước 2: Tìm Site của bạn
1. Tìm site tên **`dnttvn`** (hoặc tên site bạn đã tạo)
2. Click vào site đó để chọn

### Bước 3: Xem đường dẫn (Path)
1. Click vào tab **"Overview"** (ở phía trên)
2. Tìm phần **"Path"** hoặc **"Site Path"**
3. Bạn sẽ thấy đường dẫn như: `C:\Users\Admin\Local Sites\dnttvn\app\public\`
4. **Copy đường dẫn này**

### Bước 4: Mở File Explorer
1. Mở **File Explorer** (Windows + E)
2. Paste đường dẫn vào thanh địa chỉ (Address bar) ở trên cùng
3. Nhấn **Enter**
4. Bạn sẽ thấy thư mục `public\`

### Bước 5: Đi tới thư mục themes
1. Double-click vào thư mục **`wp-content`**
2. Double-click vào thư mục **`themes`**
3. Đây chính là nơi bạn cần paste theme!

**Đường dẫn cuối cùng:**
```
C:\Users\[Tên bạn]\Local Sites\dnttvn\app\public\wp-content\themes\
```

---

## ✅ CÁCH 2: Qua "Reveal in Finder" / "Open in Explorer"

### Bước 1: Mở Local App
1. Mở **Local**
2. Click vào site **`dnttvn`**

### Bước 2: Mở thư mục site
1. Click tab **"Overview"**
2. Tìm nút **"Reveal in Finder"** (Mac) hoặc **"Open in Explorer"** (Windows)
3. Click vào nút đó
4. File Explorer sẽ tự động mở tại thư mục `public\`

### Bước 3: Đi tới themes
1. Double-click **`wp-content`**
2. Double-click **`themes`**
3. Đây là nơi cần paste theme!

---

## ✅ CÁCH 3: Qua "Open Site Shell"

### Bước 1: Mở Site Shell
1. Trong Local, click vào site **`dnttvn`**
2. Click tab **"Overview"**
3. Click nút **"Open Site Shell"**
4. Một cửa sổ terminal/command prompt sẽ mở

### Bước 2: Xem đường dẫn hiện tại
1. Terminal sẽ tự động mở tại thư mục WordPress
2. Gõ lệnh: `cd` (và nhấn Enter)
3. Terminal sẽ hiển thị đường dẫn hiện tại
4. Copy đường dẫn này

### Bước 3: Mở File Explorer
1. Mở File Explorer
2. Paste đường dẫn vào thanh địa chỉ
3. Đi tới: `wp-content\themes\`

---

## ✅ CÁCH 4: Tìm thủ công (Nếu không thấy trong Local)

### Bước 1: Tìm thư mục Local Sites
1. Mở **File Explorer**
2. Đi tới: `C:\Users\`
3. Tìm thư mục có tên là tên user của bạn (ví dụ: `Admin`, `Nguyen Van A`, v.v.)
4. Double-click vào thư mục đó

### Bước 2: Tìm thư mục Local Sites
1. Trong thư mục user, tìm thư mục **`Local Sites`**
2. Double-click vào đó

### Bước 3: Tìm site dnttvn
1. Tìm thư mục **`dnttvn`** (hoặc tên site bạn đã tạo)
2. Double-click vào đó

### Bước 4: Đi tới themes
1. Double-click **`app`**
2. Double-click **`public`**
3. Double-click **`wp-content`**
4. Double-click **`themes`**
5. Đây là nơi cần paste theme!

**Đường dẫn đầy đủ:**
```
C:\Users\[Tên bạn]\Local Sites\dnttvn\app\public\wp-content\themes\
```

---

## 🔍 LÀM SAO BIẾT TÊN USER CỦA BẠN?

### Cách 1: Xem trong Local
- Trong Local app → Tab "Overview" → Xem "Path"
- Đường dẫn sẽ có tên user của bạn

### Cách 2: Xem trong Windows
1. Click vào **Start Menu**
2. Click vào **icon user** ở góc trên bên trái
3. Tên hiển thị chính là tên user của bạn

### Cách 3: Dùng lệnh
1. Mở **Command Prompt** (Windows + R → gõ `cmd`)
2. Gõ lệnh: `echo %USERNAME%`
3. Nhấn Enter
4. Tên hiển thị chính là tên user

---

## 📋 VÍ DỤ THỰC TẾ

### Nếu tên user của bạn là "Admin":
```
C:\Users\Admin\Local Sites\dnttvn\app\public\wp-content\themes\
```

### Nếu tên user của bạn là "Nguyen Van A":
```
C:\Users\Nguyen Van A\Local Sites\dnttvn\app\public\wp-content\themes\
```

### Nếu tên user của bạn là "John":
```
C:\Users\John\Local Sites\dnttvn\app\public\wp-content\themes\
```

---

## ✅ CHECKLIST

- [ ] Đã mở Local app
- [ ] Đã tìm thấy site `dnttvn`
- [ ] Đã xem "Path" trong tab "Overview"
- [ ] Đã copy đường dẫn
- [ ] Đã mở File Explorer
- [ ] Đã paste đường dẫn vào thanh địa chỉ
- [ ] Đã đi tới `wp-content\themes\`
- [ ] Đã sẵn sàng paste theme!

---

## 🚨 NẾU KHÔNG TÌM THẤY

### Không thấy thư mục "Local Sites":
- **Nguyên nhân**: Bạn chưa tạo site WordPress trong Local
- **Giải pháp**: Tạo site mới trong Local (xem file `HUONG_DAN_TEST_LOCAL.md`)

### Không thấy thư mục "dnttvn":
- **Nguyên nhân**: Site có tên khác
- **Giải pháp**: Tìm thư mục có tên là tên site bạn đã tạo trong Local

### Không thấy thư mục "wp-content":
- **Nguyên nhân**: WordPress chưa cài đặt xong
- **Giải pháp**: Chờ Local hoàn tất cài đặt (2-5 phút), kiểm tra site có status "Running"

---

## 📝 TÓM TẮT

**Cách nhanh nhất:**

1. Mở **Local app**
2. Click vào site **`dnttvn`**
3. Click tab **"Overview"**
4. Xem **"Path"** → Copy đường dẫn
5. Mở **File Explorer** → Paste đường dẫn
6. Đi tới: `wp-content\themes\`
7. Paste theme vào đây!

**Hoặc:**

1. Trong Local → Click site `dnttvn` → Tab "Overview"
2. Click **"Open in Explorer"**
3. Đi tới: `wp-content\themes\`
4. Paste theme vào đây!

**Chúc bạn thành công! 🎉**
