<script setup>
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, watch, computed } from 'vue';

const props = defineProps({
    ctDangKy: {
        type: Object,
        required: true
    },
    deThis: {
        type: Array,
        required: true
    },
    role: {
        type: String,
        required: true
    },
    filters: Object
});

// Thêm các biến reactive cho bộ lọc theo ngày
const tuNgay = ref(props.filters.tu_ngay || '');
const denNgay = ref(props.filters.den_ngay || '');

// Validation cho ngày
const validateDates = () => {
    if (tuNgay.value && denNgay.value) {
        const fromDate = new Date(tuNgay.value);
        const toDate = new Date(denNgay.value);
        
        if (fromDate > toDate) {
            alert('Từ ngày phải nhỏ hơn hoặc bằng đến ngày!');
            return false;
        }
    }
    return true;
};

// Hàm xóa đề thi
const xoaDeThi = (id) => {
    if (confirm('Bạn có chắc chắn muốn xóa đề thi này?\n\nThao tác này không thể hoàn tác!')) {
        router.delete(route('dethi.xoa', id), {
            onSuccess: () => {
                // Thông báo thành công sẽ được hiển thị từ server response
            },
            onError: (errors) => {
                alert('Có lỗi xảy ra khi xóa đề thi: ' + (errors.message || 'Lỗi không xác định'));
            }
        });
    }
};

// Hàm download file
const downloadFile = (deThiId, type) => {
    if (type === 'de') {
        window.open(route('dethi.download.de', deThiId));
    } else {
        window.open(route('dethi.download.dapan', deThiId));
    }
};

// Hàm format tên file
const getFileName = (filePath) => {
    if (!filePath) return '';
    return filePath.split('/').pop();
};

watch([tuNgay, denNgay], () => {
    if (validateDates()) {
        router.get(
            route('dethi.danhsach', props.ctDangKy.id),
            { 
                tu_ngay: tuNgay.value,
                den_ngay: denNgay.value
            },
            { preserveState: true, replace: true }
        );
    }
});
</script>

<template>
    <AppLayout :role="role">
        <template #sub-link>
            <li class="breadcrumb-item">
                <a :href="route('dethi.hocphan')">Danh sách học phần</a>
            </li>
            <li class="breadcrumb-item active">
                Danh sách đề thi
            </li>
        </template>
        
        <template #content>
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                           <div class="row justify-content-between">
                             <!-- Thông tin học phần -->
                             <div class="mb-4 col-md-6">
                                <h2 class="text-xl font-semibold">{{ ctDangKy.hoc_phan.ten }}</h2>
                                <p class="text-gray-600">Mã học phần: {{ ctDangKy.hoc_phan.id }}</p>
                            </div>
                              <!-- Nút chức năng -->
                              <div class="mb-6 flex space-x-4 offset-md-10 col-md-2">
                                <Link :href="route('dethi.tao', ctDangKy.id)"
                                    class="btn btn-success">
                                    <i class="fas fa-plus-circle mr-2"></i> Tạo đề thi mới
                                </Link>
                            </div>

                           </div>


                            <!-- Bộ lọc theo ngày tạo -->
                            <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="form-group">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Từ ngày:</label>
                                    <input 
                                        type="date" 
                                        v-model="tuNgay"
                                        :max="denNgay || undefined"
                                        class="form-control"
                                        placeholder="Từ ngày..."
                                    >
                                </div>
                                <div class="form-group">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Đến ngày:</label>
                                    <input 
                                        type="date" 
                                        v-model="denNgay"
                                        :min="tuNgay || undefined"
                                        class="form-control"
                                        placeholder="Đến ngày..."
                                    >
                                </div>
                            </div>

                          
                            <!-- Danh sách đề thi dạng bảng -->
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th scope="col" width="5%">STT</th>
                                            <th scope="col" width="15%">Loại đề thi</th>
                                            <th scope="col" width="25%">File đề thi</th>
                                            <th scope="col" width="25%">File đáp án</th>
                                            <th scope="col" width="15%">Ngày tạo</th>
                                            <th scope="col" width="15%">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(deThi, index) in deThis" :key="deThi.id">
                                            <td class="text-center">{{ index + 1 }}</td>
                                            <td>
                                                {{ deThi.loai == 0 ? 'Trắc nghiệm' : 'Tự luận/Vấn đáp' }}
                                            </td>
                                            <td>
                                                <div v-if="deThi.de" class="d-flex align-items-center">
                                                    <i class="fas fa-file-pdf text-red-500 mr-2"></i>
                                                    <span class="text-sm">{{ getFileName(deThi.de) }}</span>
                                                    <button @click="downloadFile(deThi.id, 'de')" 
                                                            class="btn btn-sm btn-outline-primary ml-2"
                                                            title="Tải về đề thi">
                                                        <i class="fas fa-download"></i>
                                                    </button>
                                                </div>
                                                <span v-else class="text-gray-500">
                                                    <i class="fas fa-file-times mr-1"></i>Chưa có file
                                                </span>
                                            </td>
                                            <td>
                                                <div v-if="deThi.dap_an" class="d-flex align-items-center">
                                                    <i class="fas fa-file-alt text-green-500 mr-2"></i>
                                                    <span class="text-sm">{{ getFileName(deThi.dap_an) }}</span>
                                                    <button @click="downloadFile(deThi.id, 'dap_an')" 
                                                            class="btn btn-sm btn-outline-primary ml-2"
                                                            title="Tải về đáp án">
                                                        <i class="fas fa-download"></i>
                                                    </button>
                                                </div>
                                                <span v-else class="text-gray-500">
                                                    <i class="fas fa-file-times mr-1"></i>Chưa có file
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-sm text-gray-600">
                                                    {{ new Date(deThi.created_at).toLocaleDateString('vi-VN') }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <button @click="xoaDeThi(deThi.id)" 
                                                        class="btn btn-danger btn-sm"
                                                        title="Xóa đề thi">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr v-if="deThis.length === 0">
                                            <td colspan="6" class="text-center">Không tìm thấy đề thi nào</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </AppLayout>
</template>

<style scoped>
/* Màu chủ đạo - xanh lá */
.bg-success-custom {
    background: linear-gradient(135deg, rgb(94, 181, 98), rgb(76, 150, 80)) !important;
}

.bg-success-light {
    background-color: rgba(94, 181, 98, 0.1);
    color: rgb(94, 181, 98);
}

.table-success-custom {
    background: linear-gradient(135deg, rgba(94, 181, 98, 0.1), rgba(76, 150, 80, 0.1)) !important;
}

.table-success-custom th {
    color: rgb(76, 150, 80);
    font-weight: 600;
    border-bottom: 2px solid rgb(94, 181, 98);
}

.badge-success-custom {
    background: linear-gradient(135deg, rgb(94, 181, 98), rgb(76, 150, 80));
    color: white;
}

.badge-secondary-custom {
    background: linear-gradient(135deg, #6c757d, #495057);
    color: white;
}

.btn-outline-success-custom {
    color: rgb(94, 181, 98);
    border-color: rgb(94, 181, 98);
}

.btn-outline-success-custom:hover {
    background-color: rgb(94, 181, 98);
    border-color: rgb(94, 181, 98);
    color: white;
}

.info-box {
    background: linear-gradient(135deg, #f8fff8 0%, #e8fde8 100%);
    border-radius: 12px;
    border: 1px solid rgba(94, 181, 98, 0.2);
    padding: 20px;
    margin-bottom: 20px;
}

.info-box h6 {
    color: rgb(76, 150, 80);
    font-weight: 600;
    margin-bottom: 15px;
    border-bottom: 1px solid rgba(94, 181, 98, 0.2);
    padding-bottom: 8px;
}

.info-box p {
    margin-bottom: 8px;
    color: #424242;
}

.info-box i {
    color: rgb(94, 181, 98);
}

.file-name-table {
    font-size: 0.9em;
    color: #495057;
    max-width: 150px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.empty-state {
    max-width: 400px;
    margin: 0 auto;
}

.table {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.table tbody tr:hover {
    background-color: rgba(94, 181, 98, 0.05);
}
</style>
