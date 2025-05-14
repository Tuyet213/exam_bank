<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông báo</title>
</head>
<body>
    <h2>{{ $title }}</h2>
    <div>
        {!! nl2br(e($content)) !!}
    </div>
    <hr>
    <small>Email này được gửi tự động từ hệ thống Quản lý ngân hàng câu hỏi/đề thi. Vui lòng không trả lời email này.</small>
</body>
</html>
