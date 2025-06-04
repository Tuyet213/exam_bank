<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import { router } from '@inertiajs/vue3';

const {
    danhsachs_hierarchy,
    message,
    success,
    ds_hoc_ki,
    ds_nam_hoc,
    ds_bo_mon,
    ds_khoa,
    bo_mon_theo_khoa,
    filters
} = defineProps({
    danhsachs_hierarchy: {
        type: Object,
        required: true,
        default: () => ({}),
    },
    message: {
        type: String,
        default: "",
    },
    success: {
        type: Boolean,
        default: undefined,
    },
    ds_hoc_ki: {
        type: Array,
        required: true
    },
    ds_nam_hoc: {
        type: Array,
        required: true
    },
    ds_bo_mon: {
        type: Array,
        required: true
    },
    ds_khoa: {
        type: Array,
        required: true
    },
    bo_mon_theo_khoa: {
        type: Object,
        required: true
    },
    filters: {
        type: Object,
        required: true
    }
});

const searchTerm = ref(filters.search || "");
const hocKi = ref(filters.hoc_ki || "");
const namHoc = ref(filters.nam_hoc || "");
const boMon = ref(filters.bo_mon || "");
const khoa = ref(filters.khoa || "");
const debounceTimeout = ref(null);

// State cho collapse/expand của các phần
const expandedNamHoc = ref({});
const expandedHocKi = ref({});
const expandedKhoa = ref({});
const expandedBoMon = ref({});

// Lọc bộ môn theo khoa đã chọn
const boMonTheoKhoa = computed(() => {
    if (!khoa.value) {
        return ds_bo_mon;
    }
    return bo_mon_theo_khoa[khoa.value] || [];
});

// Reset bộ môn khi thay đổi khoa
watch(khoa, (newValue) => {
    boMon.value = '';
});

const performSearch = () => {
    if (debounceTimeout.value) {
        clearTimeout(debounceTimeout.value);
    }
    
    debounceTimeout.value = setTimeout(() => {
        router.get(
            route('quality.dsbienban.index'),
            { 
                search: searchTerm.value,
                hoc_ki: hocKi.value,
                nam_hoc: namHoc.value,
                bo_mon: boMon.value,
                khoa: khoa.value
            },
            { 
                preserveState: true,
                replace: true 
            }
        );
    }, 300);
};

watch([searchTerm, hocKi, namHoc, boMon, khoa], () => {
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
const toggleKhoa = (namHoc, hocKi, khoa) => {
    const key = `${namHoc}_${hocKi}_${khoa}`;
    expandedKhoa.value[key] = !expandedKhoa.value[key];
};

// Toggle expand/collapse cho bộ môn
const toggleBoMon = (namHoc, hocKi, khoa, boMon) => {
    const key = `${namHoc}_${hocKi}_${khoa}_${boMon}`;
    expandedBoMon.value[key] = !expandedBoMon.value[key];
};

const getStatusBadgeClass = (status) => {
    const classes = 'badge ';
    switch (status) {
        case 'Đã duyệt':
            return classes + 'bg-success';
        case 'Chờ duyệt':
            return classes + 'bg-warning';
        case 'Chưa có file':
            return classes + 'bg-secondary';
        default:
            return classes + 'bg-secondary';
    }
};
</script>


<template>
    <AppLayout role="dbcl">
        <template v-slot:sub-link>
            <li class="breadcrumb-item active">
                <a :href="route('quality.dsbienban.index')">Danh sách biên bản nghiệm thu</a>
            </li>
        </template>

        <template v-slot:content>
            <div class="content">
                <div class="card border-radius-lg shadow-lg animated-fade-in">
                    <div class="card-header d-flex justify-content-between align-items-center bg-info-qo text-white p-4">
                        <h3 class="mb-0">DANH SÁCH BIÊN BẢN NGHIỆM THU</h3>
                    </div>

                    <div class="card-body pb-0">
                        <!-- Form tìm kiếm -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="nam_hoc" class="form-label">Năm học</label>
                                <select 
                                    id="nam_hoc" 
                                    class="form-select" 
                                    v-model="namHoc"
                                >
                                    <option value="">Tất cả năm học</option>
                                    <option v-for="nam in ds_nam_hoc" :key="nam" :value="nam">
                                        {{ nam }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="hoc_ki" class="form-label">Học kỳ</label>
                                <select 
                                    id="hoc_ki" 
                                    class="form-select" 
                                    v-model="hocKi"
                                >
                                    <option value="">Tất cả học kỳ</option>
                                    <option v-for="hk in ds_hoc_ki" :key="hk" :value="hk">
                                        Học kỳ {{ hk }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6 mt-3">
                                <label for="khoa" class="form-label">Khoa</label>
                                <select 
                                    id="khoa" 
                                    class="form-select" 
                                    v-model="khoa"
                                >
                                    <option value="">Tất cả khoa</option>
                                    <option v-for="k in ds_khoa" :key="k" :value="k">
                                        {{ k }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6 mt-3">
                                <label for="bo_mon" class="form-label">Bộ môn</label>
                                <select 
                                    id="bo_mon" 
                                    class="form-select" 
                                    v-model="boMon"
                                    :disabled="!khoa"
                                >
                                    <option value="">{{ khoa ? 'Tất cả bộ môn' : 'Vui lòng chọn khoa trước' }}</option>
                                    <option v-for="bm in boMonTheoKhoa" :key="bm" :value="bm">
                                        {{ bm }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="thongke-content">
                            <!-- Danh sách trống -->
                            <div v-if="Object.keys(danhsachs_hierarchy).length === 0" class="text-center py-5">
                                <h5 class="text-muted">Không có biên bản nào</h5>
                                <p>Vui lòng chọn các tiêu chí lọc khác</p>
                            </div>
                            
                            <!-- Danh sách phân cấp: năm học -> học kỳ -> khoa -> bộ môn -> danh sách -->
                            <div v-else class="accordion accordion-custom">
                                <div v-for="(namData, namHoc) in danhsachs_hierarchy" :key="namHoc" class="accordion-item">
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
                                            <!-- Danh sách học kỳ -->
                                            <div v-for="(hocKiData, hocKi) in namData.hoc_ki" :key="`${namHoc}_${hocKi}`" class="accordion-item ms-4 mt-2">
                                                <!-- Học kỳ -->
                                                <div class="accordion-header" @click="toggleHocKi(namHoc, hocKi)">
                                                    <div class="accordion-button" :class="{ 'collapsed': !expandedHocKi[`${namHoc}_${hocKi}`] }">
                                                        <i class="fas" :class="expandedHocKi[`${namHoc}_${hocKi}`] ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                                                        <span class="ms-2">{{ hocKiData.ten }}</span>
                                                    </div>
                                                </div>
                                                
                                                <!-- Nội dung của học kỳ -->
                                                <div class="accordion-collapse" :class="{ 'show': expandedHocKi[`${namHoc}_${hocKi}`] }">
                                                    <div class="accordion-body">
                                                        <!-- Danh sách khoa -->
                                                        <div v-for="(khoaData, khoa) in hocKiData.khoa" :key="`${namHoc}_${hocKi}_${khoa}`" class="accordion-item ms-4 mt-2">
                                                            <!-- Khoa -->
                                                            <div class="accordion-header" @click="toggleKhoa(namHoc, hocKi, khoa)">
                                                                <div class="accordion-button" :class="{ 'collapsed': !expandedKhoa[`${namHoc}_${hocKi}_${khoa}`] }">
                                                                    <i class="fas" :class="expandedKhoa[`${namHoc}_${hocKi}_${khoa}`] ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                                                                    <span class="ms-2">{{ khoaData.ten }}</span>
                                                                </div>
                                                            </div>
                                                            
                                                            <!-- Nội dung của khoa -->
                                                            <div class="accordion-collapse" :class="{ 'show': expandedKhoa[`${namHoc}_${hocKi}_${khoa}`] }">
                                                                <div class="accordion-body">
                                                                    <!-- Danh sách bộ môn -->
                                                                    <div v-for="(boMonData, boMon) in khoaData.bo_mon" :key="`${namHoc}_${hocKi}_${khoa}_${boMon}`" class="accordion-item ms-4 mt-2">
                                                                        <!-- Bộ môn -->
                                                                        <div class="accordion-header" @click="toggleBoMon(namHoc, hocKi, khoa, boMon)">
                                                                            <div class="accordion-button" :class="{ 'collapsed': !expandedBoMon[`${namHoc}_${hocKi}_${khoa}_${boMon}`] }">
                                                                                <i class="fas" :class="expandedBoMon[`${namHoc}_${hocKi}_${khoa}_${boMon}`] ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                                                                                <span class="ms-2">{{ boMonData.ten }}</span>
                                                                            </div>
                                                                        </div>
                                                                        
                                                                        <!-- Nội dung của bộ môn -->
                                                                        <div class="accordion-collapse" :class="{ 'show': expandedBoMon[`${namHoc}_${hocKi}_${khoa}_${boMon}`] }">
                                                                            <div class="accordion-body">
                                                                                <div class="table-responsive">
                                                                                    <table class="table table-hover">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th>STT</th>
                                                                                                <th>Học phần</th>
                                                                                                <th>Người biên soạn</th>
                                                                                                <th>Nội dung</th>
                                                                                                <th>Ngày họp</th>
                                                                                                <th>Trạng thái</th>
                                                                                                <th>Thao tác</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                            <tr v-for="(bb, index) in boMonData.danh_sach" :key="bb.id">
                                                                                                <td>{{ index + 1 }}</td>
                                                                                                <td>{{ bb.ct_d_s_dang_ky?.hoc_phan?.ten || 'Chưa có thông tin' }}</td>
                                                                                                <td>
                                                                                                    {{ (bb.ct_d_s_dang_ky?.ds_g_v_bien_soans || []).map(gv => gv?.vien_chuc?.name || 'Không có tên').join(', ') || 'Chưa có giảng viên' }}
                                                                                                </td>
                                                                                                <td>
                                                                                                    <a v-if="bb.noi_dung" 
                                                                                                       :href="route('quality.dsbienban.download', bb.id)" 
                                                                                                       class="btn btn-primary " 
                                                                                                       style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;"
                                                                                                       title="Tải xuống nội dung">
                                                                                                       <i class="fas fa-download"></i>
                                                                                                    </a>
                                                                                                    <span v-else>Chưa có nội dung</span>
                                                                                                </td>
                                                                                                <td>{{ bb.thoi_gian || 'Chưa có thông tin' }}</td>
                                                                                                <td>
                                                                                                    <span 
                                                                                                        class="badge"
                                                                                                        :class="{
                                                                                                            'bg-secondary': bb.trang_thai === 'Draft', 
                                                                                                            'bg-warning': bb.trang_thai === 'Pending',
                                                                                                            'bg-success': bb.trang_thai === 'Approved',
                                                                                                            'bg-danger': bb.trang_thai === 'Rejected'
                                                                                                        }"
                                                                                                    >
                                                                                                        {{ bb.trang_thai || 'Draft' }}
                                                                                                    </span>
                                                                                                </td>
                                                                                                
                                                                                                <td>
                                                                                                    <a 
                                                                                                        :href="route('quality.dsbienban.show', bb.id)"
                                                                                                        class="btn btn-sm btn-primary me-2"
                                                                                                        title="Xem chi tiết"
                                                                                                    >
                                                                                                        <i class="fas fa-eye"></i>
                                                                                                    </a>
                                                                                                    
                                                                                                  
                                                                                                </td>
                                                                                            </tr>
                                                                                            <tr v-if="boMonData.danh_sach.length === 0">
                                                                                                <td colspan="7" class="text-center">
                                                                                                    Không có dữ liệu
                                                                                                </td>
                                                                                            </tr>
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <div v-if="Object.keys(khoaData.bo_mon).length === 0" class="text-center py-3">
                                                                        <p class="text-muted">Không có bộ môn nào</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div v-if="Object.keys(hocKiData.khoa).length === 0" class="text-center py-3">
                                                            <p class="text-muted">Không có khoa nào</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div v-if="Object.keys(namData.hoc_ki).length === 0" class="text-center py-3">
                                                <p class="text-muted">Không có học kỳ nào</p>
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

.bg-info-qo {
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
</style> 