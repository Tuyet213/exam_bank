<!DOCTYPE html>
<html>
<head>
    <title>Thông Báo Xem Xét Kết Quả Đăng Ký</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
        }
        .content {
            padding: 20px 0;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 0.9em;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Thông Báo Xem Xét Kết Quả Đăng Ký</h2>
        </div>
        
        <div class="content">
            <p>Kính gửi {{ $dsDangKy->boMon->truongBoMon->name }},</p>
            
            <p>Có một danh sách đăng ký mới cần được xem xét từ bộ môn {{ $dsDangKy->boMon->ten }}.</p>
            
            <p><strong>Chi tiết danh sách:</strong></p>
            <ul>
                <li>Tên danh sách: {{ $dsDangKy->ten }}</li>
                <li>Thời gian: {{ $dsDangKy->thoi_gian }}</li>
                <li>Bộ môn: {{ $dsDangKy->boMon->ten }}</li>
            </ul>
            
            <p>Vui lòng đăng nhập vào hệ thống để xem xét và phê duyệt danh sách này.</p>
            
            <p>Trân trọng,<br>
            Hệ thống quản lý đăng ký</p>
        </div>
        
        <div class="footer">
            <p>Email này được gửi tự động từ hệ thống. Vui lòng không trả lời email này.</p>
        </div>
    </div>
</body>
</html> 