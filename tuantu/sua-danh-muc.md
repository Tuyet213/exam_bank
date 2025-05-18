```mermaid
%%{init: {'theme': 'base', 'themeVariables': { 'primaryColor': '#ffffff', 'primaryBorderColor': '#000000', 'primaryTextColor': '#000000', 'noteBackgroundColor': '#ffffff', 'noteBorderColor': '#000000' }}}%%

sequenceDiagram
    participant QTV as Quản trị viên
    participant GD as Giao diện
    participant HT as Hệ thống
    participant CSDL as Cơ sở dữ liệu

    rect rgb(255, 255, 255)
        QTV->>GD: 1. Truy cập mục "Quản lý hệ thống"
        GD-->>QTV: 2. Hiển thị danh sách các loại thông tin
        QTV->>GD: 3. Chọn "Sửa thông tin cơ bản"
        GD->>HT: 4. Yêu cầu thông tin hiện tại
        HT->>CSDL: 5. Truy vấn thông tin
        CSDL-->>HT: 6. Trả về thông tin hiện tại
        HT-->>GD: 7. Gửi thông tin hiện tại
        GD-->>QTV: 8. Hiển thị form với thông tin hiện tại
        QTV->>GD: 9. Cập nhật thông tin
        QTV->>GD: 10. Nhấn "Lưu"
        GD->>HT: 11. Gửi thông tin đã cập nhật
        HT->>CSDL: 12. Kiểm tra thông tin
        
        alt Thông tin hợp lệ
            CSDL-->>HT: 13. [OK] Xác nhận hợp lệ
            HT->>CSDL: 14. Lưu thay đổi
            CSDL-->>HT: 15. Lưu thành công
            HT-->>GD: 16. Thông báo thành công
            GD-->>QTV: 17. Hiển thị thông báo "Sửa thành công"
        else Thông tin không hợp lệ
            CSDL-->>HT: 13. [NotOK] Thông tin không hợp lệ
            HT-->>GD: 14. Trả về lỗi
            GD-->>QTV: 15. Hiển thị thông báo lỗi
        end
    end
```

**Mô tả:** Quản trị viên truy cập mục "Quản lý hệ thống", hệ thống hiển thị danh sách các loại thông tin. Quản trị viên chọn tính năng "Sửa thông tin cơ bản". Hệ thống truy vấn thông tin hiện tại từ cơ sở dữ liệu và hiển thị form với dữ liệu đã được điền sẵn. Quản trị viên cập nhật thông tin cần sửa và nhấn nút "Lưu". Hệ thống kiểm tra tính hợp lệ của dữ liệu. Nếu thông tin hợp lệ, hệ thống lưu thay đổi vào cơ sở dữ liệu và hiển thị thông báo thành công. Ngược lại, nếu thông tin không hợp lệ (thiếu trường bắt buộc, định dạng sai, v.v.), hệ thống sẽ hiển thị thông báo lỗi tương ứng. 