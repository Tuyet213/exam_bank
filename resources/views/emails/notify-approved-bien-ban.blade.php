<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông báo biên bản đã được duyệt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #777;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .info-table th, .info-table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .info-table th {
            width: 30%;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Thông báo: Biên bản họp bộ môn đã được duyệt</h2>
    </div>
    
    <p>Kính gửi Thầy/Cô {{ $nguoiNhan->name }},</p>
    
    <p>Phòng Đảm bảo chất lượng đã duyệt biên bản họp bộ môn của Thầy/Cô.</p>
    
    <p><strong>Thông tin chi tiết:</strong></p>
    
    <table class="info-table">
        <tr>
            <th>Học phần:</th>
            <td>{{ $bienBan->ctDSDangKy->hocPhan->ten ?? 'Không xác định' }} (Mã: {{ $bienBan->ctDSDangKy->hocPhan->id ?? 'Không xác định' }})</td>
        </tr>
        <tr>
            <th>Năm học:</th>
            <td>{{ $bienBan->ctDSDangKy->dsDangKy->nam_hoc ?? 'Không xác định' }}</td>
        </tr>
        <tr>
            <th>Học kỳ:</th>
            <td>{{ $bienBan->ctDSDangKy->dsDangKy->hoc_ki ?? 'Không xác định' }}</td>
        </tr>
        <tr>
            <th>Bộ môn:</th>
            <td>{{ $bienBan->ctDSDangKy->hocPhan->boMon->ten ?? 'Không xác định' }}</td>
        </tr>
        <tr>
            <th>Thời gian biên bản:</th>
            <td>{{ $bienBan->thoi_gian ?? 'Không xác định' }}</td>
        </tr>
    </table>
    
    <p>Cảm ơn Thầy/Cô đã hoàn thành biên bản họp bộ môn.</p>
    
    <p>Trân trọng,<br>
    Phòng Đảm bảo chất lượng</p>
    
    <div class="footer">
        <p>Email này được gửi tự động, vui lòng không trả lời email này.</p>
    </div>
</body>
</html> 