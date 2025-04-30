<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ref, watch, computed, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    role: {
        type: String,
        default: () => ''
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
    },
    thongke_data: {
        type: Object,
        default: () => ({})
    }
});

// State cho collapse/expand của các phần
const expandedNamHoc = ref({});
const expandedHocKi = ref({});
const expandedKhoa = ref({});
const expandedBoMon = ref({});

// Các biến cho bộ lọc
const selectedKhoa = ref(props.filters.khoa_id || '');
const selectedBoMon = ref(props.filters.bomon_id || '');
const selectedHocKi = ref(props.filters.hoc_ki || '');
const selectedNamHoc = ref(props.filters.nam_hoc || '');
const debounceTimeout = ref(null);

// Lọc bộ môn theo khoa được chọn
const filteredBoMons = computed(() => {
    if (!selectedKhoa.value) return [];
    return props.bomons.filter(bm => bm.id_khoa === selectedKhoa.value);
});

// Reset bộ môn khi thay đổi khoa
watch(selectedKhoa, (newValue) => {
    selectedBoMon.value = '';
});

// Hàm tìm kiếm/lọc
const performSearch = () => {
    if (debounceTimeout.value) {
        clearTimeout(debounceTimeout.value);
    }
    debounceTimeout.value = setTimeout(() => {
        router.get(
            route('quality.thongke_giang_vien.index'),
            { 
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

// Theo dõi sự thay đổi của các bộ lọc và thực hiện tìm kiếm
watch([selectedKhoa, selectedBoMon, selectedHocKi, selectedNamHoc], () => {
    performSearch();
});

// Khởi tạo trạng thái mở rộng mặc định
onMounted(() => {
    // Mở rộng năm học đầu tiên
    const firstNamHoc = Object.keys(thongke_data.giang_vien)[0];
    if (firstNamHoc) {
        expandedNamHoc.value[firstNamHoc] = true;
    }
});

// Toggle expand/collapse cho năm học
const toggleNamHoc = (namHoc) => {
    expandedNamHoc.value[namHoc] = !expandedNamHoc.value[namHoc];
};

// Toggle expand/collapse cho học kỳ
const toggleHocKi = (namHoc, hocKi) => {
    const key = `${namHoc}_${hocKi}`;
    expandedHocKi.value[key] = !expandedHocKi.value[key];
};

// Toggle expand/collapse cho khoa
const toggleKhoa = (namHoc, hocKi, khoaId) => {
    const key = `${namHoc}_${hocKi}_${khoaId}`;
    expandedKhoa.value[key] = !expandedKhoa.value[key];
};

// Toggle expand/collapse cho bộ môn
const toggleBoMon = (namHoc, hocKi, khoaId, boMonId) => {
    const key = `${namHoc}_${hocKi}_${khoaId}_${boMonId}`;
    expandedBoMon.value[key] = !expandedBoMon.value[key];
};

// Hàm xuất Excel
const exportExcel = () => {
    const queryParams = new URLSearchParams({
        khoa_id: selectedKhoa.value,
        bomon_id: selectedBoMon.value,
        hoc_ki: selectedHocKi.value,
        nam_hoc: selectedNamHoc.value
    }).toString();
    
    window.location.href = route('quality.thongke_giang_vien.excel') + '?' + queryParams;
};

// Các hàm xử lý chọn lọc
const selectNamHoc = (nam) => {
    selectedNamHoc.value = nam;
    selectedHocKi.value = '';
    selectedKhoa.value = '';
    selectedBoMon.value = '';
    performSearch();
};

const selectHocKi = (hk) => {
    selectedHocKi.value = hk;
    selectedKhoa.value = '';
    selectedBoMon.value = '';
    performSearch();
};

const selectKhoa = (khoaId) => {
    selectedKhoa.value = khoaId;
    selectedBoMon.value = '';
    performSearch();
};

const selectBoMon = (bomonId) => {
    selectedBoMon.value = bomonId;
    performSearch();
};

const resetFilters = () => {
    selectedNamHoc.value = '';
    selectedHocKi.value = '';
    selectedKhoa.value = '';
    selectedBoMon.value = '';
    performSearch();
};
</script>

<template>
    <AppLayout :role="role">
        <template #sub-link>
            <li class="breadcrumb-item active">
                <a :href="route('quality.thongke_giang_vien.index')">Thống kê giảng viên</a>
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
                                    THỐNG KÊ GIẢNG VIÊN THAM GIA BIÊN SOẠN 
                                </h3>
                            </div>
                            <div class="col-md-4 text-end">
                                <button class="btn btn-light" @click="exportExcel">
                                    <i class="fas fa-file-excel mr-1"></i> Xuất Excel
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Bộ lọc -->
                    <div class="card-body pb-0">
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <label class="form-label">Khoa</label>
                                <select class="form-select" v-model="selectedKhoa">
                                    <option value="">Tất cả Khoa</option>
                                    <option v-for="khoa in khoas" :key="khoa.id" :value="khoa.id">
                                        {{ khoa.ten }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Bộ môn</label>
                                <select class="form-select" v-model="selectedBoMon" :disabled="!selectedKhoa">
                                    <option value="">{{ selectedKhoa ? 'Chọn bộ môn' : 'Vui lòng chọn Khoa trước' }}</option>
                                    <option v-for="bm in filteredBoMons" :key="bm.id" :value="bm.id">
                                        {{ bm.ten }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Học kỳ</label>
                                <select class="form-select" v-model="selectedHocKi">
                                    <option value="">Tất cả học kỳ</option>
                                    <option v-for="hk in ds_hoc_ki" :key="hk" :value="hk">
                                        Học kỳ {{ hk }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Năm học</label>
                                <select class="form-select" v-model="selectedNamHoc">
                                    <option value="">Tất cả năm học</option>
                                    <option v-for="nam in ds_nam_hoc" :key="nam" :value="nam">
                                        {{ nam }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Nội dung thống kê -->
                    <div class="card-body">
                        <div class="thongke-content">
                            <!-- Danh sách trống -->
                            <div v-if="!thongke_data.giang_vien || Object.keys(thongke_data.giang_vien).length === 0" class="text-center py-5">
                                <h5 class="text-muted">Không có dữ liệu thống kê</h5>
                                <p>Vui lòng chọn các tiêu chí lọc khác để xem thống kê</p>
                            </div>
                            
                            <!-- Tổng hợp chung -->
                            <div v-else class="card mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Tổng hợp chung</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="stat-item">
                                                <span class="stat-label">Tổng số giảng viên tham gia:</span>
                                                <span class="stat-value">{{ thongke_data?.tong_hop?.tong_so_giang_vien || 0 }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Danh sách theo cấu trúc phân cấp -->
                            <div class="accordion accordion-custom">
                                <!-- Năm học -->
                                <div v-for="(namHocData, namHoc) in thongke_data.giang_vien" :key="namHoc" class="accordion-item">
                                    <div class="accordion-header" @click="toggleNamHoc(namHoc)">
                                        <div class="accordion-button" :class="{ 'collapsed': !expandedNamHoc[namHoc] }">
                                            <i class="fas" :class="expandedNamHoc[namHoc] ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                                            <span class="ms-2">Năm học {{ namHoc }}</span>
                                            <span class="badge bg-primary ms-2">{{ namHocData.tong_so_giang_vien }} giảng viên</span>
                                        </div>
                                    </div>
                                    
                                    <div class="accordion-collapse" :class="{ 'show': expandedNamHoc[namHoc] }">
                                        <div class="accordion-body">
                                            <!-- Học kỳ -->
                                            <div v-for="(hocKiData, hocKi) in namHocData.hoc_ki" :key="hocKi" class="accordion-item">
                                                <div class="accordion-header" @click="toggleHocKi(namHoc, hocKi)">
                                                    <div class="accordion-button" :class="{ 'collapsed': !expandedHocKi[`${namHoc}_${hocKi}`] }">
                                                        <i class="fas" :class="expandedHocKi[`${namHoc}_${hocKi}`] ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                                                        <span class="ms-2">Học kỳ {{ hocKi }}</span>
                                                        <span class="badge bg-primary ms-2">{{ hocKiData.tong_so_giang_vien }} giảng viên</span>
                                                    </div>
                                                </div>
                                                
                                                <div class="accordion-collapse" :class="{ 'show': expandedHocKi[`${namHoc}_${hocKi}`] }">
                                                    <div class="accordion-body">
                                                        <!-- Khoa -->
                                                        <div v-for="(khoaData, khoaId) in hocKiData.khoa" :key="khoaId" class="accordion-item">
                                                            <div class="accordion-header" @click="toggleKhoa(namHoc, hocKi, khoaId)">
                                                                <div class="accordion-button" :class="{ 'collapsed': !expandedKhoa[`${namHoc}_${hocKi}_${khoaId}`] }">
                                                                    <i class="fas" :class="expandedKhoa[`${namHoc}_${hocKi}_${khoaId}`] ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                                                                    <span class="ms-2">{{ khoas.find(k => k.id === khoaId)?.ten }}</span>
                                                                    <span class="badge bg-primary ms-2">{{ khoaData.tong_so_giang_vien }} giảng viên</span>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="accordion-collapse" :class="{ 'show': expandedKhoa[`${namHoc}_${hocKi}_${khoaId}`] }">
                                                                <div class="accordion-body">
                                                                    <!-- Bộ môn -->
                                                                    <div v-for="(boMonData, boMonId) in khoaData.bo_mon" :key="boMonId" class="accordion-item">
                                                                        <div class="accordion-header" @click="toggleBoMon(namHoc, hocKi, khoaId, boMonId)">
                                                                            <div class="accordion-button" :class="{ 'collapsed': !expandedBoMon[`${namHoc}_${hocKi}_${khoaId}_${boMonId}`] }">
                                                                                <i class="fas" :class="expandedBoMon[`${namHoc}_${hocKi}_${khoaId}_${boMonId}`] ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                                                                                <span class="ms-2">{{ bomons.find(bm => bm.id === boMonId)?.ten }}</span>
                                                                                <span class="badge bg-primary ms-2">{{ boMonData.tong_so_giang_vien }} giảng viên</span>
                                                                            </div>
                                                                        </div>
                                                                        
                                                                        <div class="accordion-collapse" :class="{ 'show': expandedBoMon[`${namHoc}_${hocKi}_${khoaId}_${boMonId}`] }">
                                                                            <div class="accordion-body">
                                                                                <!-- Danh sách giảng viên -->
                                                                                <div v-for="(gv, index) in boMonData.giang_vien" :key="index" class="card mb-3">
                                                                                    <div class="card-header bg-light">
                                                                                        <h6 class="mb-0">{{ gv.ten }}</h6>
                                                                                    </div>
                                                                                    <div class="card-body">
                                                                                        <div class="row">
                                                                                            <div class="col-12">
                                                                                                <h6 class="mb-3">Danh sách học phần tham gia biên soạn:</h6>
                                                                                                <div class="ps-3">
                                                                                                    <div v-for="(mon, monIndex) in gv.chi_tiet_mon" :key="monIndex" class="mb-2">
                                                                                                        <div class="d-flex justify-content-between align-items-center">
                                                                                                            <div>
                                                                                                                <span class="fw-bold">{{ mon.ten_mon }}</span>
                                                                                                                <div class="text-muted small">
                                                                                                                    <span>Mã học phần: {{ mon.ma_mon }}</span>
                                                                                                                    <span class="mx-2">|</span>
                                                                                                                    <span>Số tín chỉ: {{ mon.so_tin_chi }}</span>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                            <span class="badge bg-info">{{ mon.so_gio }} giờ</span>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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

.btn-light {
    background-color: #f8f9fa;
    border-color: #f8f9fa;
}

.btn-light:hover {
    background-color: #e2e6ea;
    border-color: #dae0e5;
}

.bg-success-tb {
    background-color: #5cb85c;
}

.accordion-custom .accordion-item {
    border: 1px solid #e9ecef;
    border-radius: 0.5rem;
    margin-bottom: 0.5rem;
    overflow: hidden;
}

.accordion-custom .accordion-header {
    padding: 0;
    cursor: pointer;
}

.accordion-custom .accordion-button {
    padding: 1rem;
    background-color: #f8f9fa;
    display: flex;
    align-items: center;
    font-weight: 500;
    color: #333;
    border: none;
    width: 100%;
    text-align: left;
}

.accordion-custom .accordion-button.collapsed {
    background-color: white;
}

.accordion-custom .accordion-button:not(.collapsed) {
    background-color: #f0f3f5;
    color: #2c3e50;
    box-shadow: none;
}

.accordion-custom .accordion-collapse {
    transition: all 0.2s ease;
    max-height: 0;
    overflow: hidden;
}

.accordion-custom .accordion-collapse.show {
    max-height: 5000px;
}

.accordion-custom .accordion-body {
    padding: 1rem;
}

.animated-fade-in {
    animation: fadeIn 0.5s;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.form-select {
    display: block;
    width: 100%;
    padding: 0.375rem 2.25rem 0.375rem 0.75rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #212529;
    background-color: #fff;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 16px 12px;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    appearance: none;
}

.form-select:focus {
    border-color: #86b7fe;
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.form-label {
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.badge {
    font-size: 0.875rem;
    padding: 0.5em 0.75em;
}

.card-header {
    padding: 0.75rem 1.25rem;
}

.card-body {
    padding: 1.25rem;
}

.mb-2 {
    margin-bottom: 0.75rem !important;
}

.ps-3 {
    padding-left: 1rem !important;
}
</style> 