<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
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
    <AdminLayout>
        <template v-slot:sub-link>
            <li class="breadcrumb-item active">
                <a :href="route('admin.user.index')">Người dùng</a>
            </li>
        </template>
        <template v-slot:content>
            <div class="content">
                <div class="card">
                    <div
                        class="card-header d-flex justify-content-between align-items-center"
                    >
                        <h3 class="mb-0">Người dùng</h3>
                        <Link
                            :href="route('admin.user.create')"
                            class="btn btn-success-add"
                        >
                            <i class="fas fa-user-plus"></i> Add Người dùng
                        </Link>
                    </div>
                    <div class="card-body">
                        <div class="search-form mb-4">
                            <div class="row justify-content-between">
                                <div class="col-md-4 col-sm-12">
                                        <input
                                            v-model="searchTerm"
                                            type="text"
                                            class="form-control"
                                            placeholder="Tìm theo ID, tên, email, sdt"
                                            @keyup="handleSearch"
                                        />
                                    </div>
                                    <select
                                        v-model="selectedBoMon"
                                        class="form-control col-md-4 col-sm-12"
                                        style="width: 200px"
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
                                    <select
                                        v-model="selectedChucVu"
                                        class="form-control col-md-4 col-sm-12"
                                        style="width: 200px"
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
                                        <td colspan="4" class="text-center">
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
                                        <td>
                                            <Link
                                                :href="
                                                    route(
                                                        'admin.user.edit',
                                                        user.id
                                                    )
                                                "
                                                class="btn btn-sm btn-success-edit me-2"
                                            >
                                                <i class="fas fa-edit"></i>
                                            </Link>
                                            <Link
                                                :href="
                                                    route(
                                                        'admin.user.show',
                                                        user.id
                                                    )
                                                "
                                                class="btn btn-sm btn-primary me-2"
                                                style="border-radius: 50%"
                                            >
                                                <i class="fas fa-eye"></i>
                                            </Link>
                                            <Link
                                                :href="
                                                    route(
                                                        'admin.user.destroy',
                                                        user.id
                                                    )
                                                "
                                                method="delete"
                                                as="button"
                                                class="btn btn-sm btn-danger-delete"
                                                @click.prevent="
                                                    deleteUser(user.id) ||
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

                                <!-- Các số trang -->
                                <li
                                    v-for="link in users.links.slice(1, -1)"
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
                                            users.current_page ===
                                            users.last_page,
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
    </AdminLayout>
</template>
