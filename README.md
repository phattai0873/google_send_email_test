# Google Authentication & Gmail API Broadcast Manager (Laravel)

Dự án này là hệ thống hỗ trợ người dùng đăng nhập bằng tài khoản Google (OAuth 2.0) và thực hiện chức năng gửi email quảng bá hàng loạt đến một danh sách người nhận linh hoạt sử dụng chính **Gmail API** của tài khoản đăng nhập đó.

Hệ thống được thiết kế theo phong cách giao diện tối giản kỹ thuật **Cobalt Theme (Modern-Minimal)** với trải nghiệm mượt mà, phản hồi nhanh cùng hộp lệnh phím tắt `⌘K`.

---

## 🌟 Tính năng nổi bật

1. **Google OAuth Login (Stateless)**: Đăng nhập an toàn bằng tài khoản Google, tự động đồng bộ hóa thông tin người dùng (tên, email, ảnh đại diện) vào cơ sở dữ liệu.
2. **Gửi Mail động qua Gmail API**: Thư quảng bá được gửi đi từ chính địa chỉ Gmail của người vừa đăng nhập (sử dụng Access Token lưu trong session) thay vì dùng cấu hình SMTP tĩnh thông thường.
3. **Hệ thống Hàng đợi Cơ sở dữ liệu (Laravel Database Queue)**:
    - Chuyển toàn bộ quá trình lặp gửi email thành tác vụ chạy nền (asynchronous background job). Tránh triệt để tình trạng treo trình duyệt hoặc lỗi quá thời gian (HTTP timeouts).
    - Tự động áp dụng khoảng trễ **1 giây (throttling)** giữa mỗi email gửi đi nhằm bảo vệ uy tín tên miền/tài khoản và tránh bị các bộ lọc thư rác (Spam Filter) chặn lại.
4. **Quản lý người nhận linh hoạt (Dynamic Recipient Checklist)**:
    - Tích chọn/bỏ tích để quyết định người nhận trong danh sách gửi.
    - Chỉnh sửa trực tiếp nội dung địa chỉ email.
    - Thêm mới hàng người nhận hoặc Xóa người nhận động bằng JavaScript.
5. **Nhật ký chiến dịch & Chi tiết trạng thái (Email Logs)**: 
    - Lưu giữ lịch sử đầy đủ theo từng tài khoản.
    - Trạng thái gửi hiển thị động theo thời gian thực (`Chờ gửi`, `Đang gửi`, `Hoàn thành`, `Thất bại`).
    - Xem chi tiết hiển thị danh sách thẻ người nhận kèm trạng thái cụ thể của riêng email đó (Hiển thị thẻ xanh lá nếu gửi thành công, thẻ đỏ kèm lý do lỗi cụ thể nếu gửi thất bại).
6. **Hộp lệnh nhanh ⌘K (Command Palette)**: Nhấn `⌘K` hoặc `Ctrl+K` để mở hộp tìm kiếm và điều hướng nhanh (Focus vào Tiêu đề, Nội dung, Cuộn nhanh xuống Nhật ký...).
7. **Mailable Layout đẹp mắt**: Mẫu email gửi đi chuyên nghiệp, có thiết kế Cobalt Accent top bar cao cấp, tự động tối ưu hóa hiển thị và kết xuất đúng mã HTML từ trình soạn thảo Quill.

---

## 🗄️ Cấu trúc Cơ sở dữ liệu (Database Schema)

Hệ thống lưu trữ dữ liệu trên **3 bảng chính**:

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
- `recipients`: Danh sách email nhận (Lưu dưới dạng mảng JSON chứa trạng thái chi tiết của từng email: `email`, `status`, `error`).
- `total_recipients`: Tổng số người nhận được gửi.
- `sent_success`: Số lượng gửi thành công.
- `sent_failed`: Số lượng gửi thất bại.
- `status`: Trạng thái tổng thể của chiến dịch (`pending`, `sending`, `completed`, `failed`).
- `created_at` / `updated_at`: Thời gian tạo/cập nhật.

### 3. Bảng `jobs` (Laravel System Table)

Dùng để lưu trữ các tác vụ gửi thư đang xếp hàng chờ hàng đợi chạy nền (Queue Worker) xử lý.

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

#### Hàng đợi (Queue Connection):

```env
QUEUE_CONNECTION=database
```
*Mẹo nhỏ: Nếu phát triển ở môi trường local và không muốn chạy tiến trình hàng đợi độc lập, bạn có thể thiết lập `QUEUE_CONNECTION=sync` để gửi thư đồng bộ trực tiếp.*

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
# Tạo cấu trúc bảng sạch và thiết lập bảng jobs hàng đợi
php artisan migrate:fresh
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

### 2. Khởi chạy Server & Hàng đợi

Để chạy ứng dụng đầy đủ, bạn cần chạy đồng thời **hai tiến trình sau**:

```bash
# Tiến trình 1: Khởi động máy chủ Web Laravel
php artisan serve

# Tiến trình 2: Khởi động bộ xử lý hàng đợi gửi thư ngầm
php artisan queue:work
```

Mở trình duyệt truy cập vào địa chỉ mặc định `http://127.0.0.1:8000` để bắt đầu trải nghiệm.
