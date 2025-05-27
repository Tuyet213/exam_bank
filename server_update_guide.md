# Hướng dẫn cập nhật code trên Server Production

Tài liệu này mô tả các bước cần thực hiện khi cập nhật code từ repository vào server sản phẩm. Tuân thủ các bước này sẽ giúp đảm bảo quá trình cập nhật được thực hiện một cách an toàn và hiệu quả.

## Thông tin hệ thống

- **Hệ điều hành**: Ubuntu 24.04.2 LTS (Noble Numbat)
- **Web Server**: Nginx 1.24.0
- **PHP Version**: 8.3.6 với OPcache
- **PHP-FPM**: 8.3.6 (fpm-fcgi)
- **Database**: MySQL 8.0.42
- **Node.js**: v18.19.1
- **NPM**: 9.2.0
- **Laravel Version**: 11.44.2

## Kiểm tra không gian đĩa

Trước khi cập nhật, hãy đảm bảo có đủ dung lượng đĩa:

```bash
df -h /
```

Nếu phần trăm sử dụng > 85%, hãy cân nhắc dọn dẹp các file tạm hoặc log cũ:

```bash
# Xóa file log cũ nếu cần
sudo find /var/log -name "*.gz" -type f -delete
# Xóa cache composer nếu cần
composer clear-cache
```

## Chuẩn bị

Trước khi tiến hành cập nhật, hãy đảm bảo:

1. Bạn đã sao lưu dữ liệu hiện tại
2. Đã kiểm tra code trên môi trường dev/staging
3. Không có sự thay đổi nào trên server cần được lưu giữ

## Quy trình cập nhật

### 1. Sao lưu dữ liệu

```bash
# Tạo thư mục backup nếu chưa có
mkdir -p ~/backups/$(date +%Y%m%d)

# Sao lưu database
mysqldump -u laravel -p exam_bank > ~/backups/$(date +%Y%m%d)/exam_bank_$(date +%Y%m%d_%H%M%S).sql

# Sao lưu thư mục project (chỉ sao lưu những phần quan trọng để tiết kiệm dung lượng)
rsync -av --exclude="node_modules" --exclude="vendor" --exclude=".git" /var/www/html/exam_bank/ ~/backups/$(date +%Y%m%d)/exam_bank_$(date +%Y%m%d_%H%M%S)/
```

### 2. Pull code mới từ Git

```bash
# Di chuyển đến thư mục project
cd /var/www/html/exam_bank

# Đảm bảo không có thay đổi cục bộ
git status

# Nếu có thay đổi cục bộ cần giữ lại, hãy stash chúng
# git stash

# Pull code mới từ repository
git pull origin main
```

### 3. Cập nhật Dependencies

```bash
# Cập nhật PHP dependencies
composer install --no-dev --optimize-autoloader

# Cập nhật Node.js dependencies và build assets
npm ci
npm run build
```

### 4. Cập nhật Cấu hình Laravel

```bash
# Xóa cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Áp dụng migration (nếu có)
php artisan migrate --force

# Tối ưu Laravel
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 5. Cập nhật quyền truy cập

```bash
# Set quyền cho thư mục storage và bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Set quyền cho public nếu cần
sudo chown -R www-data:www-data public
sudo chmod -R 755 public
```

### 6. Khởi động lại các dịch vụ

```bash
# Khởi động lại PHP-FPM
sudo systemctl restart php8.3-fpm

# Kiểm tra trạng thái PHP-FPM
sudo systemctl status php8.3-fpm

# Khởi động lại Nginx
sudo systemctl restart nginx

# Kiểm tra trạng thái Nginx
sudo systemctl status nginx

# Tùy chọn: Khởi động lại MySQL (chỉ khi cần thiết)
# Chỉ cần thực hiện khi có thay đổi lớn về cấu trúc DB hoặc cấu hình MySQL
# sudo systemctl restart mysql
# sudo systemctl status mysql
```

### 7. Xóa cache OPcache

OPcache có thể lưu trữ các phiên bản cũ của các file PHP. Để đảm bảo các thay đổi mới được áp dụng:

```bash
# Tạo file PHP để reset OPcache
cat > /var/www/html/exam_bank/public/opcache_reset.php << 'EOL'
<?php
if(function_exists('opcache_reset')) {
    opcache_reset();
    echo "OPcache has been reset.";
} else {
    echo "OPcache is not enabled.";
}
EOL

# Gọi file qua curl để xóa cache
curl -s http://117.2.18.234/opcache_reset.php

# Xóa file reset sau khi sử dụng
rm /var/www/html/exam_bank/public/opcache_reset.php
```

### 8. Kiểm tra sau khi cập nhật

1. Kiểm tra trang web để đảm bảo mọi thứ hoạt động bình thường
2. Kiểm tra logs để xem có lỗi nào không:
   ```bash
   tail -f /var/log/nginx/error.log
   tail -f storage/logs/laravel.log
   ```
3. Kiểm tra các chức năng chính của ứng dụng

## Xử lý sự cố

### Nếu cập nhật thất bại

```bash
# Khôi phục từ bản sao lưu gần nhất
cd ~/backups/[ngày backup mới nhất]

# Khôi phục database
mysql -u laravel -p exam_bank < exam_bank_[timestamp].sql

# Khôi phục code
sudo rsync -av exam_bank_[timestamp]/ /var/www/html/exam_bank/

# Cài đặt lại dependencies
cd /var/www/html/exam_bank
composer install --no-dev --optimize-autoloader
npm ci
npm run build

# Khởi động lại các dịch vụ
sudo systemctl restart php8.3-fpm
sudo systemctl restart nginx
```

### Nếu có xung đột git

```bash
# Xem các xung đột
git status

# Nếu cần, hủy các thay đổi cục bộ
git reset --hard origin/main

# Hoặc giải quyết xung đột thủ công, sau đó
git add .
git commit -m "Resolve conflicts"
```

### Nếu có lỗi PHP-FPM

```bash
# Kiểm tra log lỗi
sudo tail -f /var/log/php8.3-fpm.log

# Kiểm tra cấu hình
sudo php -i | grep php.ini
sudo cat /etc/php/8.3/fpm/php.ini | grep -E 'memory_limit|max_execution_time|upload_max_filesize'

# Khởi động lại với debug
sudo systemctl restart php8.3-fpm
```

### Nếu có lỗi quyền truy cập

```bash
# Kiểm tra quyền của thư mục project
ls -la /var/www/html/exam_bank

# Sửa quyền nếu cần
sudo find /var/www/html/exam_bank -type f -exec chmod 644 {} \;
sudo find /var/www/html/exam_bank -type d -exec chmod 755 {} \;
sudo chown -R www-data:www-data /var/www/html/exam_bank/storage
sudo chown -R www-data:www-data /var/www/html/exam_bank/bootstrap/cache
```

## Lưu ý quan trọng

1. **Không bao giờ** chỉnh sửa code trực tiếp trên server production
2. Luôn kiểm tra code trên môi trường staging trước khi triển khai lên production
3. Đảm bảo các thay đổi DB tương thích ngược
4. Lưu ý các thay đổi về quyền truy cập file
5. Đảm bảo OPcache được xóa sau mỗi lần cập nhật
6. Kiểm tra không gian đĩa trước khi cập nhật (hiện tại sử dụng 73% - cần theo dõi)

## Thông tin cấu hình hệ thống

### PHP Modules đã cài đặt
PHP 8.3 đã được cài đặt với các module chính: bcmath, curl, dom, gd, intl, json, mbstring, mysql, openssl, PDO, xml, zip, Zend OPcache.

### Nginx Site Configuration
Cấu hình Nginx hiện tại:
- Server name: 117.2.18.234
- Cấu hình SSL: Đã kích hoạt (cổng 443)
- Root directory: /var/www/html/exam_bank/public
- PHP-FPM socket: unix:/var/run/php/php8.3-fpm.sock

## Liên hệ hỗ trợ

Nếu bạn gặp vấn đề trong quá trình cập nhật, vui lòng liên hệ:
- Email: [địa chỉ email của bạn]
- Slack: [kênh Slack của bạn] 
## Cấu hình cho Request URI lớn (414 Error Fix)

Để khắc phục lỗi 414 Request-URI Too Large khi download, đã cấu hình:

### Nginx Configuration
File: `/etc/nginx/conf.d/large-uri.conf`
- client_header_buffer_size: 16k
- large_client_header_buffers: 8 64k
- client_max_body_size: 100M

### PHP Configuration
File: `/etc/php/8.3/fpm/conf.d/99-large-requests.ini`
- memory_limit: 512M
- max_execution_time: 300 seconds
- upload_max_filesize: 100M
- post_max_size: 100M
