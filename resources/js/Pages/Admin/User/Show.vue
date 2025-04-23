<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <AppLayout role="admin">
        <template v-slot:sub-link>
            <li class="breadcrumb-item">
                <Link :href="route('admin.user.index')">Người dùng</Link>
            </li>
            <li class="breadcrumb-item active">{{user.name}}</li>
        </template>

        <template v-slot:content>
            <div class="content">
                <div class="card border-radius-lg shadow-lg">
                    <div class="card-header bg-success-tb text-white p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="mb-0 font-weight-bolder">THÔNG TIN NGƯỜI DÙNG</h3>
                            <div>
                                <Link
                                    :href="route('admin.user.edit', user.id)"
                                    class="btn btn-light me-2"
                                >
                                    <i class="fas fa-edit"></i> Chỉnh sửa
                                </Link>
                                <Link
                                    :href="route('admin.user.index')"
                                    class="btn btn-light"
                                >
                                    <i class="fas fa-arrow-left"></i> Quay lại
                                </Link>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="row">
                            <!-- Thông tin cơ bản -->
                            <div class="col-md-6 mb-4">
                                <div class="info-group">
                                    <h4 class="section-title">Thông tin cơ bản</h4>
                                    <div class="info-item">
                                        <label>ID:</label>
                                        <span>{{ user.id }}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>Họ và tên:</label>
                                        <span>{{ user.name }}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>Email:</label>
                                        <span>{{ user.email }}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>Số điện thoại:</label>
                                        <span>{{ user.sdt }}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>Ngày sinh:</label>
                                        <span>{{ user.ngay_sinh }}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>Giới tính:</label>
                                        <span>{{ user.gioi_tinh ? 'Nam' : 'Nữ' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>Địa chỉ:</label>
                                        <span>{{ user.dia_chi }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Thông tin công việc -->
                            <div class="col-md-6 mb-4">
                                <div class="info-group">
                                    <h4 class="section-title">Thông tin công việc</h4>
                                    <div class="info-item">
                                        <label>Chức vụ:</label>
                                        <span>{{ user.chucvu?.ten }}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>Khoa:</label>
                                        <span>{{ user.bomon?.khoa?.ten }}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>Bộ môn:</label>
                                        <span>{{ user.bomon?.ten }}</span>
                                    </div>
                                </div>
                            </div>

                            
                            <!-- Roles và Permissions -->
                            <div class="col-md-6 mb-4">
                                <div class="info-group">
                                    <h4 class="section-title">Phân quyền</h4>
                                    <div class="info-item">
                                        <label>Vai trò:</label>
                                        <div class="role-tags">
                                            <span 
                                                v-for="role in user.role_names" 
                                                :key="role"
                                                class="badge bg-primary me-2 mb-2"
                                            >
                                                {{ role }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <label>Quyền hạn:</label>
                                        <div class="permission-tags">
                                            <span 
                                                v-for="permission in user.permission_names" 
                                                :key="permission"
                                                class="badge bg-info me-2 mb-2"
                                            >
                                                {{ permission }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </AppLayout>
</template>

<style scoped>
.section-title {
    color: #2c3e50;
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #eee;
}

.info-group {
    background-color: #f8f9fa;
    padding: 1.5rem;
    border-radius: 0.5rem;
    height: 100%;
}

.info-item {
    margin-bottom: 1rem;
}

.info-item label {
    display: block;
    color: #6c757d;
    font-weight: 500;
    margin-bottom: 0.3rem;
}

.info-item span {
    color: #2c3e50;
    font-weight: 400;
}

.role-tags, .permission-tags {
    margin-top: 0.5rem;
}

.badge {
    font-size: 0.85rem;
    padding: 0.4rem 0.8rem;
    border-radius: 1rem;
}

.bg-primary {
    background-color: #007bff !important;
}

.bg-info {
    background-color: #17a2b8 !important;
}

@media (max-width: 768px) {
    .card-header {
        flex-direction: column;
        gap: 1rem;
    }

    .card-header .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
}
</style>
