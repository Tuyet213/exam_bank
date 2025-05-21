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
    Người_dùng->>Giao_diện: 1 : Vào trang quản lý ma trận đề thi
    Giao_diện->>Hệ_thống: 2 : Gửi yêu cầu lấy danh sách ma trận
    Hệ_thống->>CSDL: 3 : Truy vấn danh sách học phần có ma trận
    CSDL-->>Hệ_thống: 4 : Trả về danh sách học phần
    Hệ_thống-->>Giao_diện: 5 : Trả về dữ liệu
    Giao_diện-->>Người_dùng: 6 : Hiển thị danh sách ma trận
    
    alt Tìm kiếm học phần
        Người_dùng->>Giao_diện: 7 : Nhập từ khóa tìm kiếm
        Giao_diện->>Hệ_thống: 8 : Gửi yêu cầu tìm kiếm
        Hệ_thống->>CSDL: 9 : Truy vấn học phần theo từ khóa
        CSDL-->>Hệ_thống: 10 : Trả về kết quả tìm kiếm
        Hệ_thống-->>Giao_diện: 11 : Cập nhật danh sách
        Giao_diện-->>Người_dùng: 12 : Hiển thị kết quả tìm kiếm
    end
    
    alt Xuất đề thi
        Người_dùng->>Giao_diện: 13 : Chọn "Xuất đề thi" của học phần
        Giao_diện->>Hệ_thống: 14 : Gửi yêu cầu xuất đề thi
        Hệ_thống->>CSDL: 15 : Truy vấn thông tin ma trận và câu hỏi
        CSDL-->>Hệ_thống: 16 : Trả về dữ liệu
        Hệ_thống-->>Người_dùng: 17 : Tạo và tải xuống file đề thi
    end
    end
```

**Mô tả**: Sơ đồ tuần tự mô tả quy trình quản lý ma trận đề thi trong hệ thống ngân hàng câu hỏi. Quá trình bắt đầu khi người dùng truy cập trang quản lý ma trận đề thi, hệ thống sẽ hiển thị danh sách các học phần đã có ma trận đề thi. Người dùng có thể tìm kiếm học phần theo tên hoặc mã học phần, hệ thống sẽ truy vấn cơ sở dữ liệu và hiển thị kết quả tìm kiếm. Ngoài ra, người dùng có thể chọn chức năng xuất đề thi cho một học phần cụ thể, hệ thống sẽ tạo đề thi dựa trên ma trận và ngân hàng câu hỏi, sau đó cung cấp file đề thi để người dùng tải xuống. 