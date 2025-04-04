<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Link } from "@inertiajs/vue3";
import { router } from "@inertiajs/vue3";
import { ref, watch } from "vue";

const { chuanDauRas, message, success } = defineProps({
    chuanDauRas: {
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

const deleteChuanDauRa = (id) => {
    const confirmed = confirm("Bạn có chắc chắn muốn xóa Chuẩn đầu ra này?");
    if (confirmed) {
        router.delete(route("admin.chuandaura.destroy", id), {
            onSuccess: () => {
                alert("Chuẩn đầu ra đã được xóa thành công!");
            },
            onError: (errors) => {
                alert("Có lỗi xảy ra khi xóa Chuẩn đầu ra!");
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
            route("admin.chuandaura.index"),
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
                <a :href="route('admin.chuandaura.index')">Chuẩn đầu ra</a>
            </li>
        </template>
        <template v-slot:content>
            <div class="content">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">Chuẩn đầu ra</h3>
                        <div class="d-flex gap-2">
                            <!-- Ô tìm kiếm -->
                            <div class="input-group" style="width: 300px;">
                                <input
                                    v-model="searchTerm"
                                    type="text"
                                    class="form-control"
                                    placeholder="Tìm theo ID hoặc tên Chuẩn đầu ra..."
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
                                <option value="ten">Theo tên hoặc nội dung</option>
                            </select>
                        </div>
                        <!-- Nút thêm Chuẩn đầu ra -->
                        <Link
                            :href="route('admin.chuandaura.create')"
                            class="btn btn-success-add"
                        >
                            <i class="fas fa-user-plus"></i> Add Chuẩn đầu ra
                        </Link>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Chuẩn đầu ra</th>
                                        <th>Nội dung</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="chuanDauRas.data.length === 0">
                                        <td colspan="4" class="text-center">
                                            Không có dữ liệu
                                        </td>
                                    </tr>
                                    <tr v-for="chuanDauRa in chuanDauRas.data" :key="chuanDauRa.id">
                                        <td>{{ chuanDauRa?.id }}</td>
                                        <td>{{ chuanDauRa?.ten }}</td>
                                        <td>{{ chuanDauRa?.noi_dung }}</td>
                                        <td>
                                            <Link
                                                :href="route('admin.chuandaura.edit', chuanDauRa.id)"
                                                class="btn btn-sm btn-success-edit me-2"
                                            >
                                                <i class="fas fa-edit"></i>
                                            </Link>
                                            <button
                                                class="btn btn-sm btn-danger-delete"
                                                @click="deleteChuanDauRa(chuanDauRa.id)"
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
                                <li class="page-item" :class="{ disabled: chuanDauRas.current_page === 1 }">
                                    <Link
                                        :href="chuanDauRas.links[0]?.url || '#'"
                                        class="page-link rounded-circle"
                                        :class="{ 'disabled-link': !chuanDauRas.links[0]?.url }"
                                    >
                                        <i class="fas fa-chevron-left"></i>
                                    </Link>
                                </li>
                                <li
                                    v-for="link in chuanDauRas.links.slice(1, -1)"
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
                                    :class="{ disabled: chuanDauRas.current_page === chuanDauRas.last_page }"
                                >
                                    <Link
                                        :href="chuanDauRas.links[chuanDauRas.links.length - 1]?.url || '#'"
                                        class="page-link rounded-circle"
                                        :class="{ 'disabled-link': !chuanDauRas.links[chuanDauRas.links.length - 1]?.url }"
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
