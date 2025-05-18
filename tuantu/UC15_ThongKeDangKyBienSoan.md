```mermaid
%%{init: {
  'theme': 'base', 
  'themeVariables': { 
    'primaryColor': '#ffffff', 
    'primaryBorderColor': '#000000', 
    'primaryTextColor': '#000000', 
    'noteBackgroundColor': '#ffffff', 
    'noteBorderColor': '#000000'
  },
  'fontSize': '40px',
  'fontFamily': 'arial'
}}%%

sequenceDiagram
    participant Người_dùng as Người dùng
    participant Giao_diện as Giao diện
    participant Hệ_thống as Hệ thống
    participant CSDL as Cơ sở dữ liệu
    rect rgb(255, 255, 255)
    Người_dùng->>Giao_diện: 1 : Truy cập trang thống kê
    Giao_diện->>Hệ_thống: 2 : Gửi yêu cầu lấy dữ liệu thống kê
    Hệ_thống->>CSDL: 3 : Truy vấn dữ liệu phân cấp (Năm học/Học kỳ/Khoa/Bộ môn)
    CSDL-->>Hệ_thống: 4 : Trả về dữ liệu cấu trúc phân cấp
    Hệ_thống-->>Giao_diện: 5 : Gửi dữ liệu thống kê
    Giao_diện-->>Người_dùng: 6 : Hiển thị giao diện thống kê tổng hợp
    
    Người_dùng->>Giao_diện: 7 : Chọn tiêu chí lọc (Khoa/Bộ môn/Năm học/Học kỳ)
    Giao_diện->>Hệ_thống: 8 : Gửi yêu cầu lọc dữ liệu
    Hệ_thống->>CSDL: 9 : Truy vấn dữ liệu theo tiêu chí
    CSDL-->>Hệ_thống: 10 : Trả về dữ liệu đã lọc
    Hệ_thống-->>Giao_diện: 11 : Gửi dữ liệu thống kê đã lọc
    Giao_diện-->>Người_dùng: 12 : Hiển thị kết quả lọc
    
    alt Xem chi tiết
        Người_dùng->>Giao_diện: 13 : Mở rộng một cấp (Năm học/Học kỳ/Khoa/Bộ môn)
        Giao_diện-->>Người_dùng: 14 : Hiển thị chi tiết của cấp được chọn
    end
    
    alt Xuất báo cáo Excel
        Người_dùng->>Giao_diện: 15 : Nhấn nút "Xuất Excel"
        Giao_diện->>Hệ_thống: 16 : Gửi yêu cầu xuất dữ liệu ra Excel
        Hệ_thống->>CSDL: 17 : Truy vấn dữ liệu thống kê đầy đủ
        CSDL-->>Hệ_thống: 18 : Trả về dữ liệu thống kê
        Hệ_thống-->>Người_dùng: 19 : Tạo và tải xuống file Excel
    end
    end
```

**Mô tả**: Sơ đồ tuần tự mô tả quy trình thống kê danh sách đăng ký biên soạn trong hệ thống ngân hàng câu hỏi. Quá trình bắt đầu khi người dùng (quản trị viên hoặc nhân viên ĐBCL&KT) truy cập trang thống kê. Hệ thống hiển thị giao diện thống kê với cấu trúc phân cấp theo năm học, học kỳ, khoa và bộ môn. Người dùng có thể áp dụng các bộ lọc (chọn khoa, bộ môn, năm học, học kỳ) để xem dữ liệu thống kê theo yêu cầu. Khi áp dụng bộ lọc, hệ thống sẽ truy vấn lại cơ sở dữ liệu và hiển thị kết quả đã lọc. Người dùng cũng có thể mở rộng từng cấp trong cấu trúc phân cấp để xem chi tiết. Ngoài ra, hệ thống còn cung cấp chức năng xuất dữ liệu thống kê ra file Excel để người dùng có thể tải xuống và sử dụng cho các mục đích khác. 