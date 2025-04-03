<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Link } from "@inertiajs/vue3";
import { router } from '@inertiajs/vue3';
import { ref, watch } from "vue";

const { chucVus, message, success } = defineProps({
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

// Thêm biến cho chức năng tìm kiếm
const searchTerm = ref("");
const filterBy = ref("all"); // all, id, ten
const debounceTimeout = ref(null);

const deleteChucVu = (id) => {
    const confirmed = confirm('Bạn có chắc chắn muốn xóa Chức vụ này?');
    if (confirmed) {
        router.delete(route('admin.chucvu.destroy', id), {
            onSuccess: () => {
                alert('Chức vụ đã được xóa thành công!');
            },
            onError: (errors) => {
                alert('Có lỗi xảy ra khi xóa Chức vụ!');
                console.error(errors);
            },
        });
    }
};

// Hàm thực hiện tìm kiếm
const performSearch = () => {
    if (debounceTimeout.value) {
        clearTimeout(debounceTimeout.value);
    }
    debounceTimeout.value = setTimeout(() => {
        router.get(
            route("admin.chucvu.index"),
            {
                search: searchTerm.value,
                filter: filterBy.value,
            },
            {
                preserveState: true,
                replace: true,
            }
        );
    }, 300); // Delay 300ms để tránh gọi API quá nhiều
};

// Theo dõi thay đổi của searchTerm và filterBy
watch([searchTerm, filterBy], () => {
    performSearch();
});

// Xử lý tìm kiếm thủ công khi nhấn Enter
const handleSearch = (event) => {
    if (event.key === "Enter") {
        performSearch();
    }
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
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">Chức vụ</h3>
                        <div class="d-flex gap-2">
                            <!-- Ô tìm kiếm -->
                            <div class="input-group" style="width: 300px;">
                                <input
                                    v-model="searchTerm"
                                    type="text"
                                    class="form-control"
                                    placeholder="Tìm theo ID hoặc tên Chức vụ..."
                                    @keyup="handleSearch"
                                />
                                <button
                                    class="btn btn-success-add"
                                    @click="performSearch"
                                >
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            <!-- Bộ lọc -->
                            <select
                                v-model="filterBy"
                                class="form-control"
                                style="width: 150px;"
                            >
                                <option value="all">Tất cả</option>
                                <option value="id">Theo ID</option>
                                <option value="ten">Theo tên</option>
                            </select>
                            
                        </div>
                        <!-- Nút thêm Chức vụ -->
                        <Link
                                :href="route('admin.chucvu.create')"
                                class="btn btn-success-add"
                            >
                                <i class="fas fa-user-plus"></i> Add Chức vụ
                            </Link>
                    </div>
                    <div class="card-body">
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
                                    <tr v-for="chucVu in chucVus.data" :key="chucVu.id">
                                        <td>{{ chucVu?.id }}</td>
                                        <td>{{ chucVu?.ten }}</td>
                                        <td>
                                            <Link
                                                :href="route('admin.chucvu.edit', chucVu.id)"
                                                class="btn btn-sm btn-success-edit me-2"
                                            >
                                                <i class="fas fa-edit"></i>
                                            </Link>
                                            <button
                                                class="btn btn-sm btn-danger-delete"
                                                @click="deleteChucVu(chucVu.id)"
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
                                <li
                                    class="page-item"
                                    :class="{ disabled: chucVus.current_page === 1 }"
                                >
                                    <Link
                                        :href="chucVus.links[0]?.url || '#'"
                                        class="page-link rounded-circle"
                                        :class="{ 'disabled-link': !chucVus.links[0]?.url }"
                                    >
                                        <i class="fas fa-chevron-left"></i>
                                    </Link>
                                </li>
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
                                <li
                                    class="page-item"
                                    :class="{ disabled: chucVus.current_page === chucVus.last_page }"
                                >
                                    <Link
                                        :href="chucVus.links[chucVus.links.length - 1]?.url || '#'"
                                        class="page-link rounded-circle"
                                        :class="{ 'disabled-link': !chucVus.links[chucVus.links.length - 1]?.url }"
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
