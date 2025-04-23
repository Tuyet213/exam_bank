<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link, useForm, router } from "@inertiajs/vue3";
import { ref, watch } from 'vue';

const props = defineProps({
    ds_bien_ban: {
        type: Object,
        required: true
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

console.log(props.ds_bien_ban);

const form = useForm({
    hoc_ki: props.filters.hoc_ki || '',
    nam_hoc: props.filters.nam_hoc || ''
});

const handleSearch = () => {
    form.get(route('tbm.dsbienban.index'), {
        preserveState: true,
        preserveScroll: true
    });
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('vi-VN');
};

const uploadNoiDung = (bienBan) => {
    // Tạo input file ẩn
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'application/pdf';
    
    input.onchange = (e) => {
        const file = e.target.files[0];
        if (file) {
            const formData = new FormData();
            formData.append('noi_dung', file);
            
            // Gửi file lên server
            router.post(route('tbm.dsbienban.upload-noi-dung', bienBan.id), formData, {
                preserveScroll: true,
                onSuccess: () => {
                    alert('Upload nội dung thành công!');
                },
                onError: () => {
                    alert('Có lỗi xảy ra khi upload file!');
                }
            });
        }
    };
    
    input.click();
};
</script>

<template>
    <AppLayout role="tbm">
        <template v-slot:sub-link>
            <li class="breadcrumb-item active">
                <a :href="route('tbm.dsbienban.index')">Danh sách biên bản họp</a>
            </li>
        </template>

        <template v-slot:content>
            <div class="content">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">DANH SÁCH BIÊN BẢN HỌP BỘ MÔN</h3>
                        
                    </div>

                    <div class="card-body">
                        <!-- Form tìm kiếm -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="hoc_ki" class="form-label">Học kỳ</label>
                                <select 
                                    id="hoc_ki" 
                                    class="form-select" 
                                    v-model="form.hoc_ki"
                                >
                                    <option value="">Tất cả học kỳ</option>
                                    <option v-for="hk in ds_hoc_ki" :key="hk" :value="hk">
                                        Học kỳ {{ hk }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="nam_hoc" class="form-label">Năm học</label>
                                <select 
                                    id="nam_hoc" 
                                    class="form-select" 
                                    v-model="form.nam_hoc"
                                >
                                    <option value="">Tất cả năm học</option>
                                    <option v-for="nam in ds_nam_hoc" :key="nam" :value="nam">
                                        {{ nam }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button 
                                    class="btn btn-primary w-100" 
                                    @click="handleSearch"
                                >
                                    <i class="fas fa-search"></i> Tìm kiếm
                                </button>
                            </div>
                        </div>

                        <!-- Bảng danh sách -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Học phần</th>
                                        <th>Giảng viên</th>
                                        <th>Thời gian</th>
                                        <th>Địa điểm</th>
                                        <th>Học kỳ</th>
                                        <th>Năm học</th>
                                        <th>File biên bản</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="ds_bien_ban.length === 0">
                                        <td colspan="10" class="text-center">
                                            Không có dữ liệu
                                        </td>
                                    </tr>
                                    <tr v-for="(bb, index) in ds_bien_ban" :key="bb.id">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ bb.ct_d_s_dang_ky?.hoc_phan?.ten }}</td>
                                        <td>{{ bb.ct_d_s_dang_ky?.vien_chuc?.name }}</td>
                                        <td>{{ bb.thoi_gian }}</td>
                                        <td>{{ bb.dia_diem }}</td>
                                        <td>{{ bb.ct_d_s_dang_ky?.ds_dang_ky?.hoc_ki }}</td>
                                        <td>{{ bb.ct_d_s_dang_ky?.ds_dang_ky?.nam_hoc }}</td>
                                        <td class="text-center">
                                            <a 
                                                v-if="bb.noi_dung"
                                                :href="route('tbm.dsbienban.download', bb.id)"
                                                class="btn btn-sm btn-primary"
                                                title="Tải xuống"
                                            >
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <span v-else class="text-muted">
                                                Chưa có
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <Link 
                                                    :href="route('tbm.dsbienban.edit', bb.id)"
                                                    class="btn btn-sm btn-warning me-2"
                                                    title="Chỉnh sửa"
                                                >
                                                    <i class="fas fa-edit"></i>
                                                </Link>
                                                <Link 
                                                    :href="route('tbm.dsbienban.edit-so-gio', bb.id)"
                                                    class="btn btn-sm btn-secondary me-2"
                                                    title="Chỉnh sửa số giờ"
                                                >
                                                    <i class="fas fa-clock"></i>
                                                </Link>
                                                <button 
                                                    class="btn btn-sm btn-info me-2"
                                                    title="Thêm nội dung"
                                                    @click="uploadNoiDung(bb)"
                                                >
                                                    <i class="fas fa-file-pdf"></i>
                                                </button>
                                            </div>
                                        </td>
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
.btn-success-add {
    background-color: #28a745;
    color: white;
}

.btn-success-add:hover {
    background-color: #218838;
    color: white;
}

.table th {
    background-color: #f8f9fa;
    color: #495057;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.form-control:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

.btn-info {
    background-color: #17a2b8;
    color: white;
}

.btn-info:hover {
    background-color: #138496;
    color: white;
}

.btn-warning {
    background-color: #ffc107;
    color: #212529;
}

.btn-warning:hover {
    background-color: #e0a800;
    color: #212529;
}

.btn-danger {
    background-color: #dc3545;
    color: white;
}

.btn-danger:hover {
    background-color: #c82333;
    color: white;
}

.me-2 {
    margin-right: 0.5rem !important;
}
</style> 