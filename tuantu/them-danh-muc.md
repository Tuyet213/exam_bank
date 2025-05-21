```mermaid
%%{init: {'theme': 'base', 'themeVariables': { 'primaryColor': '#ffffff', 'primaryBorderColor': '#000000', 'primaryTextColor': '#000000', 'noteBackgroundColor': '#ffffff', 'noteBorderColor': '#000000' }}}%%

sequenceDiagram
    participant QTV as Quản trị viên
    participant GD as Giao diện
    participant HT as Hệ thống
    participant CSDL as Cơ sở dữ liệu

    rect rgb(255, 255, 255)
        QTV->>GD: 1. Nhấn nút "Thêm mới"
        GD-->>QTV: 2. Hiển thị form nhập thông tin
        QTV->>GD: 3. Nhập thông tin
        QTV->>GD: 4. Nhấn nút "Lưu"
        GD->>HT: 5. Gửi thông tin
        HT->>CSDL: 6. Kiểm tra thông tin
        
        alt Thông tin hợp lệ
            CSDL-->>HT: 7. [OK] Xác nhận hợp lệ
            HT->>CSDL: 8. Lưu thông tin
            CSDL-->>HT: 9. Lưu thành công
            HT-->>GD: 10. Thông báo thành công
            GD-->>QTV: 11. Hiển thị thông báo "Thêm thành công"
        else Thông tin không hợp lệ
            CSDL-->>HT: 7. [NotOK] Thông tin không hợp lệ
            HT-->>GD: 8. Trả về lỗi
            GD-->>QTV: 9. Hiển thị thông báo lỗi
        end
    end
```

**Mô tả:** Quản trị viên nhấn nút "Thêm mới" trên giao diện để thêm một danh mục mới vào hệ thống. Giao diện hiển thị form nhập thông tin. Quản trị viên nhập các thông tin cần thiết và nhấn nút "Lưu". Hệ thống tiếp nhận thông tin và kiểm tra tính hợp lệ của dữ liệu trong cơ sở dữ liệu. Nếu thông tin hợp lệ, hệ thống sẽ lưu thông tin vào cơ sở dữ liệu và hiển thị thông báo thành công. Ngược lại, nếu thông tin không hợp lệ (thiếu trường bắt buộc, mã trùng lặp, v.v.), hệ thống sẽ hiển thị thông báo lỗi tương ứng. 