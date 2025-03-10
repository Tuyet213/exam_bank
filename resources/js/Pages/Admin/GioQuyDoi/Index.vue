<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Link } from "@inertiajs/vue3";
import { router } from '@inertiajs/vue3';

const {gioQuyDois, message, success } = defineProps({
    gioQuyDois: {
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

const deleteGioQuyDoi = (id) => {
    console.log('Bắt đầu xử lý xóa, ID:', id);
    const confirmed = confirm('Bạn có chắc chắn muốn xóa Gio Quy Doi này?');
    console.log('Kết quả confirm:', confirmed);
    if (confirmed) {
        console.log('Gửi yêu cầu xóa cho ID:', id);
        router.delete(route('admin.gioquydoi.destroy', id), {
            onSuccess: () => {
                console.log('Xóa thành công');
                alert('Gio Quy Doi đã được xóa thành công!');
            },
            onError: (errors) => {
                console.log('Xóa thất bại', errors);
                alert('Có lỗi xảy ra khi xóa Gio Quy Doi!');
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
                <a :href="route('admin.gioquydoi.index')">Gio Quy Doi</a>
            </li>
        </template>
        <template v-slot:content>
            <div class="content">
                <div class="card">
                    <div
                        class="card-header d-flex justify-content-between align-items-center"
                    >
                        <h3 class="mb-0">Gio Quy Doi</h3>
                        <Link
                            :href="route('admin.gioquydoi.create')"
                            class="btn btn-success-add"
                        >
                            <i class="fas fa-user-plus"></i> Add Giờ Quy Đổi
                        </Link>
                    </div>
                    <div class="card-body">
                        <!-- Bảng hiển thị danh sách người dùng -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Loại đề thi</th>
                                        <th>Loại hành động</th>
                                        <th>Giờ</th>
                                        <th>Số lượng</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="gioQuyDois.data.length === 0">
                                        <td colspan="6" class="text-center">
                                            Không có dữ liệu
                                        </td>
                                    </tr>
                                    <tr
                                        v-for="gioQuyDoi in gioQuyDois.data"
                                        :key="gioQuyDoi.id"
                                    >
                                        <td>{{ gioQuyDoi.id }}</td>
                                        <td>{{ gioQuyDoi.loai_de_thi === 0 ? 'Trắc nghiệm' : gioQuyDoi.loai_de_thi === 1 ? 'Tự luận' : 'Trắc nghiệm + Tự luận' }}</td>
                                        <td>{{ gioQuyDoi.loai_hanh_dong === 0 ? 'Biên soạn' : gioQuyDoi.loai_hanh_dong === 1 ? 'Họp phản biện cấp Bộ môn' : 'Họp thẩm định cấp Khoa' }}</td>
                                        <td>{{ gioQuyDoi.gio }}</td>
                                        <td>{{ gioQuyDoi.so_luong }}</td>
                                        <td>
                                            <Link
                                                :href="
                                                    route(
                                                        'admin.gioquydoi.edit',
                                                        gioQuyDoi.id
                                                    )
                                                "
                                                class="btn btn-sm btn-success-edit me-2"
                                            >
                                                <i class="fas fa-edit"></i>
                                            </Link>
                                            <button
                                                class="btn btn-sm btn-danger-delete"
                                                @click="deleteGioQuyDoi(gioQuyDoi.id)"
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
                                        disabled: gioQuyDois.current_page === 1,
                                    }"
                                >
                                    <Link
                                        :href="gioQuyDois.links[0]?.url || '#'"
                                        class="page-link rounded-circle"
                                        :class="{
                                            'disabled-link':
                                                !gioQuyDois.links[0]?.url,
                                        }"
                                    >
                                        <i class="fas fa-chevron-left"></i>
                                    </Link>
                                </li>

                                <!-- Các số trang -->
                                <li
                                    v-for="link in gioQuyDois.links.slice(1, -1)"
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
                                            gioQuyDois.current_page ===
                                            gioQuyDois.last_page,
                                    }"
                                >
                                    <Link
                                        :href="
                                            gioQuyDois.links[
                                                gioQuyDois.links.length - 1
                                            ]?.url || '#'
                                        "
                                        class="page-link rounded-circle"
                                        :class="{
                                            'disabled-link':
                                                !gioQuyDois.links[
                                                    gioQuyDois.links.length - 1
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
