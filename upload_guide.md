# Hướng dẫn Upload Code với UTF-8 Encoding

## Phương pháp 1: DirectAdmin File Manager
1. Mở DirectAdmin → File Manager
2. Navigate đến thư mục cần edit
3. Click file cần sửa
4. Tìm dropdown "Encoding" hoặc "Character Set"
5. Chọn "UTF-8" 
6. Paste code và Save

## Phương pháp 2: Upload file từ VS Code
1. Trong VS Code, Save file với UTF-8 (không BOM)
   - Kiểm tra góc phải dưới: phải hiển thị "UTF-8"
   - Nếu không phải, click vào encoding và chọn "Save with Encoding" → "UTF-8"
2. Sử dụng FileZilla/WinSCP upload file lên server
3. Hoặc dùng DirectAdmin File Manager → Upload Files

## Phương pháp 3: Sử dụng SSH (nếu có)
```bash
# Tạo file với encoding UTF-8
nano filename.php
# Paste code và save với Ctrl+X, Y, Enter

# Kiểm tra encoding
file -i filename.php
# Kết quả mong muốn: charset=utf-8
```

## Phương pháp 4: Fix encoding cho file hiện tại
```bash
# Convert file to UTF-8
iconv -f iso-8859-1 -t utf-8 filename.php > filename_utf8.php
mv filename_utf8.php filename.php
```

## Lưu ý quan trọng:
- Luôn backup file trước khi sửa
- Đảm bảo tất cả file PHP đều là UTF-8
- Tránh BOM (Byte Order Mark) trong file PHP
- Kiểm tra file sau khi upload xem tiếng Việt có hiển thị đúng không