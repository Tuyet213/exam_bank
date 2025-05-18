```mermaid
%%{init: {'theme': 'base', 'themeVariables': { 'primaryColor': '#ffffff', 'primaryBorderColor': '#000000', 'primaryTextColor': '#000000', 'noteBackgroundColor': '#ffffff', 'noteBorderColor': '#000000' }}}%%
sequenceDiagram
    participant TBM as Trưởng bộ môn
    participant GD as Giao diện
    participant HT as Hệ thống
    participant CSDL as Cơ sở dữ liệu
    rect rgb(255, 255, 255)
        TBM->>GD: 1. Truy cập danh sách và chọn "Thêm giờ"
        GD->>CSDL: 2. Lấy thông tin biên bản và giờ quy đổi
        CSDL-->>GD: 3. Hiển thị form nhập giờ
        TBM->>GD: 4. Chọn quy chuẩn và điều chỉnh giờ cho người biên soạn
        GD->>GD: 5. Tính toán và phân bổ giờ tự động
        TBM->>GD: 6. Chọn quy chuẩn và điều chỉnh giờ cho người phản biện
        TBM->>GD: 7. Nhấn "Lưu thay đổi"
        GD->>CSDL: 8. Kiểm tra và lưu dữ liệu
        
        alt Dữ liệu hợp lệ
            CSDL-->>GD: 9. Xác nhận lưu thành công
            GD-->>TBM: 10. Hiển thị thông báo thành công
        else Dữ liệu không hợp lệ
            CSDL-->>GD: 9. Trả về lỗi
            GD-->>TBM: 10. Hiển thị thông báo lỗi
        end
    end
```

**Mô tả:** Trưởng bộ môn truy cập vào danh sách biên bản họp đã được tạo và chọn chức năng "Thêm giờ" cho một biên bản cụ thể. Hệ thống hiển thị form với thông tin chi tiết của biên bản và cho phép trưởng bộ môn chọn quy chuẩn tính giờ cho người biên soạn và người phản biện từ danh sách quy đổi có sẵn. Khi trưởng bộ môn chọn quy chuẩn, hệ thống tự động tính toán và phân bổ giờ cho từng giảng viên biên soạn và thành viên hội đồng. Trưởng bộ môn có thể điều chỉnh số giờ cho từng cá nhân nếu cần thiết, tuy nhiên tổng số giờ phải phù hợp với quy chuẩn đã chọn. Khi hoàn tất, trưởng bộ môn nhấn nút "Lưu thay đổi" để cập nhật thông tin. Hệ thống kiểm tra tính hợp lệ của dữ liệu (tổng số giờ phải khớp với quy định theo quy chuẩn) và lưu thông tin vào cơ sở dữ liệu nếu hợp lệ. Nếu dữ liệu không hợp lệ, hệ thống sẽ hiển thị thông báo lỗi tương ứng. 