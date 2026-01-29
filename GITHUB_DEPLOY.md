# Hướng dẫn Push lên GitHub và Deploy

## Các file đã được tạo:
- `index.html` - File chính để host trên GitHub Pages
- `style-gioi-thieu.css` - File CSS (cần đảm bảo đường dẫn đúng trong index.html)

## Các bước để push lên GitHub:

### 1. Khởi tạo Git repository (nếu chưa có):
```bash
cd c:\Projects\Website
git init
git add .
git commit -m "Initial commit: Website Cộng đồng Doanh nhân Trí tuệ Việt Nam"
```

### 2. Thêm remote repository:
```bash
git remote add origin https://github.com/backen-pixel/Website-demo.git
```

### 3. Push lên GitHub:
```bash
git branch -M main
git push -u origin main
```

### 4. Kích hoạt GitHub Pages:
1. Vào repository trên GitHub: https://github.com/backen-pixel/Website-demo
2. Vào Settings > Pages
3. Chọn Source: Deploy from a branch
4. Chọn Branch: main
5. Chọn Folder: / (root)
6. Click Save

### 5. Truy cập website:
Sau khi deploy, website sẽ có tại:
`https://backen-pixel.github.io/Website-demo/`

## Lưu ý:
- Đảm bảo file `style-gioi-thieu.css` cùng thư mục với `index.html`
- Nếu có thay đổi, commit và push lại:
  ```bash
  git add .
  git commit -m "Update website"
  git push
  ```
