<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/vue3";
import { router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const { danhsachs_hierarchy, message, success, bo_mon, ds_hoc_ki, ds_nam_hoc, filters } = defineProps({
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
    bo_mon: {
        type: String,
        default: "",
    },
    ds_hoc_ki: {
        type: Array,
        required: true
    },
    ds_nam_hoc: {
        type: Array,
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
const debounceTimeout = ref(null);

// State cho collapse/expand của các phần
const expandedNamHoc = ref({});
const expandedHocKi = ref({});

const performSearch = () => {
    if (debounceTimeout.value) {
        clearTimeout(debounceTimeout.value);
    }
    debounceTimeout.value = setTimeout(() => {
        router.get(
            route('tbm.dsdangky.index'),
            { 
                search: searchTerm.value,
                hoc_ki: hocKi.value,
                nam_hoc: namHoc.value
            },
            { 
                preserveState: true,
                replace: true 
            }
        );
    }, 300);
};

watch([searchTerm, hocKi, namHoc], () => {
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

const handleSend = (id) => {
    if (confirm('Bạn có chắc chắn muốn gửi danh sách này?')) {
        router.post(route('tbm.dsdangky.send', id), {}, {
            onSuccess: () => {
                alert('Gửi danh sách đăng ký thành công!');
            },
            onError: (errors) => {
                alert('Có lỗi xảy ra khi gửi danh sách đăng ký!');
                console.error(errors);
            }
        });
    }
};

const handleEdit = (id) => {
    router.get(route('tbm.dsdangky.edit', id));
};

const getStatusBadgeClass = (status) => {
    const classes = 'badge ';
    switch (status) {
        case 'Completed':
            return classes + 'bg-success-tb';
        case 'Approved':
            return classes + 'bg-success';
        case 'Rejected':
            return classes + 'bg-danger';
        case 'Pending':
            return classes + 'bg-warning';
        case 'Sent':
            return classes + 'bg-info';
        case 'Draft':
            return classes + 'bg-secondary';
        default:
            return classes + 'bg-secondary';
    }
};
</script>

<template>
    <AppLayout role="tbm">
        <template v-slot:sub-link>
            <li class="breadcrumb-item active">
                <a :href="route('tbm.dsdangky.index')">Danh sách đăng ký</a>
            </li>
        </template>

        <template v-slot:content>
            <div class="content">
                <div class="card border-radius-lg shadow-lg animated-fade-in">
                    <div class="card-header d-flex justify-content-between align-items-center bg-success-tb text-white p-4">
                        <h3 class="mb-0">DANH SÁCH ĐĂNG KÝ (Bộ Môn {{ bo_mon }})</h3>
                        <div class="d-flex gap-2">
                            <Link 
                                :href="route('tbm.dsdangky.create')"
                                class="btn btn-light"
                            >
                                <i class="fas fa-plus"></i> Tạo danh sách đăng ký
                            </Link>
                        </div>
                    </div>

                    <div class="card-body pb-0">
                        <!-- Form tìm kiếm -->
                        <div class="row mb-4">
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
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="thongke-content">
                            <!-- Danh sách trống -->
                            <div v-if="Object.keys(danhsachs_hierarchy).length === 0" class="text-center py-5">
                                <h5 class="text-muted">Không có dữ liệu đăng ký</h5>
                                <p>Vui lòng tạo danh sách đăng ký mới hoặc chọn các tiêu chí lọc khác</p>
                            </div>
                            
                            <!-- Danh sách phân cấp: năm học -> học kỳ -> danh sách -->
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
                                                        <div class="table-responsive">
                                                            <table class="table table-hover">
                                                                <thead>
                                                                    <tr>
                                                                        <th>STT</th>
                                                                        <th>Trạng thái</th>
                                                                        <th>Thao tác</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr v-for="(ds, index) in hocKiData.danh_sach" :key="ds.id">
                                                                        <td>{{ index + 1 }}</td>
                                                                        <td>
                                                                            <span :class="getStatusBadgeClass(ds.trang_thai)" class="badge">
                                                                                {{ ds.trang_thai || 'Draft' }}
                                                                            </span>
                                                                        </td>
                                                                        <td>
                                                                            <Link 
                                                                                :href="route('tbm.ctdsdangky.index', ds.id)"
                                                                                class="btn btn-sm btn-info me-2"
                                                                                title="Xem chi tiết"
                                                                            >
                                                                                <i class="fas fa-eye"></i>
                                                                            </Link>
                                                                            <button
                                                                                :disabled="!ds.can_send"
                                                                                @click="handleSend(ds.id)"
                                                                                class="btn btn-sm btn-primary me-2"
                                                                                title="Gửi danh sách"
                                                                            >
                                                                                <i class="fas fa-paper-plane"> </i>
                                                                            </button>
                                                                            <button
                                                                                :disabled="!ds.can_send"
                                                                                class="btn btn-sm btn-warning me-2"
                                                                                title="Sửa"
                                                                                @click="handleEdit(ds.id)"
                                                                            >
                                                                                <i class="fas fa-edit"></i>
                                                                            </button>
                                                                        </td>
                                                                    </tr>
                                                                    <tr v-if="hocKiData.danh_sach.length === 0">
                                                                        <td colspan="3" class="text-center">
                                                                            Không có dữ liệu
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
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

.form-select:focus,
.form-control:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.form-label {
    margin-bottom: 0.5rem;
    font-weight: 500;
}
</style>