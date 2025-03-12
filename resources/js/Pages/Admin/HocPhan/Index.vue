<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Link } from "@inertiajs/vue3";
import { router } from '@inertiajs/vue3';
import { ref, watch } from "vue";

const { hocphans, bomons, bacdaotaos, message, success } = defineProps({
    hocphans: {
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
        default: () => [],
    },
    bacdaotaos: {
        type: Array,
        required: true,
        default: () => [],
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

// Biến cho chức năng tìm kiếm
const searchTerm = ref("");
const selectedBoMon = ref("");
const selectedBacDaoTao = ref("");
const debounceTimeout = ref(null);

const deleteHocPhan = (id) => {
    const confirmed = confirm('Bạn có chắc chắn muốn xóa Học phần này?');
    if (confirmed) {
        router.delete(route('admin.hocphan.destroy', id), {
            onSuccess: () => {
                alert('Học phần đã được xóa thành công!');
            },
            onError: (errors) => {
                alert('Có lỗi xảy ra khi xóa Học phần!');
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
            route("admin.hocphan.index"),
            {
                search: searchTerm.value,
                id_bo_mon: selectedBoMon.value,
                id_bac_dao_tao: selectedBacDaoTao.value,
            },
            {
                preserveState: true,
                replace: true,
            }
        );
    }, 300); // Delay 300ms để tránh gọi API quá nhiều
};

// Theo dõi thay đổi của các biến tìm kiếm
watch([searchTerm, selectedBoMon, selectedBacDaoTao], () => {
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
                <a :href="route('admin.hocphan.index')">Học phần</a>
            </li>
        </template>
        <template v-slot:content>
            <div class="content">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">Học phần</h3>
                        <div class="d-flex gap-2 align-items-center " >
                            <!-- Ô tìm kiếm -->
                            <div class="input-group" style="width: 300px;">
                                <input
                                    v-model="searchTerm"
                                    type="text"
                                    class="form-control"
                                    placeholder="Tìm theo ID hoặc tên Học phần..."
                                    @keyup="handleSearch"
                                />
                                <button
                                    class="btn btn-success-add"
                                    @click="performSearch"
                                >
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            <!-- Select Bộ môn -->
                            <select
                                v-model="selectedBoMon"
                                class="form-control"
                                style="width: 200px;"
                            >
                                <option value="">Tất cả Bộ môn</option>
                                <option v-for="bomon in bomons" :key="bomon.id" :value="bomon.id">
                                    {{ bomon.ten }}
                                </option>
                            </select>
                            <!-- Select Bậc đào tạo -->
                            <select
                                v-model="selectedBacDaoTao"
                                class="form-control"
                                style="width: 200px;"
                            >
                                <option value="">Tất cả Bậc đào tạo</option>
                                <option v-for="bacdaotao in bacdaotaos" :key="bacdaotao.id" :value="bacdaotao.id">
                                    {{ bacdaotao.ten }}
                                </option>
                            </select>
                            <!-- Nút thêm Học phần -->
                           
                        </div>
                        <Link
                                :href="route('admin.hocphan.create')"
                                class="btn btn-success-add ml-2"
                            >
                                <i class="fas fa-user-plus"></i> Add Học phần
                            </Link>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Học phần</th>
                                        <th>Số tín chỉ</th>
                                        <th>Bộ môn</th>
                                        <th>Bậc đào tạo</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="hocphans.data.length === 0">
                                        <td colspan="6" class="text-center">
                                            Không có dữ liệu
                                        </td>
                                    </tr>
                                    <tr v-for="hocphan in hocphans.data" :key="hocphan.id">
                                        <td>{{ hocphan.id }}</td>
                                        <td>{{ hocphan.ten }}</td>
                                        <td>{{ hocphan.so_tin_chi }}</td>
                                        <td>{{ hocphan.bomon.ten }}</td>
                                        <td>{{ hocphan.bacdaotao.ten }}</td>
                                        <td>
                                            <Link
                                                :href="route('admin.hocphan.edit', hocphan.id)"
                                                class="btn btn-sm btn-success-edit me-2"
                                            >
                                                <i class="fas fa-edit"></i>
                                            </Link>
                                            <button
                                                class="btn btn-sm btn-danger-delete"
                                                @click="deleteHocPhan(hocphan.id)"
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
                                <li class="page-item" :class="{ disabled: hocphans.current_page === 1 }">
                                    <Link
                                        :href="hocphans.links[0]?.url || '#'"
                                        class="page-link rounded-circle"
                                        :class="{ 'disabled-link': !hocphans.links[0]?.url }"
                                    >
                                        <i class="fas fa-chevron-left"></i>
                                    </Link>
                                </li>
                                <li
                                    v-for="link in hocphans.links.slice(1, -1)"
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
                                    :class="{ disabled: hocphans.current_page === hocphans.last_page }"
                                >
                                    <Link
                                        :href="hocphans.links[hocphans.links.length - 1]?.url || '#'"
                                        class="page-link rounded-circle"
                                        :class="{ 'disabled-link': !hocphans.links[hocphans.links.length - 1]?.url }"
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
