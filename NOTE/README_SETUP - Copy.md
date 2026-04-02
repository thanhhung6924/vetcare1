# VetCare - Hệ thống đăng nhập và đăng ký

## Hướng dẫn cài đặt

### 1. Yêu cầu hệ thống

- PHP 7.4 trở lên
- MySQL 5.7 trở lên
- XAMPP/WAMP/LAMP server
- Sử dụng MySQLi

### 2. Cài đặt database

#### Cách 1: Sử dụng file setup.php (Khuyến nghị)

1. Đảm bảo XAMPP/WAMP đã khởi động MySQL
2. Truy cập: `http://localhost/your-project/setup.php`
3. Làm theo hướng dẫn trên màn hình
4. **Quan trọng**: Xóa file `setup.php` sau khi cài đặt xong

#### Cách 2: Import trực tiếp SQL

1. Mở phpMyAdmin (`http://localhost/phpmyadmin`)
2. Import file `setup_database.sql`
3. Hoặc copy nội dung file và paste vào SQL tab

### 3. Cấu hình

#### Kiểm tra file `includes/db.php`:

```php
$host = 'localhost';
$db   = 'VetCare';  // Tên database
$user = 'root';     // Username MySQL
$pass = '';         // Password MySQL (để trống nếu dùng XAMPP)
```

### 4. Cấu trúc Database

#### Bảng chính:

- `roles`: Vai trò người dùng (admin, patient, doctor)
- `users`: Thông tin đăng nhập
- `users_info`: Thông tin chi tiết người dùng
- `user_addresses`: Địa chỉ người dùng
- `guest_users`: Khách vãng lai
- `remember_tokens`: Token "Ghi nhớ đăng nhập"

#### Tài khoản mặc định:

- **Username**: admin
- **Password**: admin123
- **Email**: admin@VetCare.com
- **Role**: Administrator

### 5. Tính năng

#### Đăng ký:

- ✅ Validation đầy đủ (email, phone, password)
- ✅ Mã hóa mật khẩu bằng bcrypt
- ✅ Kiểm tra trùng lặp username/email/phone
- ✅ Transaction để đảm bảo dữ liệu
- ✅ Giao diện responsive với Bootstrap 5

#### Đăng nhập:

- ✅ Đăng nhập bằng username hoặc email
- ✅ Tính năng "Ghi nhớ đăng nhập" (30 ngày)
- ✅ Redirect theo role người dùng
- ✅ Validation client-side và server-side
- ✅ Bảo mật chống XSS

#### Bảo mật:

- ✅ Password hashing với PHP password_hash()
- ✅ Prepared statements chống SQL injection
- ✅ XSS protection với htmlspecialchars()
- ✅ CSRF protection (có thể thêm)
- ✅ Session management

### 6. Cấu trúc file

```
project/
├── includes/
│   ├── db.php              # Kết nối database
│   ├── header.php          # Header chung
│   └── footer.php          # Footer chung
├── assets/                 # CSS, JS, images
├── login.php              # Trang đăng nhập
├── register.php           # Trang đăng ký
├── logout.php             # Đăng xuất
├── setup.php              # Setup database (xóa sau khi dùng)
├── setup_database.sql     # File SQL database
└── README_SETUP.md        # Hướng dẫn này
```

### 7. Sử dụng

1. **Truy cập trang chủ**: `http://localhost/your-project/`
2. **Đăng ký tài khoản mới**: `/register.php`
3. **Đăng nhập**: `/login.php`
4. **Đăng xuất**: `/logout.php`

### 8. Customization

#### Thay đổi cấu hình database:

Sửa file `includes/db.php`

#### Thêm validation:

Sửa file `register.php` và `login.php`

#### Thay đổi giao diện:

Các file sử dụng Bootstrap 5, có thể custom CSS trong `assets/`

### 9. Troubleshooting

#### Lỗi kết nối database:

- Kiểm tra MySQL đã chạy chưa
- Kiểm tra username/password trong `includes/db.php`
- Kiểm tra tên database có đúng không

#### Lỗi không tạo được bảng:

- Kiểm tra quyền user MySQL
- Chạy lại `setup.php`

#### Lỗi session:

- Kiểm tra PHP session có hoạt động không
- Kiểm tra quyền ghi folder temp

### 10. Security Notes

- **Xóa `setup.php`** sau khi cài đặt
- Đổi mật khẩu admin mặc định
- Cập nhật PHP và MySQL thường xuyên
- Backup database định kỳ
- Sử dụng HTTPS trong production

### 11. License

This project is open source. Feel free to modify and distribute.

---

**Liên hệ hỗ trợ**: Tạo issue trên GitHub hoặc liên hệ developer.

Từ db.php:

✅ format_currency() - Format tiền tệ VN
Từ format_helpers.php (với safety check):
✅ calculateDiscountPrice() - Tính giá giảm dựa trên rating
✅ getProductImage() - Xử lý ảnh với fallback
✅ formatRating() - Format rating 1 chữ số thập phân
✅ generateStars() - Tạo HTML stars cho rating
✅ calculateVAT() - Tính thuế VAT 10%
✅ calculateShipping() - Tính phí ship (miễn phí >500k)
✅ formatQuantity() - Format số lượng
✅ formatOrderCode() - Tạo mã đơn hàng QM000001
✅ formatDateVN() - Format ngày tháng VN
✅ timeAgo() - Thời gian tương đối

🏢 Tích hợp API Địa chỉ Việt Nam:
Select2 integration - Sử dụng Select2 cho dropdown đẹp và có search
Vietnam Address API - Tích hợp provinces.open-api.vn để lấy đầy đủ dữ liệu địa chỉ VN
Cascade selection - Logic chọn địa chỉ theo cấu trúc:
Quốc gia → Tỉnh/Thành phố → Quận/Huyện → Phường/Xã
Smart form handling - Tự động điền lại địa chỉ cũ khi edit
Dynamic loading - Load data theo realtime khi user chọn
