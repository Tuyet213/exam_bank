<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông báo</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            border-bottom: 2px solid #5cb85c;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h2 {
            color: #5cb85c;
            margin: 0;
        }
        .content {
            padding: 20px 0;
            white-space: pre-line;
        }
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #777;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $title }}</h2>
    </div>
    
    <div class="content">
        {!! nl2br(e($content)) !!}
    </div>
    
    <div class="footer">
        <p>Email này được gửi tự động từ hệ thống Quản lý ngân hàng câu hỏi/đề thi.</p>
        <p>Vui lòng không trả lời email này.</p>
    </div>
</body>
</html>