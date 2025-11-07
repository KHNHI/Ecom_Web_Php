# #!/bin/bash

# echo "========================================"
# echo " CÀI ĐẶT PHPMAILER CHO ECOM WEBSITE"
# echo "========================================"
# echo ""

# # Check if Composer exists
# if ! command -v composer &> /dev/null; then
#     echo "[ERROR] Composer chưa được cài đặt!"
#     echo ""
#     echo "Vui lòng cài đặt Composer từ: https://getcomposer.org/download/"
#     echo ""
#     exit 1
# fi

# echo "[OK] Đã tìm thấy Composer"
# echo ""

# # Run composer install
# echo "Đang cài đặt PHPMailer..."
# echo ""
# composer install

# if [ $? -eq 0 ]; then
#     echo ""
#     echo "========================================"
#     echo " CÀI ĐẶT THÀNH CÔNG!"
#     echo "========================================"
#     echo ""
#     echo "Thư mục vendor/ đã được tạo"
#     echo "PHPMailer đã được cài đặt"
#     echo ""
#     echo "Bạn có thể reload lại trang admin orders bây giờ!"
#     echo ""
# else
#     echo ""
#     echo "========================================"
#     echo " CÀI ĐẶT THẤT BẠI!"
#     echo "========================================"
#     echo ""
#     echo "Vui lòng chạy lệnh thủ công:"
#     echo "  composer install"
#     echo ""
# fi


