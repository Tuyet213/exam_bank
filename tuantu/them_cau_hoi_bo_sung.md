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
    Người_dùng->>Giao_diện: 1 : Truy cập chức năng "Danh sách học phần"
    Giao_diện->>Hệ_thống: 2 : Gửi yêu cầu lấy danh sách học phần
    Hệ_thống->>CSDL: 3 : Truy vấn danh sách học phần giảng viên được phân công
    CSDL-->>Hệ_thống: 4 : Trả về danh sách học phần
    Hệ_thống-->>Giao_diện: 5 : Hiển thị danh sách học phần
    Người_dùng->>Giao_diện: 6 : Chọn học phần cần thêm câu hỏi
    Giao_diện->>Hệ_thống: 7 : Chuyển đến trang tạo câu hỏi mới
    Hệ_thống->>CSDL: 8 : Lấy thông tin chương và chuẩn đầu ra của học phần
    CSDL-->>Hệ_thống: 9 : Trả về dữ liệu chương và chuẩn đầu ra
    Hệ_thống-->>Giao_diện: 10 : Hiển thị form tạo câu hỏi mới
    Người_dùng->>Giao_diện: 11 : Nhập thông tin câu hỏi và lưu
    Giao_diện->>Hệ_thống: 12 : Gửi dữ liệu câu hỏi mới
    Hệ_thống->>CSDL: 13 : Lưu câu hỏi vào ngân hàng câu hỏi
    CSDL-->>Hệ_thống: 14 : Xác nhận lưu thành công
    Hệ_thống-->>Giao_diện: 15 : Hiển thị thông báo thành công
    end
```

**Mô tả**: Sơ đồ tuần tự mô tả quy trình giảng viên thêm câu hỏi bổ sung vào ngân hàng câu hỏi thi. Đầu tiên, giảng viên truy cập vào chức năng "Danh sách học phần" để xem các học phần được phân công. Hệ thống truy vấn cơ sở dữ liệu và hiển thị danh sách học phần. Giảng viên chọn học phần cần thêm câu hỏi, hệ thống chuyển đến trang tạo câu hỏi mới và lấy thông tin về các chương và chuẩn đầu ra của học phần đó. Sau khi form tạo câu hỏi được hiển thị, giảng viên nhập thông tin câu hỏi bao gồm nội dung, đáp án, điểm số, mức độ, chương và chuẩn đầu ra. Cuối cùng, hệ thống lưu câu hỏi vào cơ sở dữ liệu và hiển thị thông báo thành công. 