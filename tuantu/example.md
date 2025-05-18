```mermaid
%%{init: {'theme': 'base', 'themeVariables': { 'primaryColor': '#ffffff', 'primaryBorderColor': '#000000', 'primaryTextColor': '#000000', 'noteBackgroundColor': '#ffffff', 'noteBorderColor': '#000000' }}}%%

sequenceDiagram
    participant Người_dùng as Người dùng
    participant Giao_diện as Giao diện
    participant Hệ_thống as Hệ thống
    participant CSDL as Cơ sở dữ liệu
    rect rgb(255, 255, 255)
    Người_dùng->>Giao_diện: 1 : Mở giao diện
    Giao_diện-->>Người_dùng: 2 : Hiển thị giao diện đăng nhập
    Người_dùng->>Giao_diện: 3 : Nhập email/Mật khẩu
    Giao_diện->>Hệ_thống: 4 : Gửi email/mật khẩu
    Hệ_thống->>CSDL: 6 : Xác thực thông tin đăng nhập
    CSDL-->>Hệ_thống: 5 : Kiểm tra tài khoản
    CSDL-->>Hệ_thống: 7 : [OK] Tài khoản hợp lệ
    Hệ_thống-->>Giao_diện: 8 : [OK] Tạo phiên đăng nhập
    Giao_diện-->>Người_dùng: 9 : Chuyển hướng đến trang chính
    
    CSDL-->>Hệ_thống: 10 : [NotOK] Tài khoản không hợp lệ
    Hệ_thống-->>Giao_diện: 
    Giao_diện-->>Người_dùng: 11 : [Not OK] Hiển thị thông báo
    end
```

**Mô tả**: Người dùng mở giao diện ứng dụng và nhập email, password để đăng nhập vào hệ thống. Thông tin đăng nhập được gửi đến hệ thống. Hệ thống truy xuất cơ sở dữ liệu, kiểm tra thông tin đăng nhập. Nếu tài khoản hợp lệ, khớp với dữ liệu có trong cơ sở dữ liệu thì tạo mới phiên hoạt động cho người dùng và chuyển người dùng về trang chủ. Hoặc nếu tài khoản không hợp lệ (không có bản ghi tương ứng trong cơ sở dữ liệu) thì hiển thị thông báo đăng nhập không thành công.
