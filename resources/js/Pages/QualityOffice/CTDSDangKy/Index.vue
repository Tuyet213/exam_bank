<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
    ctdsdangky: {
        type: Array,
        default: () => []
    },
    dsdangky: {
        type: Object,
        required: true
    }
});

// Debug để xem dữ liệu
console.log('CTDSDangKy Data:', props.ctdsdangky);
console.log('DSDangKy Info:', props.dsdangky);

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

const approve = (id) => {
    router.put(route('quality.ctdsdangky.updateStatus', id), {
        trang_thai: 'Approved',
        dsdangky_id: props.dsdangky.id
    });
};

const reject = (id) => {
    router.put(route('quality.ctdsdangky.updateStatus', id), {
        trang_thai: 'Rejected',
        dsdangky_id: props.dsdangky.id
    });
};

const approveAll = () => {
    if (confirm('Bạn có chắc chắn muốn duyệt tất cả các mục trong danh sách này?')) {
        router.put(route('quality.ctdsdangky.updateStatusAll', props.dsdangky.id), {
            trang_thai: 'Approved'
        });
    }
};

const rejectAll = () => {
    if (confirm('Bạn có chắc chắn muốn từ chối tất cả các mục trong danh sách này?')) {
        router.put(route('quality.ctdsdangky.updateStatusAll', props.dsdangky.id), {
            trang_thai: 'Rejected'
        });
    }
};

const handleSubmit = () => {
    if (confirm('Bạn có chắc chắn muốn hoàn tất danh sách này?')) {
        router.post(route('quality.ctdsdangky.submit', props.dsdangky.id));
    }
};

</script>

<template>
    <AppLayout role="dbcl">
        <template #sub-link>
            <li class="breadcrumb-item">
                <a :href="route('quality.dsdangky.index')">Danh sách đăng ký</a>
            </li>
            <li class="breadcrumb-item active">
                Chi tiết danh sách: {{ dsdangky.ten }}
            </li>
        </template>

        <template #content>
            <div class="content">
                <div class="card border-radius-lg shadow-lg animated-fade-in">
                    <!-- Card Header -->
                    <div class="card-header bg-success-tb text-white p-4">
                        <div class="row justify-content-between align-items-center">
                            <div class="col-md-8 col-sm-12">

                                <h3 class="mb-0 font-weight-bolder">
                                    CHI TIẾT DANH SÁCH ĐĂNG KÝ
                                </h3>
                                <div class="mt-3">
                            <p class="mb-1"><strong>Bộ môn:</strong> {{ dsdangky.bo_mon?.ten }}</p>
                            <p class="mb-1"><strong>Thời gian:</strong> {{ formatDate(dsdangky.thoi_gian) }}</p>
                            
                        </div>
                            </div>
                            <div class="col-md-4 col-sm-12 row gap-2">
                                <button class="btn btn-light me-2 col-12 col-sm-12" @click="approveAll" >
                                    <i class="fas fa-check-circle me-1"></i>
                                    Duyệt tất cả
                                </button>
                                <button class="btn btn-danger me-2 col-12 col-sm-12" @click="rejectAll">
                                    <i class="fas fa-times-circle me-1"></i>
                                    Từ chối tất cả
                                </button>
                                <!-- :disabled="ctdsdangky.some(ct => ct.trang_thai === 'Draft' || ct.trang_thai === 'Rejected')" -->
                                <button 
                                    class="btn btn-primary col-12 " 
                                    @click="handleSubmit"
                                    
                                    title="Hoàn tất và gửi danh sách"
                                >
                                    <i class="fas fa-paper-plane me-1"></i>
                                    Hoàn tất
                                </button>
                            </div>
                        </div>
                       
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Học phần</th>
                                        <th>Giảng viên biên soạn</th>
                                        <th>Số giờ</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(ct, index) in ctdsdangky" :key="ct.id">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ ct.hoc_phan?.ten }}</td>
                                        <td>
                                            {{ (ct.ds_g_v_bien_soans || []).map(gv => gv?.vien_chuc?.name || 'Không có tên').join(', ') || 'Chưa có giảng viên' }}
                                        </td>
                                        <td>{{ ct.so_gio }}</td>
                                        <td>
                                            <span :class="getStatusBadgeClass(ct.trang_thai)" class="badge">
                                                {{ ct.trang_thai }}
                                            </span>
                                        </td>
                                        <td>
                                            <button 
                                                class="btn btn-sm btn-success me-1" 
                                                @click="approve(ct.id)"
                                                
                                                :disabled="ct.trang_thai !== 'Rejected' && ct.trang_thai !== 'Draft' && ct.trang_thai !== 'Pending'"
                                                title="Duyệt"
                                            >
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button 
                                                class="btn btn-sm btn-danger" 
                                                @click="reject(ct.id)"
                                                :disabled="ct.trang_thai !== 'Rejected' && ct.trang_thai !== 'Draft' && ct.trang_thai !== 'Pending'"
                                                title="Từ chối"
                                            >
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="!ctdsdangky || ctdsdangky.length === 0">
                                        <td colspan="6" class="text-center">Không có dữ liệu</td>
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

.btn-light {
    background-color: #f8f9fa;
    border-color: #f8f9fa;
}

.btn-light:hover {
    background-color: #e2e6ea;
    border-color: #dae0e5;
}

.me-1 {
    margin-right: 0.25rem !important;
}

.me-2 {
    margin-right: 0.5rem !important;
}

.ms-2 {
    margin-left: 0.5rem !important;
}

.mt-3 {
    margin-top: 1rem !important;
}

.mb-1 {
    margin-bottom: 0.25rem !important;
}

.mb-0 {
    margin-bottom: 0 !important;
}

.btn:disabled {
    opacity: 0.65;
    cursor: not-allowed;
}
</style>
