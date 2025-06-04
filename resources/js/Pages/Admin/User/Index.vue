<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/vue3";
import { ref, watch, computed } from "vue";
import { router } from '@inertiajs/vue3';


const props = defineProps({
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
    khoas: {
        type: Array,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({
            search: '',
            id_khoa: '',
            id_bo_mon: '',
            id_chuc_vu: ''
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

console.log(props.users);

const deleteUser = (id) => {
    const user = props.users.data.find((user) => user.id === id);
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

const searchTerm = ref(props.filters.search || "");
const selectedKhoa = ref(props.filters.id_khoa || "");
const selectedBoMon = ref(props.filters.id_bo_mon || "");
const selectedChucVu = ref(props.filters.id_chuc_vu || "");
const debounceTimeout = ref(null);

// Lọc danh sách bộ môn theo khoa đã chọn
const filteredBoMons = computed(() => {
    if (!selectedKhoa.value) {
        return [];
    }
    return props.bomons.filter(bomon => bomon.id_khoa === selectedKhoa.value);
});

// Reset bộ môn khi thay đổi khoa
watch(selectedKhoa, (newVal) => {
    selectedBoMon.value = '';
});

const performSearch = () => {
    if (debounceTimeout.value) {
        clearTimeout(debounceTimeout.value);
    }
    debounceTimeout.value = setTimeout(() => {
        router.get(
            route("admin.user.index"),
            {
                search: searchTerm.value,
                id_khoa: selectedKhoa.value,
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
watch([searchTerm, selectedKhoa, selectedBoMon, selectedChucVu], () => {
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
                            <div class="col-md-3 mb-3">
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
                            <div class="col-md-3 mb-3">
                                <select
                                    v-model="selectedKhoa"
                                    class="form-select"
                                >
                                    <option value="">Tất cả Khoa</option>
                                    <option
                                        v-for="khoa in khoas"
                                        :key="khoa.id"
                                        :value="khoa.id"
                                    >
                                        {{ khoa.ten }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <select
                                    v-model="selectedBoMon"
                                    class="form-select"
                                    :disabled="!selectedKhoa"
                                >
                                    <option value="">{{ selectedKhoa ? 'Tất cả Bộ môn' : 'Vui lòng chọn Khoa trước' }}</option>
                                    <option
                                        v-for="bomon in filteredBoMons"
                                        :key="bomon.id"
                                        :value="bomon.id"
                                    >
                                        {{ bomon.ten }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
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
                                        <th style="width: 10%">ID</th>
                                        <th style="width: 15%">Tên</th>
                                        <th style="width: 20%">Email</th>
                                        <th style="width: 15%">Chức vụ</th>
                                        <th style="width: 15%">Khoa</th>
                                        <th style="width: 15%">Bộ môn</th>
                                        <th style="width: 10%">Actions</th>
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
                                        <td>
                                            <div class="text-truncate" :title="user?.name">
                                                {{ user?.name }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-truncate" :title="user?.email">
                                                {{ user?.email }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-truncate" :title="user?.chucvu?.ten">
                                                {{ user?.chucvu?.ten }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-truncate" :title="user?.bomon?.khoa?.ten">
                                                {{ user?.bomon?.khoa?.ten }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-truncate" :title="user?.bomon?.ten">
                                                {{ user?.bomon?.ten }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex">
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
    position: sticky;
    top: 0;
    z-index: 1;
    white-space: nowrap;
}

.table td {
    vertical-align: middle;
}

.text-truncate {
    max-width: 100%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
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

.table-responsive {
    min-height: 300px;
}
</style>
