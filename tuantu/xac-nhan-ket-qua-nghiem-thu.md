```mermaid
%%{init: {'theme': 'base', 'themeVariables': { 'primaryColor': '#ffffff', 'primaryBorderColor': '#000000', 'primaryTextColor': '#000000', 'noteBackgroundColor': '#ffffff', 'noteBorderColor': '#000000' }}}%%
sequenceDiagram
    participant NVĐBCL as Nhân viên ĐBCL&KT
    participant GD as Giao diện
    participant HT as Hệ thống
    participant CSDL as Cơ sở dữ liệu
    rect rgb(255, 255, 255)
        NVĐBCL->>GD: 1. Truy cập và tìm kiếm biên bản
        GD->>HT: 2. Gửi yêu cầu lọc dữ liệu
        HT->>CSDL: 3. Truy vấn dữ liệu
        CSDL-->>GD: 4. Hiển thị danh sách biên bản
        NVĐBCL->>GD: 5. Xem chi tiết biên bản
        GD->>CSDL: 6. Lấy và hiển thị thông tin chi tiết
        
        alt Phê duyệt biên bản
            NVĐBCL->>GD: 7. Chọn phê duyệt biên bản
            GD->>CSDL: 8. Cập nhật trạng thái "Đã duyệt"
            CSDL-->>GD: 9. Xác nhận và gửi thông báo
            GD-->>NVĐBCL: 10. Hiển thị kết quả phê duyệt
        else Từ chối biên bản
            NVĐBCL->>GD: 7. Chọn từ chối và nhập lý do
            GD->>CSDL: 8. Cập nhật trạng thái "Đã từ chối"
            CSDL-->>GD: 9. Xác nhận và gửi thông báo
            GD-->>NVĐBCL: 10. Hiển thị kết quả từ chối
        end
    end
```

**Mô tả:** Nhân viên Phòng Đảm bảo chất lượng & Khảo thí truy cập vào danh sách biên bản nghiệm thu. Hệ thống hiển thị danh sách các biên bản được tổ chức theo năm học, học kỳ, khoa và bộ môn. Nhân viên có thể sử dụng bộ lọc để tìm kiếm biên bản cụ thể theo nhiều tiêu chí. Sau khi tìm thấy biên bản cần xem xét, nhân viên nhấn vào nút xem chi tiết để xem thông tin đầy đủ về biên bản nghiệm thu. Sau khi xem xét nội dung biên bản, nhân viên quyết định phê duyệt hoặc từ chối. Nếu phê duyệt, hệ thống cập nhật trạng thái biên bản thành "Đã duyệt" và gửi email thông báo đến Trưởng bộ môn. Nếu từ chối, nhân viên phải nhập lý do từ chối, sau đó hệ thống cập nhật trạng thái biên bản thành "Đã từ chối" và gửi email kèm lý do từ chối đến Trưởng bộ môn. 