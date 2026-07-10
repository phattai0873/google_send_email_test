# BÁO CÁO TỔNG KẾT DỰ ÁN

## HỆ THỐNG ĐĂNG NHẬP GOOGLE & GỬI EMAIL QUẢNG BÁ

Dự án được xây dựng trên nền tảng **Laravel** nhằm triển khai luồng đăng nhập Google OAuth 2.0 và quản lý gửi chiến dịch email quảng bá hàng loạt qua **Gmail API (hoặc SMTP dự phòng)** của tài khoản đăng nhập, tích hợp thiết kế giao diện tối giản kỹ thuật theo phong cách **Cobalt (Modern-Minimal)**.

---

## 1. TỔNG QUAN DỰ ÁN & THIẾT KẾ CƠ SỞ DỮ LIỆU (DATABASE)

Dự án đã được cấu hình tối giản hóa tuyệt đối để **chỉ giữ lại 2 bảng lõi** phục vụ nghiệp vụ trong cơ sở dữ liệu:

### 1.1 Chi tiết 2 Bảng trong Cơ sở dữ liệu:

- **Bảng `users`**: Lưu trữ và quản lý người dùng đăng nhập.
    - `id` (Khóa chính, Auto Increment)
    - `name` (Họ tên lấy từ Google)
    - `email` (Địa chỉ email - Unique)
    - `avatar` (Ảnh đại diện Google)
    - `google_id` (ID định danh Google)
    - `google_token` (Access Token OAuth dùng để gọi API gửi thư)
    - `created_at`, `updated_at`
- **Bảng `email_logs`**: Nhật ký gửi thư cá nhân hóa.
    - `id` (Khóa chính, Auto Increment)
    - `user_id` (Khóa ngoại liên kết bảng `users` - Cascade Delete)
    - `subject` (Tiêu đề thư)
    - `content` (Nội dung thư)
    - `recipients` (Mảng JSON chứa danh sách email nhận thực tế)
    - `total_recipients` (Tổng số người nhận được tích chọn gửi)
    - `sent_success` (Số lượng gửi thành công)
    - `sent_failed` (Số lượng gửi thất bại)
    - `created_at`, `updated_at`

### 1.2 Điều chỉnh Driver cấu hình để tối giản Database:

Để hệ thống không tự động tạo các bảng quản lý hệ thống mặc định của Laravel làm loãng cơ sở dữ liệu, các driver đã được chuyển cấu hình từ `database` sang `file` và `sync` trong tệp `.env`:

- `SESSION_DRIVER=file`: Lưu trữ session đăng nhập của người dùng qua các tệp tin cục bộ, loại bỏ bảng `sessions`.
- `CACHE_STORE=file`: Bộ nhớ cache lưu dạng file, loại bỏ bảng `cache`.
- `QUEUE_CONNECTION=sync`: Xử lý gửi hàng loạt đồng bộ trực tiếp, loại bỏ bảng `jobs`.

---

## 2. CHI TIẾT 5 TEST CASES THỬ NGHIỆM HỆ THỐNG

Dự án tích hợp bộ kiểm thử tự động tại [GoogleAuthAndCampaignTest.php](file:///home/phattai2908__/Projects/google_send_email_test/tests/Feature/GoogleAuthAndCampaignTest.php) bao quát chính xác 5 kịch bản nghiệp vụ theo yêu cầu:

### Test Case 1: Đăng nhập Google lần đầu -> Tạo tài khoản mới

- **Mô tả**: Khi người dùng chưa có tài khoản trong hệ thống tiến hành kết nối Google, hệ thống sẽ tự động tạo một dòng dữ liệu mới trong bảng `users` và đăng nhập.
- **Mã kiểm thử (PHPUnit)**:

```php
public function test_google_login_callback_registers_new_user_and_logs_in(): void
{
    $googleUserMock = Mockery::mock(SocialiteUser::class);
    $googleUserMock->shouldReceive('getId')->andReturn('google-id-123');
    $googleUserMock->shouldReceive('getEmail')->andReturn('newuser@gmail.com');
    $googleUserMock->shouldReceive('getName')->andReturn('New Google User');
    $googleUserMock->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/avatar/1');
    $googleUserMock->token = 'mock-access-token';

    $providerMock = Mockery::mock(\Laravel\Socialite\Two\GoogleProvider::class);
    $providerMock->shouldReceive('stateless')->andReturnSelf();
    $providerMock->shouldReceive('user')->andReturn($googleUserMock);

    Socialite::shouldReceive('driver')->with('google')->andReturn($providerMock);

    $response = $this->get(route('google.callback'));

    $response->assertRedirect(route('home'));
    $response->assertSessionHas('success', 'Đăng nhập thành công!');

    // Xác minh tài khoản mới được tạo trong database
    $this->assertDatabaseHas('users', [
        'email' => 'newuser@gmail.com',
        'google_id' => 'google-id-123',
        'name' => 'New Google User',
    ]);
    $this->assertTrue(Auth::check());
}
```

---

### Test Case 2: Đăng nhập lại -> Cập nhật Token, không tạo tài khoản trùng lặp

- **Mô tả**: Khi người dùng đã có tài khoản từ trước tiến hành đăng nhập lại, hệ thống sẽ nhận diện theo `email` hoặc `google_id`, tiến hành cập nhật `google_token` mới và thông tin cập nhật mà không tạo thêm bản ghi mới trong bảng `users`.
- **Mã kiểm thử (PHPUnit)**:

```php
public function test_google_login_callback_updates_existing_user_and_logs_in(): void
{
    // Tạo sẵn user cũ trong database
    $existingUser = User::create([
        'name' => 'Old Name',
        'email' => 'existing@gmail.com',
        'google_id' => null,
        'avatar' => null,
    ]);

    $googleUserMock = Mockery::mock(SocialiteUser::class);
    $googleUserMock->shouldReceive('getId')->andReturn('google-id-existing');
    $googleUserMock->shouldReceive('getEmail')->andReturn('existing@gmail.com');
    $googleUserMock->shouldReceive('getName')->andReturn('Updated Name');
    $googleUserMock->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/avatar/updated');
    $googleUserMock->token = 'new-mock-token-456';

    $providerMock = Mockery::mock(\Laravel\Socialite\Two\GoogleProvider::class);
    $providerMock->shouldReceive('stateless')->andReturnSelf();
    $providerMock->shouldReceive('user')->andReturn($googleUserMock);

    Socialite::shouldReceive('driver')->with('google')->andReturn($providerMock);

    $response = $this->get(route('google.callback'));

    $response->assertRedirect(route('home'));

    // Xác minh thông tin được cập nhật và không bị trùng lặp bản ghi
    $this->assertDatabaseHas('users', [
        'id' => $existingUser->id,
        'email' => 'existing@gmail.com',
        'name' => 'Updated Name',
        'google_id' => 'google-id-existing',
        'google_token' => 'new-mock-token-456',
    ]);
    $this->assertEquals(1, User::where('email', 'existing@gmail.com')->count());
}
```

---

### Test Case 3: Gửi email quảng bá thành công & Lưu nhật ký

- **Mô tả**: Khi gửi chiến dịch email thành công, hệ thống thực hiện gọi gửi mail (qua SMTP hoặc Gmail API), ghi nhận trạng thái và tạo bản ghi log trong bảng `email_logs` đính kèm danh sách email nhận thực tế.
- **Mã kiểm thử (PHPUnit)**:

```php
public function test_successful_campaign_send_triggers_mail_and_database_logs(): void
{
    Mail::fake();

    $user = User::create([
        'name' => 'Campaign Admin',
        'email' => 'admin@gmail.com',
    ]);

    $response = $this->actingAs($user)->post(route('email.send'), [
        'subject' => 'New Product Broadcast',
        'content' => 'Hello team,\nCheck out our new launch!',
        'recipients' => [
            'devmelon2601@gmail.com',
            'dev.watermelon2602@gmail.com',
        ],
    ]);

    $response->assertRedirect(route('home'));
    $response->assertSessionHas('success');

    // Xác minh thư được gửi tới các email nhận
    Mail::assertSent(CampaignEmail::class, 2);

    // Xác minh nhật ký (EmailLog) được ghi lại chính xác vào Database
    $this->assertDatabaseHas('email_logs', [
        'user_id' => $user->id,
        'subject' => 'New Product Broadcast',
        'total_recipients' => 2,
        'sent_success' => 2,
        'sent_failed' => 0,
    ]);
}
```

---

### Test Case 4: Khách chưa đăng nhập -> Chặn gửi thư

- **Mô tả**: Nếu người dùng chưa đăng nhập cố tình gửi yêu cầu gửi thư (POST đến `/send-email`), middleware `auth` sẽ chặn lại và chuyển hướng về trang chủ để đăng nhập.
- **Mã kiểm thử (PHPUnit)**:

```php
public function test_cannot_send_email_when_unauthenticated(): void
{
    $response = $this->post(route('email.send'), [
        'subject' => 'Test Subject',
        'content' => 'Test Content',
        'recipients' => ['test@gmail.com']
    ]);

    // Trả về trang chủ do chưa xác thực
    $response->assertRedirect('/');
    $this->assertDatabaseEmpty('email_logs');
}
```

---

### Test Case 5: Gửi mail với nội dung/tiêu đề rỗng -> Validate lỗi

- **Mô tả**: Nếu người dùng gửi form nhưng để trống tiêu đề hoặc nội dung, hệ thống sẽ chặn lại, trả về lỗi validation trong session và không lưu bất kỳ log nào vào database.
- **Mã kiểm thử (PHPUnit)**:

```php
public function test_cannot_send_email_with_empty_subject_or_content(): void
{
    $user = User::create([
        'name' => 'Test User',
        'email' => 'test@gmail.com',
    ]);

    $response = $this->actingAs($user)->post(route('email.send'), [
        'subject' => '',
        'content' => '',
        'recipients' => ['test@example.com'],
    ]);

    // Trả về lỗi validation
    $response->assertSessionHasErrors(['subject', 'content']);
    $this->assertDatabaseEmpty('email_logs');
}
```
