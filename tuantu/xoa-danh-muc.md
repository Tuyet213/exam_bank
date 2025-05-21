```mermaid
%%{init: {'theme': 'base', 'themeVariables': { 'primaryColor': '#ffffff', 'primaryBorderColor': '#000000', 'primaryTextColor': '#000000', 'noteBackgroundColor': '#ffffff', 'noteBorderColor': '#000000' }}}%%

sequenceDiagram
    participant QTV as Quản trị viên
    participant GD as Giao diện
    participant HT as Hệ thống
    participant CSDL as Cơ sở dữ liệu

    rect rgb(255, 255, 255)
        QTV->>GD: 1. Truy cập danh sách thông tin
        GD-->>QTV: 2. Hiển thị danh sách các thông tin
        QTV->>GD: 3. Nhấn nút "Xóa" trên mục cần xóa
        GD-->>QTV: 4. Hiển thị hộp thoại xác nhận
        QTV->>GD: 5. Xác nhận xóa
        GD->>HT: 6. Gửi yêu cầu xóa
        HT->>CSDL: 7. Kiểm tra ràng buộc dữ liệu
        
        alt Có thể xóa
            CSDL-->>HT: 8. [OK] Xác nhận có thể xóa
            HT->>CSDL: 9. Cập nhật trạng thái thông tin (able = false)
            CSDL-->>HT: 10. Cập nhật thành công
            HT-->>GD: 11. Thông báo xóa thành công
            GD-->>QTV: 12. Hiển thị thông báo "Xóa thành công"
            GD-->>QTV: 13. Cập nhật danh sách (loại bỏ mục đã xóa)
        else Không thể xóa
            CSDL-->>HT: 8. [NotOK] Không thể xóa do ràng buộc
            HT-->>GD: 9. Trả về lỗi
            GD-->>QTV: 10. Hiển thị thông báo lỗi
        end
    end
```

**Mô tả:** Quản trị viên truy cập danh sách thông tin cần quản lý. Hệ thống hiển thị danh sách các thông tin hiện có. Quản trị viên nhấn nút "Xóa" trên mục cần xóa. Hệ thống hiển thị hộp thoại xác nhận để tránh xóa nhầm. Sau khi quản trị viên xác nhận, hệ thống gửi yêu cầu xóa và kiểm tra các ràng buộc dữ liệu. Nếu thông tin có thể xóa (không có ràng buộc với dữ liệu khác), hệ thống sẽ cập nhật trạng thái của thông tin (đánh dấu là đã xóa thay vì xóa hoàn toàn) và hiển thị thông báo thành công. Ngược lại, nếu thông tin không thể xóa do ràng buộc với dữ liệu khác, hệ thống sẽ hiển thị thông báo lỗi tương ứng. 