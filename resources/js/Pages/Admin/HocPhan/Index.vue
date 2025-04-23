<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/vue3";
import { router } from '@inertiajs/vue3';
import { ref, watch, computed } from "vue";

const { hocphans, bomons, bacdaotaos, khoas, message, success } = defineProps({
    hocphans: {
        type: Object,
        required: true,
        default: () => ({
            data: [],
            current_page: 1,
            last_page: 1,
            total: 0,
            links: [],
            filters: {}
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
    khoas: {
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
const searchTerm = ref(hocphans.filters?.search || "");
const selectedKhoa = ref(hocphans.filters?.khoa_id || "");
const selectedBoMon = ref(hocphans.filters?.id_bo_mon || "");
const selectedBacDaoTao = ref(hocphans.filters?.id_bac_dao_tao || "");
const debounceTimeout = ref(null);

// Lọc danh sách bộ môn theo khoa đã chọn
const filteredBoMons = computed(() => {
    if (!selectedKhoa.value) return bomons;
    return bomons.filter(bm => bm.id_khoa == selectedKhoa.value);
});

// Reset bộ môn khi thay đổi khoa
watch(selectedKhoa, (newValue, oldValue) => {
    if (newValue !== oldValue) {
        selectedBoMon.value = '';
    }
});

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
                khoa_id: selectedKhoa.value,
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
watch([searchTerm, selectedKhoa, selectedBoMon, selectedBacDaoTao], () => {
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
                <a :href="route('admin.hocphan.index')">Học phần</a>
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
                                    DANH SÁCH HỌC PHẦN
                                </h3>
                            </div>
                            <div class="col-md-4 text-end">
                                <Link
                                    :href="route('admin.hocphan.create')"
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
                            <div class="col-md-3 mb-3">
                                <div class="input-group">
                                    <input
                                        v-model="searchTerm"
                                        type="text"
                                        class="form-control"
                                        placeholder="Tìm kiếm học phần..."
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
                                    <option v-for="khoa in khoas" :key="khoa.id" :value="khoa.id">
                                        {{ khoa.ten }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <select
                                    v-model="selectedBacDaoTao"
                                    class="form-select"
                                >
                                    <option value="">Tất cả Bậc đào tạo</option>
                                    <option v-for="bacdaotao in bacdaotaos" :key="bacdaotao.id" :value="bacdaotao.id">
                                        {{ bacdaotao.ten }}
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
                                    <option v-for="bomon in filteredBoMons" :key="bomon.id" :value="bomon.id">
                                        {{ bomon.ten }}
                                    </option>
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
                                        <td>{{ hocphan?.id }}</td>
                                        <td>{{ hocphan?.ten }}</td>
                                        <td>{{ hocphan?.so_tin_chi }}</td>
                                        <td>{{ hocphan?.bomon?.ten }}</td>
                                        <td>{{ hocphan?.bacdaotao?.ten }}</td>
                                        <td>
                                            <div class="d-flex">
                                                <Link
                                                    :href="route('admin.hocphan.edit', hocphan.id)"
                                                    class="btn btn-sm btn-success-edit me-2"
                                                >
                                                    <i class="far fa-edit"></i>
                                                </Link>
                                                <button
                                                    class="btn btn-sm btn-danger-delete"
                                                    @click="deleteHocPhan(hocphan.id)"
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
                                    :class="{ disabled: hocphans.current_page === 1 }"
                                >
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
                                    :class="{
                                        active:
                                            link.active ||
                                            link.label == hocphans.current_page,
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
