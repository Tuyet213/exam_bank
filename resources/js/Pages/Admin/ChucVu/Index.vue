<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Link } from "@inertiajs/vue3";

const {chucVus, message, success } = defineProps({
    chucVus: {
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

const deleteChucVu = (id) => {
    const chucVu = chucVus.data.find((chucVu) => chucVu.id === id); 
    if (!chucVu) {
        alert("Chức vụ không tồn tại!");
        return;
    }

    const chucVuTen = chucVu.ten || "Không có tên";
    if (confirm(`Bạn có muốn xóa Chức vụ "${chucVuTen}" không?`)) {
        return true;
    }
    return false;
};
</script>

<template>
    <AdminLayout>
        <template v-slot:sub-link>
            <li class="breadcrumb-item active">
                <a :href="route('admin.chucvu.index')">Chức vụ</a>
            </li>
        </template>
        <template v-slot:content>
            <div class="content">
                <div class="card">
                    <div
                        class="card-header d-flex justify-content-between align-items-center"
                    >
                        <h3 class="mb-0">Chức vụ</h3>
                        <Link
                            :href="route('admin.chucvu.create')"
                            class="btn btn-success-add"
                        >
                            <i class="fas fa-user-plus"></i> Add Chức vụ
                        </Link>
                    </div>
                    <div class="card-body">
                        <!-- Bảng hiển thị danh sách người dùng -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Chức vụ</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="chucVus.data.length === 0">
                                        <td colspan="3" class="text-center">
                                            Không có dữ liệu
                                        </td>
                                    </tr>
                                    <tr
                                        v-for="chucVu in chucVus.data"
                                        :key="chucVu.id"
                                    >
                                        <td>{{ chucVu.id }}</td>
                                        <td>{{ chucVu.ten }}</td>
                                        <td>
                                            <Link
                                                :href="
                                                    route(
                                                        'admin.chucvu.edit',
                                                        chucVu.id
                                                    )
                                                "
                                                class="btn btn-sm btn-success-edit me-2"
                                            >
                                                <i class="fas fa-edit"></i>
                                            </Link>
                                            <Link
                                                :href="
                                                    route(
                                                        'admin.chucvu.destroy',
                                                        chucVu.id
                                                    )
                                                "
                                                method="delete"
                                                as="button"
                                                class="btn btn-sm btn-danger-delete"
                                                @click.prevent="
                                                    deleteChucVu(chucVu.id) ||
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
                                        disabled: chucVus.current_page === 1,
                                    }"
                                >
                                    <Link
                                        :href="chucVus.links[0]?.url || '#'"
                                        class="page-link rounded-circle"
                                        :class="{
                                            'disabled-link':
                                                !chucVus.links[0]?.url,
                                        }"
                                    >
                                        <i class="fas fa-chevron-left"></i>
                                    </Link>
                                </li>

                                <!-- Các số trang -->
                                <li
                                    v-for="link in chucVus.links.slice(1, -1)"
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
                                            chucVus.current_page ===
                                            chucVus.last_page,
                                    }"
                                >
                                    <Link
                                        :href="
                                            chucVus.links[
                                                chucVus.links.length - 1
                                            ]?.url || '#'
                                        "
                                        class="page-link rounded-circle"
                                        :class="{
                                            'disabled-link':
                                                !chucVus.links[
                                                    chucVus.links.length - 1
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
