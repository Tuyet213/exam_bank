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
        TBM->>GD: 3. Chọn tạo biên bản họp
        GD->>HT: 4. Yêu cầu thông tin chi tiết đăng ký
        HT->>CSDL: 5. Truy vấn thông tin chi tiết và danh sách viên chức
        CSDL-->>HT: 6. Trả về dữ liệu
        HT-->>GD: 7. Hiển thị form tạo biên bản
        TBM->>GD: 8. Nhập thông tin: thời gian, địa điểm
        TBM->>GD: 9. Chọn người tham gia theo nhiệm vụ
        TBM->>GD: 10. Nhấn "Lưu tất cả"
        GD->>HT: 11. Gửi dữ liệu biên bản
        HT->>CSDL: 12. Kiểm tra thông tin
        
        alt Thông tin hợp lệ
            CSDL-->>HT: 13. [OK] Xác nhận hợp lệ
            HT->>CSDL: 14. Lưu biên bản họp với trạng thái "Draft"
            CSDL-->>HT: 15. Lưu thành công
            HT-->>GD: 16. Thông báo thành công
            GD-->>TBM: 17. Hiển thị thông báo "Tạo biên bản thành công"
        else Thông tin không hợp lệ
            CSDL-->>HT: 13. [NotOK] Thông tin không hợp lệ
            HT-->>GD: 14. Trả về lỗi
            GD-->>TBM: 15. Hiển thị thông báo lỗi
        end
    end
```

**Mô tả:** Trưởng bộ môn truy cập trang danh sách đăng ký và chọn chức năng tạo biên bản họp. Hệ thống truy vấn thông tin chi tiết đăng ký, danh sách viên chức và nhiệm vụ từ cơ sở dữ liệu. Giao diện hiển thị form tạo biên bản với thông tin chi tiết đăng ký đã được chọn. Trưởng bộ môn nhập các thông tin cần thiết như thời gian, địa điểm và chọn các thành viên tham gia theo nhiệm vụ (Chủ tịch, Thư ký, Cán bộ phản biện, Ủy viên). Khi hoàn tất, trưởng bộ môn nhấn nút "Lưu tất cả". Hệ thống kiểm tra tính hợp lệ của dữ liệu. Nếu thông tin hợp lệ, hệ thống lưu biên bản họp với trạng thái "Draft" và hiển thị thông báo thành công. Ngược lại, nếu thông tin không hợp lệ (thiếu trường bắt buộc, định dạng sai, v.v.), hệ thống sẽ hiển thị thông báo lỗi tương ứng. 