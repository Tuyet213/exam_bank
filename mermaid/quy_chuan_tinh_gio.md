```mermaid
block-beta
    columns 5
    
    A(("Bắt đầu")) space B["Trưởng bộ môn\nđăng nhập"] space C["Truy cập danh sách\nbiên bản và chọn\n'Thêm giờ'"]
    
    space:5
    
    F["Chọn quy chuẩn\ntính giờ cho\nngười biên soạn"] space E["Hệ thống tính toán\nphân bổ giờ"] space D["Hệ thống hiển thị\ngiao diện nhập"]
    
    space:5
    
    G["Nhấn nút\n'Cập nhật'"] space H{"Kiểm tra\nthông tin"} space J["Hiển thị\nthông báo lỗi"] 
    
    space:5
    
    L(("Kết thúc")) space K["Số giờ quy đổi\nđược lưu vào\nhệ thống"] space I["Thông báo\nthành công"]
    
    A --> B
    B --> C
    C --> D
    D --> E
    E --> F
    F --> G
    G --> H
    H -- "Hợp lệ" --> I
    H -- "Không hợp lệ" --> J
    J --> F
    I --> K
    K --> L

    classDef WHITE fill: #fff
    class A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,X,T,U,V,W,X,Y,Z WHITE
``` 