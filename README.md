# Google Authentication & Gmail API Broadcast Manager (Laravel)

Dự án này là hệ thống hỗ trợ người dùng đăng nhập bằng tài khoản Google (OAuth 2.0) và thực hiện chức năng gửi email quảng bá hàng loạt đến một danh sách người nhận linh hoạt sử dụng chính **Gmail API** của tài khoản đăng nhập đó.

Hệ thống được thiết kế theo phong cách giao diện tối giản kỹ thuật **Cobalt Theme (Modern-Minimal)** với trải nghiệm mượt mà, phản hồi nhanh cùng hộp lệnh phím tắt `⌘K`.

---

## 🌟 Tính năng nổi bật

1. **Google OAuth Login (Stateless)**: Đăng nhập an toàn bằng tài khoản Google, tự động đồng bộ hóa thông tin người dùng (tên, email, ảnh đại diện) vào cơ sở dữ liệu.
2. **Gửi Mail động qua Gmail API**: Thư quảng bá được gửi đi từ chính địa chỉ Gmail của người vừa đăng nhập (sử dụng Access Token lưu trong session) thay vì dùng cấu hình SMTP tĩnh thông thường.
3. **Quản lý người nhận linh hoạt (Dynamic Recipient Checklist)**:
    - Tích chọn/bỏ tích để quyết định người nhận trong danh sách gửi.
    - Chỉnh sửa trực tiếp nội dung địa chỉ email.
    - Thêm mới hàng người nhận hoặc Xóa người nhận động bằng JavaScript.
4. **Nhật ký chiến dịch cá nhân hóa (Email Logs)**: Lưu trữ lịch sử tất cả các email đã gửi thành công/thất bại và hiển thị riêng tư theo từng tài khoản đăng nhập.
5. **Hộp lệnh nhanh ⌘K (Command Palette)**: Nhấn `⌘K` hoặc `Ctrl+K` để mở hộp tìm kiếm và điều hướng nhanh (Focus vào Tiêu đề, Nội dung, Cuộn nhanh xuống Nhật ký...).
6. **Mailable Layout đẹp mắt**: Mẫu email gửi đi chuyên nghiệp, căn chỉnh dòng hiển thị sạch sẽ và hỗ trợ hiển thị tốt trên mọi thiết bị.

---

## 🗄️ Cấu trúc Cơ sở dữ liệu (Database Schema)

Hệ thống được thiết kế tối giản, chỉ lưu trữ dữ liệu trên **2 bảng chính**:

### 1. Bảng `users`

Lưu trữ thông tin tài khoản người dùng đăng nhập qua Google OAuth.

- `id`: Khóa chính (BigInt, Auto Increment).
- `name`: Tên hiển thị từ Google.
- `email`: Địa chỉ email (Unique).
- `avatar`: Đường dẫn ảnh đại diện.
- `google_id`: ID định danh duy nhất của Google.
- `google_token`: Access Token dùng để đại diện gửi thư.
- `created_at` / `updated_at`: Thời gian tạo/cập nhật.

### 2. Bảng `email_logs`

Lưu nhật ký các lần gửi email quảng bá.

- `id`: Khóa chính (BigInt, Auto Increment).
- `user_id`: Khóa ngoại liên kết tới bảng `users` (Cascade Delete).
- `subject`: Tiêu đề email.
- `content`: Nội dung thư.
- `recipients`: Danh sách email nhận (lưu dưới dạng mảng JSON).
- `total_recipients`: Tổng số người nhận được gửi.
- `sent_success`: Số lượng gửi thành công.
- `sent_failed`: Số lượng gửi thất bại.
- `created_at` / `updated_at`: Thời gian tạo/cập nhật.

---

## 🛠️ Hướng dẫn Cài đặt & Cấu hình

### 1. Yêu cầu hệ thống

- **PHP** >= 8.2
- **Composer**
- **Node.js & NPM**
- **MySQL**

### 2. Các bước cài đặt

```bash
# 1. Clone dự án và truy cập vào thư mục
git clone <repository_url> google_send_email_test
cd google_send_email_test

# 2. Cài đặt các gói phụ thuộc PHP
composer install

# 3. Cài đặt các gói phụ thuộc CSS/JS
npm install
npm run build

# 4. Sao chép tệp cấu hình môi trường
cp .env.example .env
```

### 3. Cấu hình tệp `.env`

Mở file `.env` và thiết lập các thông số sau:

#### Cơ sở dữ liệu MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=google_send_email_test
DB_USERNAME=root
DB_PASSWORD=your_mysql_password
```

#### Google OAuth credentials & Redirect URI:

Đăng ký thông tin thông tin Credential (OAuth 2.0 Client IDs) tại [Google Cloud Console](https://console.cloud.google.com/):

```env
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

#### Cấu hình Email dự phòng (SMTP Fallback):

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_backup_email@gmail.com
MAIL_PASSWORD="your_gmail_app_password"
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="your_backup_email@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### 4. Chạy Migrations tái cấu trúc DB

```bash
php artisan migrate
```

---

## 🚀 Kích hoạt API Google & Chạy dự án

### 1. Kích hoạt Gmail API trên Google Cloud

Để hệ thống có thể dùng token tài khoản gửi thư đi, bạn bắt buộc phải kích hoạt Gmail API cho dự án của bạn trên Google Cloud Console:

1. Truy cập vào trang **[Kích hoạt Gmail API](https://console.developers.google.com/apis/api/gmail.googleapis.com/overview)**.
2. Đảm bảo chọn đúng dự án và bấm **Enable** (Bật).
3. Tại trang cấu hình **OAuth consent screen**:
    - Thêm địa chỉ Gmail bạn định đăng nhập thử nghiệm vào mục **Test users** (vì app đang ở trạng thái Testing nên bắt buộc phải có tài khoản trong danh sách này mới đăng nhập được).
    - Khi đăng nhập lần đầu, hãy chắc chắn tích chọn vào hộp kiểm **"Gửi email thay mặt bạn" (Send email on your behalf)** ở màn hình đồng ý của Google.

### 2. Khởi chạy Server

```bash
# Khởi động Laravel server
php artisan serve
```

Mở trình duyệt truy cập vào địa chỉ mặc định `http://127.0.0.1:8000` để bắt đầu trải nghiệm.
