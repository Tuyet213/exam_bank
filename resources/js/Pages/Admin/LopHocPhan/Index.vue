<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Link } from "@inertiajs/vue3";
import { router } from '@inertiajs/vue3';

const { lophocphans, message, success } = defineProps({
    lophocphans: {
        type: Object, 
        required: true,
        default: () => ({
            data: [], 
            current_page: 1,
            last_page: 1,
            total: 0,
            links: [], 
        }),
    },
    message: {
        type: String,
        default: "",
    },
    success: {
        type: Boolean,
        default: undefined,
    },
});

console.log(lophocphans);
for (let i = 0; i < lophocphans.data.length; i++) {
    console.log(lophocphans.data[i].khoa.ten);
}

const deleteLopHocPhan = (id) => {
    console.log('Bắt đầu xử lý xóa, ID:', id);
    const confirmed = confirm('Bạn có chắc chắn muốn xóa Lớp học phần này?');
    console.log('Kết quả confirm:', confirmed);
    if (confirmed) {
        console.log('Gửi yêu cầu xóa cho ID:', id);
        router.delete(route('admin.lophocphan.destroy', id), {
            onSuccess: () => {
                console.log('Xóa thành công');
                alert('Lớp học phần đã được xóa thành công!');
            },
            onError: (errors) => {
                console.log('Xóa thất bại', errors);
                alert('Có lỗi xảy ra khi xóa Lớp học phần!');
                console.error(errors);
            },
        });
    } else {
        console.log('Hủy xóa, không gửi yêu cầu');
    }
};
</script>

<template>
    <AdminLayout>
        <template v-slot:sub-link>
            <li class="breadcrumb-item active">
                <a :href="route('admin.lophocphan.index')">Lớp học phần</a>
            </li>
        </template>
        <template v-slot:content>
            <div class="content">
                <div class="card">
                    <div
                        class="card-header d-flex justify-content-between align-items-center"
                    >
                        <h3 class="mb-0">Lớp học phần</h3>
                        <Link
                            :href="route('admin.lophocphan.create')"
                            class="btn btn-success-add"
                        >
                            <i class="fas fa-user-plus"></i> Add Lớp học phần
                        </Link>
                    </div>
                    <div class="card-body">
                        <!-- Bảng hiển thị danh sách người dùng -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Lớp học phần</th>
                                        <th>Kỳ học</th>
                                        <th>Năm học</th>
                                        <th>Số lượng</th>
                                        <th>Khoa</th>
                                        <th>Học phần</th>
                                        <th>Giảng viên</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="lophocphans.data.length === 0">
                                        <td colspan="10" class="text-center">
                                            Không có dữ liệu
                                        </td>
                                    </tr>
                                    <tr
                                        v-for="lophocphan in lophocphans.data"
                                        :key="lophocphan.id"
                                    >
                                        <td>{{ lophocphan.id }}</td>
                                        <td>{{ lophocphan.ten }}</td>
                                        <td>{{ lophocphan.ky_hoc }}</td>
                                        <td>{{ lophocphan.nam_hoc }}</td>
                                        <td>{{ lophocphan.so_luong_sinh_vien }}</td>
                                        <td>{{ lophocphan.khoa.ten }}</td>
                                        <td>{{ lophocphan.hoc_phan.ten }}</td>
                                        <td>{{ lophocphan.vien_chuc.name }}</td>
                                        <td>
                                            <Link
                                                :href="
                                                    route(
                                                        'admin.lophocphan.edit',
                                                        lophocphan.id
                                                    )
                                                "
                                                class="btn btn-sm btn-success-edit me-2"
                                            >
                                                <i class="fas fa-edit"></i>
                                            </Link>
                                            <button
                                                class="btn btn-sm btn-danger-delete"
                                                @click="deleteLopHocPhan(lophocphan.id)"
                                            >
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Phân trang -->
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center mt-3">
                                <!-- Nút Previous -->
                                <li
                                    class="page-item"
                                    :class="{
                                        disabled: lophocphans.current_page === 1,
                                    }"
                                >
                                    <Link
                                        :href=" lophocphans.links[0]?.url || '#'"
                                        class="page-link rounded-circle"
                                        :class="{
                                            'disabled-link':
                                                !lophocphans.links[0]?.url,
                                        }"
                                    >
                                        <i class="fas fa-chevron-left"></i>
                                    </Link>
                                </li>

                                <!-- Các số trang -->
                                <li
                                    v-for="link in lophocphans.links.slice(1, -1)"
                                    :key="link.label"
                                    class="page-item"
                                    :class="{ active: link.active }"
                                >
                                    <Link
                                        :href="link.url || '#'"
                                        class="page-link rounded-circle"
                                        :class="{ 'active-page': link.active }"
                                    >
                                        {{ link.label }}
                                    </Link>
                                </li>

                                <!-- Nút Next -->
                                <li
                                    class="page-item"
                                    :class="{
                                        disabled:
                                            lophocphans.current_page ===
                                            lophocphans.last_page,
                                    }"
                                >
                                    <Link
                                        :href="
                                            lophocphans.links[
                                                lophocphans.links.length - 1
                                            ]?.url || '#'
                                        "
                                        class="page-link rounded-circle"
                                        :class="{
                                            'disabled-link':
                                                !lophocphans.links[
                                                    lophocphans.links.length - 1
                                                ]?.url,
                                        }"
                                    >
                                        <i class="fas fa-chevron-right"></i>
                                    </Link>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </template>
    </AdminLayout>
</template>
