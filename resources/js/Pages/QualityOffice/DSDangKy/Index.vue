<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    dsdangky: {
        type: Object,
        default: () => ({
            data: []
        })
    },
    khoas: {
        type: Array,
        default: () => []
    },
    bomons: {
        type: Array,
        default: () => []
    },
    ds_hoc_ki: {
        type: Array,
        default: () => ['1', '2', 'Hè']
    },
    ds_nam_hoc: {
        type: Array,
        default: () => []
    },
    filters: {
        type: Object,
        default: () => ({
            khoa_id: '',
            bomon_id: '',
            hoc_ki: '',
            nam_hoc: ''
        })
    }
});

// Các biến cho bộ lọc
const searchTerm = ref(props.filters.search || '');
const selectedKhoa = ref(props.filters.khoa_id || '');
const selectedBoMon = ref(props.filters.bomon_id || '');
const selectedHocKi = ref(props.filters.hoc_ki || '');
const selectedNamHoc = ref(props.filters.nam_hoc || '');
const debounceTimeout = ref(null);

// Lọc bộ môn theo khoa được chọn
const filteredBoMons = computed(() => {
    if (!selectedKhoa.value) return props.bomons;
    return props.bomons.filter(bm => bm.id_khoa == selectedKhoa.value);
});

// Reset bộ môn khi thay đổi khoa
watch(selectedKhoa, (newValue, oldValue) => {
    if (newValue !== oldValue) {
        selectedBoMon.value = '';
    }
});

// Hàm tìm kiếm
const performSearch = () => {
    if (debounceTimeout.value) {
        clearTimeout(debounceTimeout.value);
    }
    debounceTimeout.value = setTimeout(() => {
        router.get(
            route('quality.dsdangky.index'),
            { 
                search: searchTerm.value,
                khoa_id: selectedKhoa.value,
                bomon_id: selectedBoMon.value,
                hoc_ki: selectedHocKi.value,
                nam_hoc: selectedNamHoc.value
            },
            { 
                preserveState: true,
                replace: true 
            }
        );
    }, 300);
};

// Theo dõi sự thay đổi của các lọc và thực hiện tìm kiếm
watch([searchTerm, selectedKhoa, selectedBoMon, selectedHocKi, selectedNamHoc], () => {
    performSearch();
});

// Debug để xem dữ liệu
console.log('DSDangKy Data:', props.dsdangky);

const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString('vi-VN');
};

const getStatusBadgeClass = (status) => {
    const classes = 'badge ';
    switch (status) {
        case 'Approved':
            return classes + 'bg-success';
        case 'Rejected':
            return classes + 'bg-danger';
        case 'Pending':
            return classes + 'bg-warning';
        default:
            return classes + 'bg-secondary';
    }
};
</script>


<template>
    <AppLayout role="dbcl">
        <template #sub-link>
            <li class="breadcrumb-item active">
                <a :href="route('quality.dsdangky.index')">Danh sách đăng ký</a>
            </li>
        </template>

        <template #content>
            <div class="content">
                <div class="card border-radius-lg shadow-lg animated-fade-in">
                    <!-- Card Header -->
                    <div class="card-header bg-success-tb text-white p-4">
                        <div class="row justify-content-between align-items-center">
                            <div class="col-md-8">
                                <h3 class="mb-0 font-weight-bolder">
                                    DANH SÁCH ĐĂNG KÝ
                                </h3>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        placeholder="Tìm kiếm..." 
                                        v-model="searchTerm"
                                        @keyup.enter="performSearch"
                                    >
                                    <button 
                                        class="btn btn-light" 
                                        type="button"
                                        @click="performSearch"
                                    >
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bộ lọc -->
                    <div class="card-body pb-0">
                        <div class="row mb-4">
                            <div class="col-md-3 mb-3">
                                <label for="khoa" class="form-label">Khoa</label>
                                <select 
                                    id="khoa" 
                                    class="form-select" 
                                    v-model="selectedKhoa"
                                >
                                    <option value="">Tất cả Khoa</option>
                                    <option v-for="khoa in khoas" :key="khoa.id" :value="khoa.id">
                                        {{ khoa.ten }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="bomon" class="form-label">Bộ môn</label>
                                <select 
                                    id="bomon" 
                                    class="form-select" 
                                    v-model="selectedBoMon"
                                    :disabled="!selectedKhoa"
                                >
                                    <option value="">{{ selectedKhoa ? 'Tất cả Bộ môn của Khoa' : 'Vui lòng chọn Khoa trước' }}</option>
                                    <option v-for="bm in filteredBoMons" :key="bm.id" :value="bm.id">
                                        {{ bm.ten }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="hoc_ki" class="form-label">Học kỳ</label>
                                <select 
                                    id="hoc_ki" 
                                    class="form-select" 
                                    v-model="selectedHocKi"
                                >
                                    <option value="">Tất cả học kỳ</option>
                                    <option v-for="hk in ds_hoc_ki" :key="hk" :value="hk">
                                        Học kỳ {{ hk }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="nam_hoc" class="form-label">Năm học</label>
                                <select 
                                    id="nam_hoc" 
                                    class="form-select" 
                                    v-model="selectedNamHoc"
                                >
                                    <option value="">Tất cả năm học</option>
                                    <option v-for="nam in ds_nam_hoc" :key="nam" :value="nam">
                                        {{ nam }}
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
                                        <th>STT</th>
                                        <th>Bộ môn</th>
                                        <th>Học kỳ</th>
                                        <th>Năm học</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(ds, index) in dsdangky" :key="ds.id">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ ds.bo_mon?.ten }}</td>
                                        <td>{{ ds.hoc_ki }}</td>
                                        <td>{{ ds.nam_hoc }}</td>
                                        <td>
                                            <span :class="getStatusBadgeClass(ds.status)" class="badge">
                                                {{ ds.status }}
                                            </span>
                                        </td>
                                        <td>
                                            <Link 
                                                :href="route('quality.ctdsdangky.index', ds.id)"
                                                class="btn btn-sm btn-primary"
                                                title="Xem chi tiết"
                                            >
                                                <i class="fas fa-eye"></i>
                                            </Link>
                                        </td>
                                    </tr>
                                    <tr v-if="!dsdangky || dsdangky.length === 0">
                                        <td colspan="7" class="text-center">Không có dữ liệu</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
            
        </template>
    </AppLayout>
</template>


<style scoped>
.table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.badge {
    font-size: 0.85em;
    padding: 0.35em 0.65em;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.btn-sm i {
    font-size: 0.875rem;
}

.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.table {
    width: 100%;
    margin-bottom: 1rem;
    color: #212529;
    vertical-align: top;
    border-color: #dee2e6;
}

.table > :not(caption) > * > * {
    padding: 0.5rem;
    border-bottom-width: 1px;
}

.badge.bg-success {
    background-color: #198754 !important;
    color: white;
}

.badge.bg-danger {
    background-color: #dc3545 !important;
    color: white;
}

.badge.bg-warning {
    background-color: #ffc107 !important;
    color: black;
}

.badge.bg-secondary {
    background-color: #6c757d !important;
    color: white;
}

.form-select:focus,
.form-control:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.form-label {
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.animated-fade-in {
    animation: fadeIn 0.5s;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
</style>
