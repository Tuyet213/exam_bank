<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Link } from "@inertiajs/vue3";

const {khoas, message, success } = defineProps({
    khoas: {
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

const deleteKhoa = (id) => {
    const khoa = khoas.data.find((khoa) => khoa.id === id); 
    if (!khoa) {
        alert("Khoa không tồn tại!");
        return;
    }

    const khoaTen = khoa.ten || "Không có tên";
    if (confirm(`Bạn có muốn xóa Khoa "${khoaTen}" không?`)) {
        return true;
    }
    return false;
};
</script>

<template>
    <AdminLayout>
        <template v-slot:sub-link>
            <li class="breadcrumb-item active">
                <a :href="route('admin.khoa.index')">Khoa</a>
            </li>
        </template>
        <template v-slot:content>
            <div class="content">
                <div class="card">
                    <div
                        class="card-header d-flex justify-content-between align-items-center"
                    >
                        <h3 class="mb-0">Khoa</h3>
                        <Link
                            :href="route('admin.khoa.create')"
                            class="btn btn-success-add"
                        >
                            <i class="fas fa-user-plus"></i> Add Khoa
                        </Link>
                    </div>
                    <div class="card-body">
                        <!-- Bảng hiển thị danh sách người dùng -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Khoa</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="khoas.data.length === 0">
                                        <td colspan="3" class="text-center">
                                            Không có dữ liệu
                                        </td>
                                    </tr>
                                    <tr
                                        v-for="khoa in khoas.data"
                                        :key="khoa.id"
                                    >
                                        <td>{{ khoa.id }}</td>
                                        <td>{{ khoa.ten }}</td>
                                        <td>
                                            <Link
                                                :href="
                                                    route(
                                                        'admin.khoa.edit',
                                                        khoa.id
                                                    )
                                                "
                                                class="btn btn-sm btn-success-edit me-2"
                                            >
                                                <i class="fas fa-edit"></i>
                                            </Link>
                                            <Link
                                                :href="
                                                    route(
                                                        'admin.khoa.destroy',
                                                        khoa.id
                                                    )
                                                "
                                                method="delete"
                                                as="button"
                                                class="btn btn-sm btn-danger-delete"
                                                @click.prevent="
                                                    deleteKhoa(khoa.id) ||
                                                        $event.preventDefault()
                                                "
                                            >
                                                <i class="fas fa-trash"></i>
                                            </Link>
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
                                        disabled: khoas.current_page === 1,
                                    }"
                                >
                                    <Link
                                        :href="khoas.links[0]?.url || '#'"
                                        class="page-link rounded-circle"
                                        :class="{
                                            'disabled-link':
                                                !khoas.links[0]?.url,
                                        }"
                                    >
                                        <i class="fas fa-chevron-left"></i>
                                    </Link>
                                </li>

                                <!-- Các số trang -->
                                <li
                                    v-for="link in khoas.links.slice(1, -1)"
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
                                            khoas.current_page ===
                                            khoas.last_page,
                                    }"
                                >
                                    <Link
                                        :href="
                                            khoas.links[
                                                khoas.links.length - 1
                                            ]?.url || '#'
                                        "
                                        class="page-link rounded-circle"
                                        :class="{
                                            'disabled-link':
                                                !khoas.links[
                                                    khoas.links.length - 1
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
