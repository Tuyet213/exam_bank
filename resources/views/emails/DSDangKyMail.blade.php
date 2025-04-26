<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $tieuDe }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333333;
            font-size: 24px;
            margin-bottom: 20px;
        }
        p {
            color: #666666;
            font-size: 16px;
            line-height: 1.5;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #999999;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .content {
            padding: 20px;
            margin: 20px 0;
        }
        .header {
            margin-bottom: 20px;
        }
        .badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            color: white;
        }
        .badge-pending {
            background-color: #ffc107;
        }
        .badge-draft {
            background-color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>{{ $tieuDe }}</h2>
            <p>Bộ môn {{ $tenBoMon }} đã gửi một danh sách đăng ký xây dựng ngân hàng câu hỏi/đề thi mới.</p>
            <p>Người gửi: {{ $tenNguoiGui }}</p>
        </div>

        <div id="content" class="content">
            <h3>Danh sách đăng ký: Học kỳ {{ $dsdangky->hoc_ki }}, năm học {{ $dsdangky->nam_hoc }}</h3>
            
            <table>
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Học phần</th>
                        <th>Viên chức</th>
                        <th>Loại ngân hàng</th>
                        <th>Hình thức thi</th>
                        <th>Số lượng</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ctdsdangky as $index => $ct)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $ct->hocPhan->ten }}</td>
                            <td>
                                @foreach($ct->dsGVBienSoans as $gvbs)
                                    {{ $gvbs->vienChuc->name }}@if(!$loop->last), @endif
                                @endforeach
                            </td>
                            <td>{{ $ct->loai_ngan_hang == 1 ? 'Ngân hàng câu hỏi' : 'Ngân hàng đề thi' }}</td>
                            <td>{{ $ct->hinh_thuc_thi }}</td>
                            <td>{{ $ct->so_luong }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center;">Không có dữ liệu</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <p style="margin-top: 20px;">
                Vui lòng kiểm tra và phê duyệt danh sách đăng ký này.
            </p>
        </div>

        <div class="footer">
            <p>Trân trọng,</p>
            <p>{{ $tenNguoiGui }}</p>
        </div>
    </div>

    <div style="margin-top: 20px; font-size: 12px; color: #666;">
        <p>Email này được gửi tự động từ hệ thống Exam Bank.</p>
    </div>
</body>
</html>