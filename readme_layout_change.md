# Hướng dẫn thay đổi Layout và hiển thị thông tin người dùng

## Mục đích
- Thống nhất layout cho toàn hệ thống bằng cách sử dụng `AppLayout` duy nhất với các role khác nhau
- Hiển thị thông tin người dùng đăng nhập trong layout chung

## Cấu trúc và nguyên tắc
1. Sử dụng một layout duy nhất `AppLayout.vue` cho toàn bộ hệ thống
2. Sử dụng prop `role` để xác định vai trò của người dùng và hiển thị menu tương ứng
3. Thông tin người dùng được lấy từ `usePage().props.auth.user` (Inertia.js)

## Các file quan trọng
1. `AppLayout.vue` - Layout chính cho toàn bộ hệ thống
   - Nhận prop `role` để hiển thị menu tương ứng
   - Hiển thị thông tin người dùng đăng nhập từ `usePage().props.auth.user`
   
2. `resources/js/app.js` - Cấu hình Inertia shared data
   - Đảm bảo thông tin người dùng được chia sẻ qua `auth.user`

3. `app/Http/Middleware/HandleInertiaRequests.php` - Middleware chia sẻ dữ liệu
   - Cấu hình để chia sẻ thông tin người dùng đăng nhập với tất cả các trang

## Hướng dẫn triển khai

### 1. Cập nhật `HandleInertiaRequests.php` để chia sẻ dữ liệu người dùng
```php
public function share(Request $request): array
{
    return array_merge(parent::share($request), [
        'auth' => [
            'user' => $request->user() ? [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
                'role' => $request->user()->role,
                // Thêm các thông tin cần thiết khác
            ] : null,
        ],
        // Dữ liệu chia sẻ khác
    ]);
}
```

### 2. Tạo file `AppLayout.vue` với nội dung từ ví dụ `AppLayout_example.vue`
- Sử dụng prop `role` để hiển thị menu tương ứng
- Hiển thị thông tin người dùng từ `usePage().props.auth.user`

### 3. Cập nhật tất cả các file Vue để sử dụng `AppLayout` với role tương ứng
- Sử dụng script `update-layouts.js` để tự động thay đổi
- Hoặc thực hiện thủ công theo hướng dẫn trong `guide_layout_replacement.md`

### 4. Kiểm tra và cập nhật
- Đảm bảo tất cả các trang hiển thị đúng layout và menu
- Kiểm tra thông tin người dùng hiển thị đúng trên tất cả các trang

## Lưu ý quan trọng
1. Đảm bảo các route được định nghĩa đúng trong file JavaScript (xem `jsconfig.json` hoặc `tsconfig.json`)
2. Thêm route helper trong file `app.js` nếu cần thiết
3. Tất cả các component layout cũ có thể bị xóa sau khi hoàn tất quá trình chuyển đổi

## Ví dụ mẫu
Xem các file sau để có ví dụ tham khảo:
- `admin_example.vue` - Mẫu cho trang Admin
- `tbm_example.vue` - Mẫu cho trang Trưởng Bộ Môn
- `tk_example.vue` - Mẫu cho trang Trưởng Khoa
- `quality_example.vue` - Mẫu cho trang Phòng Đảm Bảo Chất Lượng
- `AppLayout_example.vue` - Mẫu cho layout chung 