<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/vue3";
import { router } from "@inertiajs/vue3";
import { ref, watch } from "vue";

const { khoas, message, success } = defineProps({
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

const searchTerm = ref("");
const filterBy = ref("all"); // all, id, ten
const debounceTimeout = ref(null);

const deleteKhoa = (id) => {
    const confirmed = confirm("Bạn có chắc chắn muốn xóa Khoa này?");
    if (confirmed) {
        router.delete(route("admin.khoa.destroy", id), {
            onSuccess: () => {
                alert("Khoa đã được xóa thành công!");
            },
            onError: (errors) => {
                alert("Có lỗi xảy ra khi xóa Khoa!");
                console.error(errors);
            },
        });
    }
};

const performSearch = () => {
    if (debounceTimeout.value) {
        clearTimeout(debounceTimeout.value);
    }
    debounceTimeout.value = setTimeout(() => {
        router.get(
            route("admin.khoa.index"),
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
    <AppLayout role="admin">
        <template v-slot:sub-link>
            <li class="breadcrumb-item active">
                <a :href="route('admin.khoa.index')">Khoa</a>
            </li>
        </template>
        <template v-slot:content>
            <div class="content">
                <div class="card border-radius-lg shadow-lg animated-fade-in">
                    <!-- Card Header -->
                    <div class="card-header bg-success-tb text-white p-4">
                        <div class="row justify-content-between align-items-center">
                            <div class="col-md-8">
                                <h3 class="mb-0 font-weight-bolder">
                                    DANH SÁCH KHOA
                                </h3>
                            </div>
                            <div class="col-md-4 text-end">
                                <Link
                                    :href="route('admin.khoa.create')"
                                    class="btn btn-light"
                                >
                                    <i class="fas fa-plus"></i> Thêm mới
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Bộ lọc -->
                    <div class="card-body pb-0">
                        <div class="row mb-4">
                            <div class="col-md-8 mb-3">
                                <div class="input-group">
                                    <input
                                        v-model="searchTerm"
                                        type="text"
                                        class="form-control"
                                        placeholder="Tìm kiếm theo ID hoặc tên Khoa..."
                                        @keyup="handleSearch"
                                    >
                                    <button
                                        class="btn btn-success-add"
                                        @click="performSearch"
                                    >
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <select
                                    v-model="filterBy"
                                    class="form-select"
                                >
                                    <option value="all">Tất cả</option>
                                    <option value="id">Theo ID</option>
                                    <option value="ten">Theo tên</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
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
                                        <td>{{ khoa?.id }}</td>
                                        <td>{{ khoa?.ten }}</td>
                                        <td>
                                            <div class="d-flex">
                                                <Link
                                                    :href="
                                                        route(
                                                            'admin.khoa.edit',
                                                            khoa.id
                                                        )
                                                    "
                                                    class="btn btn-sm btn-success-edit me-2"
                                                >
                                                    <i class="far fa-edit"></i>
                                                </Link>
                                                <button
                                                    class="btn btn-sm btn-danger-delete"
                                                    @click="deleteKhoa(khoa.id)"
                                                >
                                                    <i class="far fa-trash-alt"></i>
                                                </button>
                                            </div>
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
                                <li
                                    v-for="link in khoas.links.slice(1, -1)"
                                    :key="link.label"
                                    class="page-item"
                                    :class="{
                                        active:
                                            link.active ||
                                            link.label == khoas.current_page,
                                    }"
                                >
                                    <Link
                                        :href="link.url"
                                        class="page-link rounded-circle"
                                        v-html="link.label"
                                    ></Link>
                                </li>
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
                                            khoas.links[khoas.links.length - 1]
                                                ?.url || '#'
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
    </AppLayout>
</template>

<style scoped>
.bg-success-tb {
    background-color: #5cb85c;
}

.btn-success-add {
    background-color: #5cb85c;
    color: white;
}

.btn-success-edit {
    background-color: #f0ad4e;
    color: white;
    border-radius: 0;
}

.btn-danger-delete {
    background-color: #d9534f;
    color: white;
    border-radius: 0;
}

.table th {
    background-color: #f8f9fa;
}

.animated-fade-in {
    animation: fadeIn 0.5s;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.border-radius-lg {
    border-radius: 0.5rem;
}

.shadow-lg {
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.form-select:focus,
.form-control:focus {
    border-color: #5cb85c;
    box-shadow: 0 0 0 0.25rem rgba(92, 184, 92, 0.25);
}
</style>
