<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $emailSubject }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f4f5f6;
            color: #2d3748;
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: none;
            width: 100% !important;
        }
        .wrapper {
            background-color: #f4f5f6;
            padding: 32px 16px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            border-top: 4px solid #4f46e5;
        }
        .header {
            padding: 24px 32px 16px;
            border-bottom: 1px solid #f7fafc;
        }
        .logo-text {
            font-size: 14px;
            font-weight: 700;
            color: #4f46e5;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }
        .content {
            padding: 32px;
            line-height: 1.6;
            font-size: 15px;
            color: #2d3748;
        }
        .content h1, .content h2, .content h3 {
            color: #1a202c;
            margin-top: 0;
            margin-bottom: 16px;
            font-weight: 700;
        }
        .email-subject {
            font-size: 20px;
            color: #1a202c;
            margin-bottom: 24px;
            border-bottom: 1px solid #edf2f7;
            padding-bottom: 12px;
            font-weight: 700;
        }
        .footer {
            background-color: #fafbfc;
            padding: 24px 32px;
            text-align: center;
            font-size: 11px;
            color: #718096;
            border-top: 1px solid #edf2f7;
            line-height: 1.5;
        }
        .footer p {
            margin: 0 0 4px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <span class="logo-text">MailCampaign</span>
            </div>
            <div class="content">
                <div class="email-subject">{{ $emailSubject }}</div>
                {!! $content !!}
            </div>
            <div class="footer">
                <p>Thư này được gửi tự động từ chiến dịch quảng bá thông qua hệ thống MailCampaign.</p>
                <p>&copy; {{ date('Y') }} MailCampaign. Tất cả các quyền được bảo lưu.</p>
            </div>
        </div>
    </div>
</body>
</html>
