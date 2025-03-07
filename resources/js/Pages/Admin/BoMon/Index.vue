<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Link } from "@inertiajs/vue3";
import { router } from '@inertiajs/vue3';

const { bomons, message, success } = defineProps({
    bomons: {
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

const deleteBomon = (id) => {
    console.log('Bắt đầu xử lý xóa, ID:', id);
    const confirmed = confirm('Bạn có chắc chắn muốn xóa Bộ môn này?');
    console.log('Kết quả confirm:', confirmed);
    if (confirmed) {
        console.log('Gửi yêu cầu xóa cho ID:', id);
        router.delete(route('admin.bomon.destroy', id), {
            onSuccess: () => {
                console.log('Xóa thành công');
                alert('Bộ môn đã được xóa thành công!');
            },
            onError: (errors) => {
                console.log('Xóa thất bại', errors);
                    alert('Có lỗi xảy ra khi xóa Bộ môn!');
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
                <a :href="route('admin.bomon.index')">Bộ môn</a>
            </li>
        </template>
        <template v-slot:content>
            <div class="content">
                <div class="card">
                    <div
                        class="card-header d-flex justify-content-between align-items-center"
                    >
                        <h3 class="mb-0">Bộ môn</h3>
                        <Link
                            :href="route('admin.bomon.create')"
                            class="btn btn-success-add"
                        >
                            <i class="fas fa-user-plus"></i> Add Bộ môn
                        </Link>
                    </div>
                    <div class="card-body">
                        <!-- Bảng hiển thị danh sách người dùng -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Bộ môn</th>
                                        <th>Khoa</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="bomons.data.length === 0">
                                        <td colspan="4" class="text-center">
                                            Không có dữ liệu
                                        </td>
                                    </tr>
                                    <tr
                                        v-for="bomon in bomons.data"
                                        :key="bomon.id"
                                    >
                                        <td>{{ bomon.id }}</td>
                                        <td>{{ bomon.ten }}</td>
                                        <td>{{ bomon.khoa.ten }}</td>
                                        <td>
                                            <Link
                                                :href="
                                                    route(
                                                        'admin.bomon.edit',
                                                        bomon.id
                                                    )
                                                "
                                                class="btn btn-sm btn-success-edit me-2"
                                            >
                                                <i class="fas fa-edit"></i>
                                            </Link>
                                            <button
                                                class="btn btn-sm btn-danger-delete"
                                                @click="deleteBomon(bomon.id)"
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
                                        disabled: bomons.current_page === 1,
                                    }"
                                >
                                    <Link
                                        :href="bomons.links[0]?.url || '#'"
                                        class="page-link rounded-circle"
                                        :class="{
                                            'disabled-link':
                                                !bomons.links[0]?.url,
                                        }"
                                    >
                                        <i class="fas fa-chevron-left"></i>
                                    </Link>
                                </li>

                                <!-- Các số trang -->
                                <li
                                    v-for="link in bomons.links.slice(1, -1)"
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
                                            bomons.current_page ===
                                            bomons.last_page,
                                    }"
                                >
                                    <Link
                                        :href="
                                            bomons.links[
                                                bomons.links.length - 1
                                            ]?.url || '#'
                                        "
                                        class="page-link rounded-circle"
                                        :class="{
                                            'disabled-link':
                                                !bomons.links[
                                                    bomons.links.length - 1
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
