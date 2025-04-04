<script setup>
import QualityLayout from '@/Layouts/QualityLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    dsdangky: {
        type: Object,
        default: () => ({
            data: []
        })
    }
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
    <QualityLayout>
        <template #sub-link>
            <li class="breadcrumb-item active">
                <a :href="route('quality.dsdangky.index')">Danh sách đăng ký</a>
            </li>
        </template>

        <template #content>
            <div class="content">
                <div class="card border-radius-lg shadow-lg animated-fade-in">
                    <!-- Card Header -->
                    <div class="card-header bg-success-tb text-white p-4 d-flex justify-content-between">
                        <h3 class="mb-0 font-weight-bolder">
                            DANH SÁCH ĐĂNG KÝ
                        </h3>
                       
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Tên danh sách</th>
                                        <th>Bộ môn</th>
                                        <th>Thời gian</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(ds, index) in dsdangky" :key="ds.id">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ ds.ten }}</td>
                                        <td>{{ ds.bo_mon?.ten }}</td>
                                        <td>{{ formatDate(ds.thoi_gian) }}</td>
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
                                        <td colspan="6" class="text-center">Không có dữ liệu</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
            
        </template>
    </QualityLayout>
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
</style>
