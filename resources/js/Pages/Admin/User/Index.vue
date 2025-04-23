<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import { router } from '@inertiajs/vue3';


const { users, message, success, bomons, chucvus } = defineProps({
    users: {
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
    bomons: {
        type: Array,
        required: true,
    },
    chucvus: {
        type: Array,
        required: true,
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

console.log(users);

const deleteUser = (id) => {
    const user = users.data.find((user) => user.id === id);
    if (!user) {
        alert("Người dùng không tồn tại!");
        return;
    }

    const userTen = user.ten || "Không có tên";
    if (confirm(`Bạn có muốn xóa Người dùng "${userTen}" không?`)) {
        return true;
    }
    return false;
};
const searchTerm = ref("");
const selectedBoMon = ref("");
const selectedChucVu = ref("");
const debounceTimeout = ref(null);

const performSearch = () => {
    if (debounceTimeout.value) {
        clearTimeout(debounceTimeout.value);
    }
    debounceTimeout.value = setTimeout(() => {
        router.get(
            route("admin.user.index"),
            {
                search: searchTerm.value,
                id_bo_mon: selectedBoMon.value,
                id_chuc_vu: selectedChucVu.value,
            },
            {
                preserveState: true,
                replace: true,
            }
        );
    }, 300); // Delay 300ms để tránh gọi API quá nhiều
};

// Theo dõi thay đổi của các biến tìm kiếm
watch([searchTerm, selectedBoMon, selectedChucVu], () => {
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
                <a :href="route('admin.user.index')">Người dùng</a>
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
                                    DANH SÁCH NGƯỜI DÙNG
                                </h3>
                            </div>
                            <div class="col-md-4 text-end">
                                <Link
                                    :href="route('admin.user.create')"
                                    class="btn btn-light"
                                >
                                    <i class="fas fa-user-plus"></i> Thêm mới
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Bộ lọc -->
                    <div class="card-body pb-0">
                        <div class="row mb-4">
                            <div class="col-md-4 mb-3">
                                <div class="input-group">
                                    <input
                                        v-model="searchTerm"
                                        type="text"
                                        class="form-control"
                                        placeholder="Tìm theo ID, tên, email, sdt"
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
                                    v-model="selectedBoMon"
                                    class="form-select"
                                >
                                    <option value="">Tất cả Bộ môn</option>
                                    <option
                                        v-for="bomon in bomons"
                                        :key="bomon.id"
                                        :value="bomon.id"
                                    >
                                        {{ bomon.ten }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <select
                                    v-model="selectedChucVu"
                                    class="form-select"
                                >
                                    <option value="">Tất cả Chức vụ</option>
                                    <option
                                        v-for="chucvu in chucvus"
                                        :key="chucvu.id"
                                        :value="chucvu.id"
                                    >
                                        {{ chucvu.ten }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Bảng hiển thị danh sách người dùng -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên</th>
                                        <th>Email</th>
                                        <th>Chức vụ</th>
                                        <th>Khoa</th>
                                        <th>Bộ môn</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="users.data.length === 0">
                                        <td colspan="7" class="text-center">
                                            Không có dữ liệu
                                        </td>
                                    </tr>
                                    <tr
                                        v-for="user in users.data"
                                        :key="user.id"
                                    >
                                        <td>{{ user?.id }}</td>
                                        <td>{{ user?.name }}</td>
                                        <td
                                            style="
                                                word-wrap: break-word;
                                                max-width: 150px;
                                            "
                                        >
                                            {{ user?.email }}
                                        </td>
                                        <td
                                            style="
                                                word-wrap: break-word;
                                                max-width: 150px;
                                            "
                                        >
                                            {{ user?.chucvu?.ten }}
                                        </td>
                                        <td
                                            style="
                                                word-wrap: break-word;
                                                max-width: 150px;
                                            "
                                        >
                                            {{ user?.bomon?.khoa?.ten }}
                                        </td>
                                        <td
                                            style="
                                                word-wrap: break-word;
                                                max-width: 150px;
                                            "
                                        >
                                            {{ user?.bomon?.ten }}
                                        </td>
                                        <td class="d-flex" style="word-wrap: break-word;
                                                max-width: 150px;">
                                            <Link
                                                :href="route('admin.user.edit', user.id)"
                                                class="btn btn-sm btn-success-edit me-2"
                                            >
                                                <i class="fas fa-edit"></i>
                                            </Link>
                                            <Link
                                                :href="route('admin.user.destroy', user.id)"
                                                method="delete"
                                                as="button"
                                                type="button"
                                                :data="{
                                                    id: user.id,
                                                    _method: 'delete',
                                                }"
                                                class="btn btn-sm btn-danger-delete"
                                                @click="
                                                    (e) => {
                                                        if (!deleteUser(user.id))
                                                            e.preventDefault();
                                                    }
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
                                <li
                                    class="page-item"
                                    :class="{
                                        disabled: users.current_page === 1,
                                    }"
                                >
                                    <Link
                                        :href="users.links[0]?.url || '#'"
                                        class="page-link rounded-circle"
                                        :class="{
                                            'disabled-link':
                                                !users.links[0]?.url,
                                        }"
                                    >
                                        <i class="fas fa-chevron-left"></i>
                                    </Link>
                                </li>
                                <li
                                    v-for="link in users.links.slice(1, -1)"
                                    :key="link.label"
                                    class="page-item"
                                    :class="{
                                        active:
                                            link.active ||
                                            link.label == users.current_page,
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
                                            users.current_page === users.last_page,
                                    }"
                                >
                                    <Link
                                        :href="
                                            users.links[users.links.length - 1]
                                                ?.url || '#'
                                        "
                                        class="page-link rounded-circle"
                                        :class="{
                                            'disabled-link':
                                                !users.links[
                                                    users.links.length - 1
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
