# Hướng dẫn thay đổi Layout cho các file Vue

## Mục đích
Thay thế tất cả các Layout cũ (AdminLayout, TBMLayout, TKLayout, QualityLayout) bằng AppLayout với thuộc tính role phù hợp.

## Ánh xạ layout
- AdminLayout -> AppLayout role="admin"
- TBMLayout -> AppLayout role="tbm" 
- TKLayout -> AppLayout role="tk"
- QualityLayout -> AppLayout role="dbcl"

## Các bước thực hiện

### 1. Thay đổi cú pháp import
Tìm dòng code import layout cũ:
```vue
import AdminLayout from '@/Layouts/AdminLayout.vue';
```

Thay thế bằng:
```vue
import AppLayout from '@/Layouts/AppLayout.vue';
```

### 2. Thay đổi thẻ layout
Tìm thẻ mở layout cũ:
```vue
<AdminLayout>
```

Thay thế bằng:
```vue
<AppLayout role="admin">
```

Tìm thẻ đóng layout cũ:
```vue
</AdminLayout>
```

Thay thế bằng:
```vue
</AppLayout>
```

### 3. Các thư mục cần xử lý
- `resources/js/Pages/Admin/**/*.vue`
- `resources/js/Pages/TBM/**/*.vue`
- `resources/js/Pages/TK/**/*.vue`
- `resources/js/Pages/QualityOffice/**/*.vue`

### 4. Mẫu file đã được chỉnh sửa

#### Ví dụ AdminLayout:
```vue
// Trước khi sửa
<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
// các import khác...

// code...
</script>

<template>
  <AdminLayout>
    <!-- Nội dung -->
  </AdminLayout>
</template>
```

```vue
// Sau khi sửa
<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
// các import khác...

// code...
</script>

<template>
  <AppLayout role="admin">
    <!-- Nội dung -->
  </AppLayout>
</template>
```

### 5. Quy trình kiểm tra sau thay đổi
1. Mở từng file đã thay đổi để kiểm tra giao diện
2. Xác nhận chức năng của mỗi trang vẫn hoạt động bình thường
3. Kiểm tra xem layout mới có hiển thị đúng cho từng role hay không

### 6. Sau khi hoàn tất
Khi tất cả các file đã được cập nhật và kiểm tra:
1. Có thể xóa các file layout cũ: AdminLayout.vue, TBMLayout.vue, TKLayout.vue, QualityLayout.vue
2. Cập nhật README hoặc tài liệu dự án về sự thay đổi này 