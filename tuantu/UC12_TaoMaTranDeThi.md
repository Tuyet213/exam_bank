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
    participant Người_dùng as Giảng viên
    participant Giao_diện as Giao diện
    participant Hệ_thống as Hệ thống
    participant CSDL as Cơ sở dữ liệu
    rect rgb(255, 255, 255)
    Người_dùng->>Giao_diện: 1 : Truy cập chức năng tạo ma trận
    Giao_diện->>Hệ_thống: 2 : Gửi yêu cầu lấy danh sách học phần
    Hệ_thống->>CSDL: 3 : Truy vấn học phần chưa có ma trận
    CSDL-->>Hệ_thống: 4 : Trả về danh sách học phần
    Hệ_thống-->>Giao_diện: 5 : Trả về dữ liệu
    Giao_diện-->>Người_dùng: 6 : Hiển thị form tạo ma trận
    
    Người_dùng->>Giao_diện: 7 : Chọn học phần từ danh sách
    Giao_diện->>Hệ_thống: 8 : Gửi yêu cầu lấy chi tiết học phần
    Hệ_thống->>CSDL: 9 : Truy vấn chương và chuẩn đầu ra
    CSDL-->>Hệ_thống: 10 : Trả về dữ liệu chương và CDR
    Hệ_thống-->>Giao_diện: 11 : Hiển thị bảng ma trận
    Giao_diện-->>Người_dùng: 12 : Hiển thị bảng nhập liệu ma trận
    
    Người_dùng->>Giao_diện: 13 : Nhập số lượng câu hỏi cho từng mức độ
    Người_dùng->>Giao_diện: 14 : Nhấn nút "Lưu ma trận"
    Giao_diện->>Hệ_thống: 15 : Gửi dữ liệu ma trận
    Hệ_thống->>CSDL: 16 : Lưu thông tin ma trận vào CSDL
    CSDL-->>Hệ_thống: 17 : Xác nhận lưu thành công
    Hệ_thống-->>Người_dùng: 18 : Thông báo tạo ma trận thành công
    end
```

**Mô tả**: Sơ đồ tuần tự mô tả quy trình tạo ma trận đề thi trong hệ thống ngân hàng câu hỏi. Quá trình bắt đầu khi giảng viên truy cập chức năng tạo ma trận đề thi. Hệ thống hiển thị form với danh sách các học phần chưa có ma trận. Giảng viên chọn một học phần, hệ thống sẽ tải dữ liệu về các chương và chuẩn đầu ra của học phần đó, hiển thị bảng ma trận để giảng viên nhập số lượng câu hỏi cho từng chương, chuẩn đầu ra và mức độ (Dễ, Trung bình, Khó). Sau khi giảng viên nhập đủ thông tin và nhấn nút "Lưu ma trận", hệ thống sẽ kiểm tra tính hợp lệ của dữ liệu rồi lưu ma trận vào cơ sở dữ liệu. Khi lưu thành công, hệ thống sẽ hiển thị thông báo xác nhận. 