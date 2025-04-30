<template>
    <AppLayout :role="role">
        <template #sub-link>
            <li class="breadcrumb-item active">
                <a :href="route('quality.thongkehocphan.index')">Thống kê học phần</a>
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
                                    THỐNG KÊ HỌC PHẦN ĐƯỢC BIÊN SOẠN
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
                            <div v-if="!thongke_data.nam_hoc || Object.keys(thongke_data.nam_hoc).length === 0" class="text-center py-5">
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
                                        <div class="col-md-6">
                                            <div class="stat-item">
                                                <span class="stat-label">Tổng số học phần:</span>
                                                <span class="stat-value">{{ thongke_data?.tong_hop?.tong_so_hoc_phan || 0 }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="stat-item">
                                                <span class="stat-label">Tổng số giờ:</span>
                                                <span class="stat-value">{{ thongke_data?.tong_hop?.tong_so_gio || 0 }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Danh sách theo cấu trúc phân cấp -->
                            <div class="accordion accordion-custom">
                                <!-- Năm học -->
                                <div v-for="(namHocData, namHoc) in thongke_data.nam_hoc" :key="namHoc" class="accordion-item">
                                    <div class="accordion-header" @click="toggleNamHoc(namHoc)">
                                        <div class="accordion-button" :class="{ 'collapsed': !expandedNamHoc[namHoc] }">
                                            <i class="fas" :class="expandedNamHoc[namHoc] ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                                            <span class="ms-2">Năm học {{ namHoc }}</span>
                                            <div class="ms-auto">
                                                <span class="badge bg-info me-2">{{ namHocData.tong_so_hoc_phan }} học phần</span>
                                                <span class="badge bg-success">{{ namHocData.tong_so_gio }} giờ</span>
                                            </div>
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
                                                        <div class="ms-auto">
                                                            <span class="badge bg-info me-2">{{ hocKiData.tong_so_hoc_phan }} học phần</span>
                                                            <span class="badge bg-success">{{ hocKiData.tong_so_gio }} giờ</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="accordion-collapse" :class="{ 'show': expandedHocKi[`${namHoc}_${hocKi}`] }">
                                                    <div class="accordion-body">
                                                        <!-- Khoa -->
                                                        <div v-for="(khoaData, khoaId) in hocKiData.khoa" :key="khoaId" class="accordion-item">
                                                            <div class="accordion-header" @click="toggleKhoa(namHoc, hocKi, khoaId)">
                                                                <div class="accordion-button" :class="{ 'collapsed': !expandedKhoa[`${namHoc}_${hocKi}_${khoaId}`] }">
                                                                    <i class="fas" :class="expandedKhoa[`${namHoc}_${hocKi}_${khoaId}`] ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                                                                    <span class="ms-2">{{ khoaData.ten }}</span>
                                                                    <div class="ms-auto">
                                                                        <span class="badge bg-info me-2">{{ khoaData.tong_so_hoc_phan }} học phần</span>
                                                                        <span class="badge bg-success">{{ khoaData.tong_so_gio }} giờ</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="accordion-collapse" :class="{ 'show': expandedKhoa[`${namHoc}_${hocKi}_${khoaId}`] }">
                                                                <div class="accordion-body">
                                                                    <!-- Bộ môn -->
                                                                    <div v-for="(boMonData, boMonId) in khoaData.bo_mon" :key="boMonId" class="accordion-item">
                                                                        <div class="accordion-header" @click="toggleBoMon(namHoc, hocKi, khoaId, boMonId)">
                                                                            <div class="accordion-button" :class="{ 'collapsed': !expandedBoMon[`${namHoc}_${hocKi}_${khoaId}_${boMonId}`] }">
                                                                                <i class="fas" :class="expandedBoMon[`${namHoc}_${hocKi}_${khoaId}_${boMonId}`] ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                                                                                <span class="ms-2">{{ boMonData.ten }}</span>
                                                                                <div class="ms-auto">
                                                                                    <span class="badge bg-info me-2">{{ boMonData.tong_so_hoc_phan }} học phần</span>
                                                                                    <span class="badge bg-success">{{ boMonData.tong_so_gio }} giờ</span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        
                                                                        <div class="accordion-collapse" :class="{ 'show': expandedBoMon[`${namHoc}_${hocKi}_${khoaId}_${boMonId}`] }">
                                                                            <div class="accordion-body">
                                                                                <!-- Danh sách học phần -->
                                                                                <div v-for="(hp, index) in boMonData.hoc_phan" :key="index" class="card mb-3">
                                                                                    <div class="card-header bg-light">
                                                                                        <div class="d-flex justify-content-between align-items-center">
                                                                                            <h6 class="mb-0">
                                                                                                {{ hp.ten }}
                                                                                                <small class="text-muted">({{ hp.ma_hoc_phan }})</small>
                                                                                            </h6>
                                                                                            <div>
                                                                                                <span class="badge bg-secondary me-2">{{ hp.so_tin_chi }} tín chỉ</span>
                                                                                                <span class="badge" :class="getStatusBadgeClass(hp.trang_thai)">
                                                                                                    {{ getStatusText(hp.trang_thai) }}
                                                                                                </span>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="card-body">
                                                                                        <div class="row">
                                                                                           
                                                                                            <div class="col-md-6">
                                                                                                <p class="mb-2">
                                                                                                    <strong>Giảng viên tham gia:</strong>
                                                                                                </p>
                                                                                                <ul class="list-unstyled ps-3">
                                                                                                    <li v-for="(gv, gvIndex) in hp.giang_vien" :key="gvIndex">
                                                                                                        {{ gv.ten }} ({{ gv.so_gio }} giờ)
                                                                                                    </li>
                                                                                                </ul>
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

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ref, watch, computed, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    role: {
        type: String,
        default: ''
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
            nam_hoc: '',
            bac_dao_tao: ''
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
            route('quality.thongkehocphan.index'),
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
    const firstNamHoc = Object.keys(thongke_data.nam_hoc)[0];
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
    
    window.location.href = route('quality.thongkehocphan.excel') + '?' + queryParams;
};

// Hàm format date
const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString('vi-VN');
};

// Hàm lấy class cho badge trạng thái
const getStatusBadgeClass = (status) => {
    switch (status) {
        case 'Draft': return 'bg-warning';
        case 'Pending': return 'bg-primary';
        case 'Approved': return 'bg-secondary';
        case 'Rejected': return 'bg-danger';
        case 'Completed': return 'bg-success';
        default: return 'bg-secondary';
    }
};

// Hàm lấy text cho trạng thái
const getStatusText = (status) => {
    switch (status) {
        case 'Draft': return 'Draft';
        case 'Pending': return 'Pending';
        case 'Approved': return 'Approved';
        case 'Rejected': return 'Rejected';
        case 'Completed': return 'Completed';
        default: return 'Không xác định';
    }
};
</script>

<style scoped>
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

.stat-item {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-label {
    font-weight: 500;
    color: #6c757d;
}

.stat-value {
    font-size: 1.25rem;
    font-weight: 600;
    color: #2c3e50;
}

.badge {
    font-size: 0.875rem;
    padding: 0.5em 0.75em;
}

.bg-success-tb {
    background-color: #5cb85c;
}
</style> 