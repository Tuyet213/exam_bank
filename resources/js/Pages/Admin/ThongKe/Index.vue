<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ref, watch, computed, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
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

// Computed property để lọc ra các năm học (loại bỏ tong_hop)
const filteredThongKeData = computed(() => {
    const data = { ...props.thongke_data };
    delete data.tong_hop;
    return data;
});

// State cho collapse/expand của các phần
const expandedNamHoc = ref({});
const expandedHocKi = ref({});
const expandedKhoa = ref({});
const expandedBoMon = ref({});

// Khởi tạo trạng thái mở rộng mặc định
onMounted(() => {
    // Mở rộng năm học đầu tiên
    const firstNamHoc = Object.keys(filteredThongKeData.value)[0];
    if (firstNamHoc) {
        expandedNamHoc.value[firstNamHoc] = true;
    }
});

// Các biến cho bộ lọc
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

// Hàm tìm kiếm/lọc
const performSearch = () => {
    if (debounceTimeout.value) {
        clearTimeout(debounceTimeout.value);
    }
    debounceTimeout.value = setTimeout(() => {
        router.get(
            route('quality.thongke.index'),
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
    
    window.location.href = route('quality.thongke.excel') + '?' + queryParams;
};

// Thêm các phương thức xử lý trạng thái
const getStatusClass = (status) => {
    switch(status) {
        case 'Approved':
            return 'badge bg-success';
        case 'Pending':
            return 'badge bg-warning';
        case 'Rejected':
            return 'badge bg-danger';
        case 'Draft':
            return 'badge bg-secondary';
        case 'Completed':
            return 'badge bg-success';
    }
};

const getStatusText = (status) => {
    switch(status) {
        case 'Approved':
            return 'Approved';
        case 'Pending':
            return 'Pending';
        case 'Rejected':
            return 'Rejected';
        case 'Draft':
            return 'Draft';
        case 'Completed':
            return 'Completed';
        default:
            return 'Không xác định';
    }
};
const exportExcelGioThamGia = () => {
    const queryParams = new URLSearchParams({
        khoa_id: selectedKhoa.value,
        bomon_id: selectedBoMon.value,
        hoc_ki: selectedHocKi.value,
        nam_hoc: selectedNamHoc.value
    }).toString();
    window.location.href = route('quality.thongke.excel_gio_tham_gia') + '?' + queryParams;
};
</script>

<template>
    <AppLayout role="admin">
        <template #sub-link>
            <li class="breadcrumb-item active">
                <a :href="route('quality.thongke.index')">Thống kê biên soạn</a>
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
                                    THỐNG KÊ BIÊN SOẠN NGÂN HÀNG ĐỀ THI/CÂU HỎI
                                </h3>
                            </div>
                            <div class="col-md-4 text-end">
                                <button class="btn btn-light" @click="exportExcel">
                                    <i class="fas fa-file-excel mr-1"></i> Xuất Excel
                                </button>
                                <button class="btn btn-warning ms-2" @click="exportExcelGioThamGia">
                                    <i class="fas fa-file-excel mr-1"></i> Xuất Excel giờ tham gia
                                </button>
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

                    <!-- Nội dung thống kê -->
                    <div class="card-body">
                        <div class="thongke-content">
                            <!-- Danh sách trống -->
                            <div v-if="Object.keys(filteredThongKeData).length === 0" class="text-center py-5">
                                <h5 class="text-muted">Không có dữ liệu thống kê</h5>
                                <p>Vui lòng chọn các tiêu chí lọc khác để xem thống kê</p>
                            </div>
                            
                            <!-- Tổng hợp chung -->
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Tổng hợp chung</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="stat-item">
                                                <span class="stat-label">Tổng số giờ:</span>
                                                <span class="stat-value">{{ thongke_data?.tong_hop?.tong_so_gio || 0 }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="stat-item">
                                                <span class="stat-label">Tổng số giảng viên tham gia:</span>
                                                <span class="stat-value">{{ thongke_data?.tong_hop?.tong_so_nguoi_tham_gia || 0 }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="stat-item">
                                                <span class="stat-label">Tổng số học phần:</span>
                                                <span class="stat-value">{{ thongke_data?.tong_hop?.tong_so_hoc_phan || 0 }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="stat-item">
                                                <span class="stat-label">Tổng số câu hỏi:</span>
                                                <span class="stat-value">{{ thongke_data?.tong_hop?.tong_so_cau_hoi || 0 }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="stat-item">
                                                <span class="stat-label">Tổng số đề thi:</span>
                                                <span class="stat-value">{{ thongke_data?.tong_hop?.tong_so_de_thi || 0 }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Danh sách năm học -->
                            <div class="accordion accordion-custom">
                                <div v-for="(namData, namHoc) in filteredThongKeData" :key="namHoc" class="accordion-item">
                                    <!-- Năm học -->
                                    <div class="accordion-header" @click="toggleNamHoc(namHoc)">
                                        <div class="accordion-button" :class="{ 'collapsed': !expandedNamHoc[namHoc] }">
                                            <i class="fas" :class="expandedNamHoc[namHoc] ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                                            <span class="ms-2">Năm học: {{ namData.ten }}</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Nội dung của năm học -->
                                    <div class="accordion-collapse" :class="{ 'show': expandedNamHoc[namHoc] }">
                                        <div class="accordion-body">
                                            <!-- Tổng hợp năm học -->
                                            <div class="card mb-4">
                                                <div class="card-header bg-light">
                                                    <h5 class="mb-0">Tổng hợp năm học {{ namData.ten }}</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <div class="stat-item">
                                                                <span class="stat-label">Tổng số giờ:</span>
                                                                <span class="stat-value">{{ namData?.tong_hop?.tong_so_gio || 0 }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="stat-item">
                                                                <span class="stat-label">Tổng số giảng viên tham gia:</span>
                                                                <span class="stat-value">{{ namData?.tong_hop?.tong_so_nguoi_tham_gia || 0 }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="stat-item">
                                                                <span class="stat-label">Tổng số học phần:</span>
                                                                <span class="stat-value">{{ namData?.tong_hop?.tong_so_hoc_phan || 0 }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="stat-item">
                                                                <span class="stat-label">Tổng số câu hỏi:</span>
                                                                <span class="stat-value">{{ namData?.tong_hop?.tong_so_cau_hoi || 0 }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="stat-item">
                                                                <span class="stat-label">Tổng số đề thi:</span>
                                                                <span class="stat-value">{{ namData?.tong_hop?.tong_so_de_thi || 0 }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Danh sách học kỳ -->
                                            <div v-for="(hocKiData, hocKi) in namData.hoc_ki" :key="hocKi" class="accordion-item">
                                                <!-- Học kỳ -->
                                                <div class="accordion-header" @click="toggleHocKi(namHoc, hocKi)">
                                                    <div class="accordion-button" :class="{ 'collapsed': !expandedHocKi[`${namHoc}_${hocKi}`] }">
                                                        <i class="fas" :class="expandedHocKi[`${namHoc}_${hocKi}`] ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                                                        <span class="ms-2">Học kỳ: {{ hocKiData.ten }}</span>
                                                    </div>
                                                </div>
                                                
                                                <!-- Nội dung của học kỳ -->
                                                <div class="accordion-collapse" :class="{ 'show': expandedHocKi[`${namHoc}_${hocKi}`] }">
                                                    <div class="accordion-body">
                                                        <!-- Tổng hợp học kỳ -->
                                                        <div class="card mb-4">
                                                            <div class="card-header bg-light">
                                                                <h5 class="mb-0">{{ hocKiData.ten }}</h5>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="row">
                                                                    <div class="col-md-3">
                                                                        <div class="stat-item">
                                                                            <span class="stat-label">Tổng số giờ:</span>
                                                                            <span class="stat-value">{{ hocKiData?.tong_hop?.tong_so_gio || 0 }}</span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="stat-item">
                                                                            <span class="stat-label">Tổng số giảng viên tham gia:</span>
                                                                            <span class="stat-value">{{ hocKiData?.tong_hop?.tong_so_nguoi_tham_gia || 0 }}</span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="stat-item">
                                                                            <span class="stat-label">Tổng số học phần:</span>
                                                                            <span class="stat-value">{{ hocKiData?.tong_hop?.tong_so_hoc_phan || 0 }}</span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="stat-item">
                                                                            <span class="stat-label">Tổng số câu hỏi:</span>
                                                                            <span class="stat-value">{{ hocKiData?.tong_hop?.tong_so_cau_hoi || 0 }}</span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <div class="stat-item">
                                                                            <span class="stat-label">Tổng số đề thi:</span>
                                                                            <span class="stat-value">{{ hocKiData?.tong_hop?.tong_so_de_thi || 0 }}</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Danh sách khoa -->
                                                        <div v-for="(khoa, khoaId) in hocKiData.khoa" :key="khoaId" class="accordion-item">
                                                            <!-- Khoa -->
                                                            <div class="accordion-header" @click="toggleKhoa(namHoc, hocKi, khoaId)">
                                                                <div class="accordion-button" :class="{ 'collapsed': !expandedKhoa[`${namHoc}_${hocKi}_${khoaId}`] }">
                                                                    <i class="fas" :class="expandedKhoa[`${namHoc}_${hocKi}_${khoaId}`] ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                                                                    <span class="ms-2">Khoa: {{ khoa.ten }}</span>
                                                                </div>
                                                            </div>
                                                            
                                                            <!-- Nội dung của khoa -->
                                                            <div class="accordion-collapse" :class="{ 'show': expandedKhoa[`${namHoc}_${hocKi}_${khoaId}`] }">
                                                                <div class="accordion-body">
                                                                    <!-- Tổng hợp khoa -->
                                                                    <div class="card mb-4">
                                                                        <div class="card-header bg-light">
                                                                            <h5 class="mb-0">{{ khoa.ten }}</h5>
                                                                        </div>
                                                                        <div class="card-body">
                                                                            <div class="row">
                                                                                <div class="col-md-3">
                                                                                    <div class="stat-item">
                                                                                        <span class="stat-label">Tổng số giờ:</span>
                                                                                        <span class="stat-value">{{ khoa?.tong_hop?.tong_so_gio || 0 }}</span>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-3">
                                                                                    <div class="stat-item">
                                                                                        <span class="stat-label">Tổng số giảng viên tham gia:</span>
                                                                                        <span class="stat-value">{{ khoa?.tong_hop?.tong_so_nguoi_tham_gia || 0 }}</span>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-3">
                                                                                    <div class="stat-item">
                                                                                        <span class="stat-label">Tổng số học phần:</span>
                                                                                        <span class="stat-value">{{ khoa?.tong_hop?.tong_so_hoc_phan || 0 }}</span>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-3">
                                                                                    <div class="stat-item">
                                                                                        <span class="stat-label">Tổng số câu hỏi:</span>
                                                                                        <span class="stat-value">{{ khoa?.tong_hop?.tong_so_cau_hoi || 0 }}</span>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-3">
                                                                                    <div class="stat-item">
                                                                                        <span class="stat-label">Tổng số đề thi:</span>
                                                                                        <span class="stat-value">{{ khoa?.tong_hop?.tong_so_de_thi || 0 }}</span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <!-- Danh sách bộ môn -->
                                                                    <div v-for="(bomon, bomonId) in khoa.bomon" :key="bomonId" class="accordion-item">
                                                                        <!-- Bộ môn -->
                                                                        <div class="accordion-header" @click="toggleBoMon(namHoc, hocKi, khoaId, bomonId)">
                                                                            <div class="accordion-button" :class="{ 'collapsed': !expandedBoMon[`${namHoc}_${hocKi}_${khoaId}_${bomonId}`] }">
                                                                                <i class="fas" :class="expandedBoMon[`${namHoc}_${hocKi}_${khoaId}_${bomonId}`] ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                                                                                <span class="ms-2">Bộ môn: {{ bomon.ten }}</span>
                                                                            </div>
                                                                        </div>
                                                                        
                                                                        <!-- Nội dung của bộ môn -->
                                                                        <div class="accordion-collapse" :class="{ 'show': expandedBoMon[`${namHoc}_${hocKi}_${khoaId}_${bomonId}`] }">
                                                                            <div class="accordion-body">
                                                                                <!-- Tổng hợp bộ môn -->
                                                                                <div class="card mb-4">
                                                                                    <div class="card-header bg-light">
                                                                                        <h5 class="mb-0">{{ bomon.ten }}</h5>
                                                                                    </div>
                                                                                    <div class="card-body">
                                                                                        <div class="row">
                                                                                            <div class="col-md-3">
                                                                                                <div class="stat-item">
                                                                                                    <span class="stat-label">Tổng số giờ:</span>
                                                                                                    <span class="stat-value">{{ bomon?.tong_hop?.tong_so_gio || 0 }}</span>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-md-3">
                                                                                                <div class="stat-item">
                                                                                                    <span class="stat-label">Tổng số giảng viên tham gia:</span>
                                                                                                    <span class="stat-value">{{ bomon?.tong_hop?.tong_so_nguoi_tham_gia || 0 }}</span>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-md-3">
                                                                                                <div class="stat-item">
                                                                                                    <span class="stat-label">Tổng số học phần:</span>
                                                                                                    <span class="stat-value">{{ bomon?.tong_hop?.tong_so_hoc_phan || 0 }}</span>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-md-3">
                                                                                                <div class="stat-item">
                                                                                                    <span class="stat-label">Tổng số câu hỏi:</span>
                                                                                                    <span class="stat-value">{{ bomon?.tong_hop?.tong_so_cau_hoi || 0 }}</span>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-md-3">
                                                                                                <div class="stat-item">
                                                                                                    <span class="stat-label">Tổng số đề thi:</span>
                                                                                                    <span class="stat-value">{{ bomon?.tong_hop?.tong_so_de_thi || 0 }}</span>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                
                                                                                <!-- Bảng chi tiết -->
                                                                                <div class="table-responsive">
                                                                                    <table class="table table-hover">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th>STT</th>
                                                                                                <th>Học phần</th>
                                                                                                <th>Giảng viên biên soạn</th>
                                                                                                <th>Người phản biện cấp bộ môn</th>
                                                                                                <th>Số giờ</th>
                                                                                                <th>Hình thức thi</th>
                                                                                                <th>Loại ngân hàng</th>
                                                                                                <th>Số lượng</th>
                                                                                                <th>Trạng thái</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                            <tr v-for="(chitiet, index) in bomon.chitiet" :key="index">
                                                                                                <td>{{ index + 1 }}</td>
                                                                                                <td>{{ chitiet?.hoc_phan || '' }}</td>
                                                                                                <td>{{ chitiet?.giang_vien || '' }}</td>
                                                                                                <td>{{ chitiet?.nguoi_phan_bien || '' }}</td>
                                                                                                <td>{{ chitiet?.so_gio || 0 }}</td>
                                                                                                <td>{{ chitiet?.hinh_thuc_thi || '' }}</td>
                                                                                                <td>{{ chitiet?.loai_ngan_hang || '' }}</td>
                                                                                                <td>{{ chitiet?.so_luong || 0 }}</td>
                                                                                                <td>
                                                                                                    <span :class="getStatusClass(chitiet?.trang_thai)">
                                                                                                        {{ getStatusText(chitiet?.trang_thai) }}
                                                                                                    </span>
                                                                                                </td>
                                                                                            </tr>
                                                                                        </tbody>
                                                                                    </table>
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

.stat-item {
    padding: 10px;
    border-radius: 5px;
    background-color: #f8f9fa;
    margin-bottom: 10px;
}

.stat-label {
    font-weight: 600;
    color: #6c757d;
    margin-right: 10px;
}

.stat-value {
    font-weight: 700;
    color: #212529;
}

.badge {
    font-size: 0.875rem;
    padding: 0.35em 0.65em;
}

.animated-fade-in {
    animation: fadeIn 0.5s;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
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
    transition: all 0.3s ease;
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
    transition: max-height 0.3s ease-out;
    max-height: 0;
    overflow: hidden;
}

.accordion-custom .accordion-collapse.show {
    max-height: 5000px;
}

.accordion-custom .accordion-body {
    padding: 1rem;
}
</style> 