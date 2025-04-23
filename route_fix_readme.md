# Hướng dẫn khắc phục lỗi route yêu cầu tham số

## Lỗi phổ biến

```
Uncaught (in promise) Error: Ziggy error: 'id' parameter is required for route 'tbm.ctdsdangky.index'.
```

Lỗi này xảy ra khi một route yêu cầu tham số nhưng không được cung cấp khi sử dụng hàm `route()` trong JavaScript.

## Nguyên nhân

1. Trong file `web.php`, route được định nghĩa yêu cầu tham số, ví dụ:
```php
Route::get('/tbm/ctdsdangky/{id}', [CTDSDangKyController::class, 'index'])->name('tbm.ctdsdangky.index');
```

2. Trong AppLayout, route được sử dụng không có tham số:
```javascript
{ name: 'Chi tiết đăng ký', href: route('tbm.ctdsdangky.index'), current: route().current('tbm.ctdsdangky.*') }
```

## Cách khắc phục

### 1. Kiểm tra định nghĩa route trong web.php

Xem file `routes/web.php` để hiểu các tham số bắt buộc cho route:

```php
// Ví dụ route yêu cầu tham số id
Route::get('/tbm/ctdsdangky/{id}', [CTDSDangKyController::class, 'index'])->name('tbm.ctdsdangky.index');
```

### 2. Cập nhật AppLayout.vue

Sửa lại logic tạo menu navigation để đảm bảo tham số được cung cấp:

```javascript
else if (props.role === 'tbm') {
  // Lấy id bộ môn của người dùng nếu có
  const boMonId = user && user.bo_mon_id ? user.bo_mon_id : null;
  
  navigation.value = [
    { name: 'Dashboard', href: route('tbm.dashboard'), current: route().current('tbm.dashboard') },
    // Đảm bảo bạn có id bộ môn trước khi tạo route có tham số id
    ...boMonId ? [
      { name: 'Đăng ký', href: route('tbm.dsdangky.index'), current: route().current('tbm.dsdangky.*') },
      // Đưa id vào route yêu cầu tham số
      { name: 'Chi tiết đăng ký', href: route('tbm.ctdsdangky.index', { id: boMonId }), current: route().current('tbm.ctdsdangky.*') }
    ] : [
      // Nếu không có id, có thể hiển thị link rỗng hoặc ẩn đi
      { name: 'Đăng ký', href: '#', current: false }
    ],
    // Các menu khác cho tbm
  ];
}
```

### 3. Đảm bảo thông tin người dùng được chia sẻ

Kiểm tra `HandleInertiaRequests.php` để đảm bảo thông tin cần thiết của người dùng (như bo_mon_id) được chia sẻ:

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
                'bo_mon_id' => $request->user()->bo_mon_id, // Thêm id bộ môn
                // Thêm các thông tin cần thiết khác
            ] : null,
        ],
        // Dữ liệu chia sẻ khác
    ]);
}
```

### 4. Thêm mã debug để kiểm tra

Thêm các dòng log để kiểm tra dữ liệu trong quá trình chạy:

```javascript
// Debug
console.log('Role:', props.role);
console.log('User:', user);
console.log('Navigation:', navigation.value);
```

### 5. Xử lý các trường hợp đặc biệt

Nếu tham số route là động và phụ thuộc vào ngữ cảnh người dùng, bạn có thể:

1. **Ẩn menu** nếu không có tham số cần thiết
2. **Dùng route mặc định** hoặc route không yêu cầu tham số
3. **Tạo trình xử lý click** thay vì dùng href trực tiếp:

```vue
<Link 
  @click.prevent="handleNavigation(item)" 
  href="#"
  :class="[item.current ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white', 
    'rounded-md px-3 py-2 text-sm font-medium']"
>
  {{ item.name }}
</Link>

<script setup>
// ...
const handleNavigation = (item) => {
  if (item.requiresParams && !hasRequiredParams) {
    // Hiển thị thông báo hoặc xử lý khác
    alert('Không thể truy cập trang này');
  } else {
    // Chuyển hướng đến trang
    window.location = item.href;
  }
};
</script>
``` 