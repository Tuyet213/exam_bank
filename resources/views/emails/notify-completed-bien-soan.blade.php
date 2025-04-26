<!DOCTYPE html>
<html>
<head>
    <title>Thông Báo Hoàn Thành Biên Soạn</title>
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
        .details {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
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
            <h2>Thông Báo Hoàn Thành Biên Soạn Học Phần</h2>
        </div>
        
        <div class="content">
            <p>Kính gửi Phòng Đảm bảo chất lượng,</p>
            
            <p>Trân trọng thông báo học phần sau đây đã hoàn thành quá trình biên soạn và nghiệm thu:</p>
            
            <div class="details">
                <p><strong>Học phần:</strong> {{ $bien_ban->ctDSDangKy->hocPhan->ten }}</p>
                <p><strong>Giảng viên biên soạn:</strong> 
                @php
                    $gvNames = [];
                    if(isset($bien_ban->ctDSDangKy->dsGVBienSoans) && count($bien_ban->ctDSDangKy->dsGVBienSoans) > 0) {
                        foreach($bien_ban->ctDSDangKy->dsGVBienSoans as $gv) {
                            if(isset($gv->vienChuc->name)) {
                                $gvNames[] = $gv->vienChuc->name;
                            }
                        }
                    }
                    echo !empty($gvNames) ? implode(', ', $gvNames) : 'Chưa có giảng viên';
                @endphp
                </p>
                <p><strong>Bộ môn:</strong> {{ $bien_ban->ctDSDangKy->hocPhan->boMon->ten }}</p>
                <p><strong>Học kỳ:</strong> {{ $bien_ban->ctDSDangKy->dsDangKy->hoc_ki }}</p>
                <p><strong>Năm học:</strong> {{ $bien_ban->ctDSDangKy->dsDangKy->nam_hoc }}</p>
                
            </div>
            
            <p>Biên bản nghiệm thu đã được hoàn thành đầy đủ và số giờ quy đổi đã được cập nhật trên hệ thống.</p>
            
            <p>Trân trọng,<br>
            {{ $nguoi_gui->name }}<br>
            Trưởng Bộ môn {{ $bo_mon->ten }}</p>
        </div>
        
        <div class="footer">
            <p>Email này được gửi từ hệ thống quản lý biên soạn đề thi.</p>
        </div>
    </div>
</body>
</html> 