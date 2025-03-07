<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Link } from "@inertiajs/vue3";
import { router } from '@inertiajs/vue3';
const { nhiemvus, message, success } = defineProps({
    nhiemvus: {
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

// const deleteNhiemVu = (id) => {
//     console.log('Xác nhận xóa được gọi');
//     if (confirm('Bạn có chắc chắn muốn xóa nhiệm vụ này?')) {
//         console.log('Xác nhận OK, gửi yêu cầu xóa');
//         router.delete(route('admin.nhiemvu.destroy', id), {
//             onSuccess: () => {
//                 alert('Nhiệm vụ đã được xóa thành công!');
//             },
//             onError: (errors) => {
//                 alert('Có lỗi xảy ra khi xóa nhiệm vụ!');
//                 console.error(errors);
//             },
//         });
//     } else {
//         console.log('Xác nhận Cancel, không xóa');
//     }
// };
const deleteNhiemVu = (id) => {
    console.log('Bắt đầu xử lý xóa, ID:', id);
    const confirmed = confirm('Bạn có chắc chắn muốn xóa nhiệm vụ này?');
    console.log('Kết quả confirm:', confirmed);
    if (confirmed) {
        console.log('Gửi yêu cầu xóa cho ID:', id);
        router.delete(route('admin.nhiemvu.destroy', id), {
            onSuccess: () => {
                console.log('Xóa thành công');
                alert('Nhiệm vụ đã được xóa thành công!');
            },
            onError: (errors) => {
                console.log('Xóa thất bại', errors);
                alert('Có lỗi xảy ra khi xóa nhiệm vụ!');
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
                <a :href="route('admin.nhiemvu.index')">Nhiệm vụ</a>
            </li>
        </template>
        <template v-slot:content>
            <div class="content">
                <div class="card">
                    <div
                        class="card-header d-flex justify-content-between align-items-center"
                    >
                        <h3 class="mb-0">Nhiệm vụ</h3>
                        <Link
                            :href="route('admin.nhiemvu.create')"
                            class="btn btn-success-add"
                        >
                            <i class="fas fa-user-plus"></i> Add Nhiệm vụ
                        </Link>
                    </div>
                    <div class="card-body">
                        <!-- Bảng hiển thị danh sách người dùng -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nhiệm vụ</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="nhiemvus.data.length === 0">
                                        <td colspan="3" class="text-center">
                                            Không có dữ liệu
                                        </td>
                                    </tr>
                                    <tr
                                        v-for="nhiemvu in nhiemvus.data"
                                        :key="nhiemvu.id"
                                    >
                                        <td>{{ nhiemvu.id }}</td>
                                        <td>{{ nhiemvu.ten }}</td>
                                        <td>
                                            <Link
                                                :href="
                                                    route(
                                                        'admin.nhiemvu.edit',
                                                        nhiemvu.id
                                                    )
                                                "
                                                class="btn btn-sm btn-success-edit me-2"
                                            >
                                                <i class="fas fa-edit"></i>
                                            </Link>
                                            <!-- <Link
                                                :href="
                                                    route(
                                                        'admin.nhiemvu.destroy',
                                                        nhiemvu.id
                                                    )
                                                "
                                                method="delete"
                                                as="button"
                                                class="btn btn-sm btn-danger-delete"
                                                @click.prevent="
                                                    deleteNhiemVu(nhiemvu.id)
                                                "
                                            >
                                                <i class="fas fa-trash"></i>
                                            </Link> -->
                                            <button
                  class="btn btn-sm btn-danger-delete"
                  @click="deleteNhiemVu(nhiemvu.id)"
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
                                        disabled: nhiemvus.current_page === 1,
                                    }"
                                >
                                    <Link
                                        :href="nhiemvus.links[0]?.url || '#'"
                                        class="page-link rounded-circle"
                                        :class="{
                                            'disabled-link':
                                                !nhiemvus.links[0]?.url,
                                        }"
                                    >
                                        <i class="fas fa-chevron-left"></i>
                                    </Link>
                                </li>

                                <!-- Các số trang -->
                                <li
                                    v-for="link in nhiemvus.links.slice(1, -1)"
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
                                            nhiemvus.current_page ===
                                            nhiemvus.last_page,
                                    }"
                                >
                                    <Link
                                        :href="
                                            nhiemvus.links[
                                                nhiemvus.links.length - 1
                                            ]?.url || '#'
                                        "
                                        class="page-link rounded-circle"
                                        :class="{
                                            'disabled-link':
                                                !nhiemvus.links[
                                                    nhiemvus.links.length - 1
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
