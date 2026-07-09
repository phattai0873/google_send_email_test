<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $emailSubject }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            color: #334155;
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: none;
            width: 100% !important;
        }
        .wrapper {
            background-color: #f8fafc;
            padding: 40px 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .header {
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            padding: 30px 40px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            font-size: 24px;
            font-weight: 700;
            margin: 0;
            letter-spacing: -0.025em;
        }
        .content {
            padding: 40px;
            line-height: 1.6;
            font-size: 16px;
        }
        .content p {
            margin: 0 0 16px;
        }
        .footer {
            background-color: #f1f5f9;
            padding: 20px 40px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
        }
        .footer p {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h1>{{ $emailSubject }}</h1>
            </div>
            <div class="content">
                {!! nl2br(e($content)) !!}
            </div>
            <div class="footer">
                <p>Tin nhắn này được gửi tự động từ chiến dịch quảng bá của chúng tôi.</p>
                <p>&copy; {{ date('Y') }} Laravel Mail App. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
