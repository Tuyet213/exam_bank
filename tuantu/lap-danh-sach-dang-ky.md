```mermaid
%%{init: {'theme': 'base', 'themeVariables': { 'primaryColor': '#ffffff', 'primaryBorderColor': '#000000', 'primaryTextColor': '#000000', 'noteBackgroundColor': '#ffffff', 'noteBorderColor': '#000000' }}}%%

sequenceDiagram
    participant TBM as Trưởng bộ môn
    participant GD as Giao diện
    participant HT as Hệ thống
    participant CSDL as Cơ sở dữ liệu

    rect rgb(255, 255, 255)
        TBM->>GD: 1. Truy cập trang danh sách đăng ký
        GD-->>TBM: 2. Hiển thị danh sách đăng ký
        TBM->>GD: 3. Chọn tạo danh sách đăng ký mới
        HT->>CSDL: 4. Truy vấn danh sách học phần, giảng viên
        CSDL-->>HT: 5. Trả về dữ liệu
        HT-->>GD: 6. Hiển thị form nhập thông tin
        TBM->>GD: 7. Nhập thông tin: học kỳ, năm học
        TBM->>GD: 8. Thêm chi tiết đăng ký với thông tin: học phần, giảng viên biên soạn, loại ngân hàng
        TBM->>GD: 9. Nhấn "Lưu"
        GD->>HT: 10. Gửi dữ liệu đăng ký
        HT->>CSDL: 11. Kiểm tra thông tin
        
        alt Thông tin hợp lệ
            CSDL-->>HT: 12. [OK] Xác nhận hợp lệ
            HT->>CSDL: 13. Lưu danh sách đăng ký với trạng thái "Draft"
            CSDL-->>HT: 14. Lưu thành công
            HT-->>GD: 15. Thông báo thành công
            GD-->>TBM: 16. Hiển thị thông báo "Thêm thành công"
        else Thông tin không hợp lệ
            CSDL-->>HT: 12. [NotOK] Thông tin không hợp lệ
            HT-->>GD: 13. Trả về lỗi
            GD-->>TBM: 14. Hiển thị thông báo lỗi
        end
    end
```

**Mô tả:** Trưởng bộ môn truy cập trang danh sách đăng ký và chọn tạo danh sách đăng ký mới. Hệ thống truy vấn và hiển thị thông tin cần thiết để tạo đăng ký như danh sách học phần, giảng viên. Trưởng bộ môn nhập các thông tin cơ bản: học kỳ, năm học, sau đó thêm chi tiết đăng ký gồm học phần, giảng viên biên soạn, loại ngân hàng (câu hỏi hoặc đề thi) và các thông tin khác như số lượng, hình thức thi. Khi hoàn thành, trưởng bộ môn nhấn nút "Lưu". Hệ thống kiểm tra tính hợp lệ của dữ liệu. Nếu thông tin hợp lệ, hệ thống lưu danh sách đăng ký với trạng thái "Draft" và hiển thị thông báo thành công. Ngược lại, nếu thông tin không hợp lệ (thiếu trường bắt buộc, định dạng sai, v.v.), hệ thống sẽ hiển thị thông báo lỗi tương ứng. 