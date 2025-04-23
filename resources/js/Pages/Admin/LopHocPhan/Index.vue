<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/vue3";
import { router } from '@inertiajs/vue3';
import { ref, watch, computed } from "vue";

const { lophocphans, khoas, hoc_phans, message, success } = defineProps({
    lophocphans: {
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
    khoas: {
        type: Array,
        required: true,
        default: () => [],
    },
    hoc_phans: {
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
const selectedKyHoc = ref(lophocphans.filters?.ky_hoc || "");
const selectedNamHoc = ref(lophocphans.filters?.nam_hoc || "");
const selectedKhoa = ref(lophocphans.filters?.id_khoa || "");
const selectedHocPhan = ref(lophocphans.filters?.id_hoc_phan || "");
const searchTerm = ref(lophocphans.filters?.search || "");
const debounceTimeout = ref(null);

// Tạo mảng năm học theo định dạng "năm - năm+1"
const schoolYears = computed(() => {
    const currentYear = new Date().getFullYear();
    return Array.from({ length: 10 }, (_, i) => {
        const startYear = currentYear - i;
        const endYear = startYear + 1;
        return {
            value: `${startYear}-${endYear}`,
            label: `${startYear} - ${endYear}`
        };
    });
});

const deleteLopHocPhan = (id) => {
    const confirmed = confirm('Bạn có chắc chắn muốn xóa Lớp học phần này?');
    if (confirmed) {
        router.delete(route('admin.lophocphan.destroy', id), {
            onSuccess: () => {
                alert('Lớp học phần đã được xóa thành công!');
            },
            onError: (errors) => {
                alert('Có lỗi xảy ra khi xóa Lớp học phần!');
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
            route("admin.lophocphan.index"),
            {
                ky_hoc: selectedKyHoc.value,
                nam_hoc: selectedNamHoc.value,
                id_khoa: selectedKhoa.value,
                id_hoc_phan: selectedHocPhan.value,
                search: searchTerm.value,
            },
            {
                preserveState: true,
                replace: true,
            }
        );
    }, 300); // Delay 300ms
};

// Theo dõi thay đổi của các biến tìm kiếm
watch([selectedKyHoc, selectedNamHoc, selectedKhoa, selectedHocPhan, searchTerm], () => {
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
                <a :href="route('admin.lophocphan.index')">Lớp học phần</a>
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
                                    DANH SÁCH LỚP HỌC PHẦN
                                </h3>
                            </div>
                            <div class="col-md-4 text-end">
                                <Link
                                    :href="route('admin.lophocphan.create')"
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
                            <div class="col-12 mb-3">
                                <div class="input-group">
                                    <input
                                        v-model="searchTerm"
                                        type="text"
                                        class="form-control"
                                        placeholder="Tìm theo ID, tên lớp học phần hoặc tên giảng viên..."
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
                                    v-model="selectedKyHoc"
                                    class="form-select"
                                >
                                    <option value="">Tất cả Học kỳ</option>
                                    <option value="1">Học kỳ 1</option>
                                    <option value="2">Học kỳ 2</option>
                                    <option value="Hè">Học kỳ Hè</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <select
                                    v-model="selectedNamHoc"
                                    class="form-select"
                                >
                                    <option value="">Tất cả Năm học</option>
                                    <option v-for="year in schoolYears" :key="year.value" :value="year.value">
                                        {{ year.label }}
                                    </option>
                                </select>
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
                                    v-model="selectedHocPhan"
                                    class="form-select"
                                >
                                    <option value="">Tất cả Học phần</option>
                                    <option v-for="hocphan in hoc_phans" :key="hocphan.id" :value="hocphan.id">
                                        {{ hocphan.ten }}
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
                                        <th>Lớp học phần</th>
                                        <th>Kỳ học</th>
                                        <th>Năm học</th>
                                        <th>Khoa</th>
                                        <th>Học phần</th>
                                        <th>Giảng viên</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="lophocphans.data.length === 0">
                                        <td colspan="8" class="text-center">
                                            Không có dữ liệu
                                        </td>
                                    </tr>
                                    <tr v-for="lophocphan in lophocphans.data" :key="lophocphan.id">
                                        <td>{{ lophocphan?.id }}</td>
                                        <td>{{ lophocphan?.ten }}</td>
                                        <td>{{ lophocphan?.ky_hoc }}</td>
                                        <td>{{ lophocphan?.nam_hoc }}</td>
                                        <td>{{ lophocphan?.khoa?.ten }}</td>
                                        <td>{{ lophocphan?.hoc_phan?.ten }}</td>
                                        <td>{{ lophocphan?.vien_chuc?.name }}</td>
                                        <td>
                                            <div class="d-flex">
                                                <Link
                                                    :href="route('admin.lophocphan.edit', lophocphan.id)"
                                                    class="btn btn-sm btn-success-edit me-2"
                                                >
                                                    <i class="far fa-edit"></i>
                                                </Link>
                                                <button
                                                    class="btn btn-sm btn-danger-delete"
                                                    @click="deleteLopHocPhan(lophocphan.id)"
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
                                <li class="page-item" :class="{ disabled: lophocphans.current_page === 1 }">
                                    <Link
                                        :href="lophocphans.links[0]?.url || '#'"
                                        class="page-link rounded-circle"
                                        :class="{ 'disabled-link': !lophocphans.links[0]?.url }"
                                    >
                                        <i class="fas fa-chevron-left"></i>
                                    </Link>
                                </li>
                                <li
                                    v-for="link in lophocphans.links.slice(1, -1)"
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
                                    :class="{ disabled: lophocphans.current_page === lophocphans.last_page }"
                                >
                                    <Link
                                        :href="lophocphans.links[lophocphans.links.length - 1]?.url || '#'"
                                        class="page-link rounded-circle"
                                        :class="{ 'disabled-link': !lophocphans.links[lophocphans.links.length - 1]?.url }"
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
