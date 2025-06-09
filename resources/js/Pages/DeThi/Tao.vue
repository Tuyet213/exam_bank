<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    ctDangKy: {
        type: Object,
        required: true
    },
    role: {
        type: String,
        required: true
    }
});

const form = useForm({
    id_ct_ds_dang_ky: props.ctDangKy.id,
    loai: props.ctDangKy.hinh_thuc_thi, // Tự động lấy từ hình thức thi
    file_de: null,
    file_dap_an: null
});

// Ref cho file inputs
const fileDeInput = ref(null);
const fileDapAnInput = ref(null);

// Computed properties để hiển thị tên file
const deFileName = computed(() => {
    return form.file_de ? form.file_de.name : '';
});

const dapAnFileName = computed(() => {
    return form.file_dap_an ? form.file_dap_an.name : '';
});

// Hàm xử lý chọn file đề thi
const handleFileDeChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        // Kiểm tra kích thước file (max 10MB)
        if (file.size > 10 * 1024 * 1024) {
            alert('File đề thi không được vượt quá 10MB');
            event.target.value = '';
            return;
        }
        
        // Kiểm tra định dạng file bằng extension (đáng tin cậy hơn)
        const fileName = file.name.toLowerCase();
        const allowedExtensions = ['.pdf', '.doc', '.docx'];
        const isValidExtension = allowedExtensions.some(ext => fileName.endsWith(ext));
        
        if (!isValidExtension) {
            alert('File đề thi phải có định dạng PDF, DOC hoặc DOCX\nFile hiện tại: ' + file.name);
            event.target.value = '';
            return;
        }
        
        // Kiểm tra MIME type (backup check)
        const allowedTypes = [
            'application/pdf', 
            'application/msword', 
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/octet-stream' // Một số file .docx có MIME type này
        ];
        
        console.log('File name:', file.name);
        console.log('File type:', file.type);
        console.log('File size:', file.size);
        
        // Chỉ cảnh báo nếu MIME type không khớp nhưng extension đúng
        if (!allowedTypes.includes(file.type) && isValidExtension) {
            console.warn('MIME type không được nhận diện:', file.type, 'nhưng extension hợp lệ');
        }
        
        form.file_de = file;
    }
};

// Hàm xử lý chọn file đáp án
const handleFileDapAnChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        // Kiểm tra kích thước file (max 10MB)
        if (file.size > 10 * 1024 * 1024) {
            alert('File đáp án không được vượt quá 10MB');
            event.target.value = '';
            return;
        }
        
        // Kiểm tra định dạng file bằng extension (đáng tin cậy hơn)
        const fileName = file.name.toLowerCase();
        const allowedExtensions = ['.pdf', '.doc', '.docx'];
        const isValidExtension = allowedExtensions.some(ext => fileName.endsWith(ext));
        
        if (!isValidExtension) {
            alert('File đáp án phải có định dạng PDF, DOC hoặc DOCX\nFile hiện tại: ' + file.name);
            event.target.value = '';
            return;
        }
        
        // Kiểm tra MIME type (backup check)
        const allowedTypes = [
            'application/pdf', 
            'application/msword', 
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/octet-stream' // Một số file .docx có MIME type này
        ];
        
        console.log('File name:', file.name);
        console.log('File type:', file.type);
        console.log('File size:', file.size);
        
        // Chỉ cảnh báo nếu MIME type không khớp nhưng extension đúng
        if (!allowedTypes.includes(file.type) && isValidExtension) {
            console.warn('MIME type không được nhận diện:', file.type, 'nhưng extension hợp lệ');
        }
        
        form.file_dap_an = file;
    }
};

// Hàm xóa file đã chọn
const removeFile = (type) => {
    if (type === 'de') {
        form.file_de = null;
        if (fileDeInput.value) {
            fileDeInput.value.value = '';
        }
    } else {
        form.file_dap_an = null;
        if (fileDapAnInput.value) {
            fileDapAnInput.value.value = '';
        }
    }
};

// Hàm format kích thước file
const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

// Hàm submit form
const submit = () => {
    if (!form.file_de) {
        alert('Vui lòng chọn file đề thi');
        return;
    }
    
    console.log('Submitting form data:', {
        id_ct_ds_dang_ky: form.id_ct_ds_dang_ky,
        loai: form.loai,
        file_de: form.file_de ? form.file_de.name : null,
        file_dap_an: form.file_dap_an ? form.file_dap_an.name : null
    });
    
    form.post(route('dethi.luu'), {
        forceFormData: true,
        onStart: () => {
            console.log('Form submission started');
        },
        onSuccess: (response) => {
            console.log('Form submission successful:', response);
            // Reset form sau khi thành công
            form.reset();
        },
        onError: (errors) => {
            console.error('Lỗi khi tạo đề thi:', errors);
            
            // Hiển thị lỗi chi tiết cho user
            let errorMessage = 'Có lỗi xảy ra:\n';
            if (typeof errors === 'object') {
                Object.keys(errors).forEach(key => {
                    if (Array.isArray(errors[key])) {
                        errorMessage += `${key}: ${errors[key].join(', ')}\n`;
                    } else {
                        errorMessage += `${key}: ${errors[key]}\n`;
                    }
                });
            } else {
                errorMessage += errors;
            }
            alert(errorMessage);
        },
        onFinish: () => {
            console.log('Form submission finished');
        }
    });
};
</script>

<template>
    <AppLayout :role="role">
        <template #sub-link>
            <li class="breadcrumb-item">
                <a :href="route('dethi.hocphan')">Danh sách học phần</a>
            </li>
            <li class="breadcrumb-item">
                <a :href="route('dethi.danhsach', ctDangKy.id)">{{ ctDangKy.hoc_phan?.ten }}</a>
            </li>
            <li class="breadcrumb-item active">
                Tạo đề thi mới
            </li>
        </template>

        <template #content>
            <div class="container py-4">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header bg-success-custom">
                                <h4 class="card-title text-white mb-0">
                                    <i class="fas fa-plus-circle me-2"></i>TẠO ĐỀ THI MỚI
                                </h4>
                            </div>
                            
                            <div class="card-body">
                                <!-- Thông tin học phần -->
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="info-box">
                                            <h6><i class="fas fa-book me-2"></i>Thông tin học phần</h6>
                                            <p><strong><i class="fas fa-code me-1"></i>Mã học phần:</strong> {{ ctDangKy.hoc_phan?.id }}</p>
                                            <p><strong><i class="fas fa-graduation-cap me-1"></i>Tên học phần:</strong> {{ ctDangKy.hoc_phan?.ten }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-box">
                                            <h6><i class="fas fa-clipboard-list me-2"></i>Thông tin đăng ký</h6>
                                            <p><strong><i class="fas fa-calendar-alt me-1"></i>Năm học:</strong> {{ ctDangKy.ds_dang_ky?.nam_hoc }}</p>
                                            <p><strong><i class="fas fa-calendar me-1"></i>Học kỳ:</strong> {{ ctDangKy.ds_dang_ky?.hoc_ki }}</p>
                                            <p><strong><i class="fas fa-clipboard-check me-1"></i>Hình thức thi:</strong> {{ ctDangKy.hinh_thuc_thi }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Form tạo đề thi -->
                                <form @submit.prevent="submit">
                                    <!-- Hiển thị loại đề thi (theo hình thức thi) -->
                                    <div class="form-group mb-4">
                                        <label class="form-label">
                                            <i class="fas fa-tags me-2"></i>Loại đề thi
                                        </label>
                                        <div class="info-display">
                                            <div class="display-value">
                                                <span class="badge badge-primary-custom">
                                                    <i class="fas fa-clipboard-check me-1"></i>
                                                    {{ ctDangKy.hinh_thuc_thi }}
                                                </span>
                                            </div>
                                            <small class="text-muted">Loại đề thi được xác định theo hình thức thi đã đăng ký</small>
                                        </div>
                                    </div>

                                    <!-- Upload file đề thi -->
                                    <div class="form-group mb-4">
                                        <label class="form-label required">
                                            <i class="fas fa-file-upload me-2"></i>File đề thi
                                        </label>
                                        <div class="upload-area" :class="{ 'has-file': form.file_de }">
                                            <div v-if="!form.file_de" class="upload-placeholder">
                                                <i class="fas fa-cloud-upload-alt fa-2x mb-3"></i>
                                                <p class="mb-2">Nhấn để chọn file đề thi</p>
                                                <p class="text-muted small">Hỗ trợ: PDF, DOC, DOCX (Tối đa 10MB)</p>
                                            </div>
                                            <div v-else class="file-preview">
                                                <div class="file-info">
                                                    <i class="fas fa-file-alt fa-2x text-success mb-2"></i>
                                                    <p class="file-name">{{ deFileName }}</p>
                                                    <p class="file-size text-muted">{{ formatFileSize(form.file_de.size) }}</p>
                                                </div>
                                                <button type="button" @click="removeFile('de')" class="btn btn-sm btn-outline-danger remove-btn">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            <input 
                                                ref="fileDeInput"
                                                type="file" 
                                                @change="handleFileDeChange"
                                                accept=".pdf,.doc,.docx"
                                                class="file-input"
                                                required
                                            >
                                        </div>
                                        <div v-if="form.errors.file_de" class="text-danger mt-1">
                                            {{ form.errors.file_de }}
                                        </div>
                                    </div>

                                    <!-- Upload file đáp án (không bắt buộc) -->
                                    <div class="form-group mb-4">
                                        <label class="form-label">
                                            <i class="fas fa-file-alt me-2"></i>File đáp án
                                        </label>
                                        <div class="upload-area" :class="{ 'has-file': form.file_dap_an }">
                                            <div v-if="!form.file_dap_an" class="upload-placeholder">
                                                <i class="fas fa-cloud-upload-alt fa-2x mb-3"></i>
                                                <p class="mb-2">Nhấn để chọn file đáp án</p>
                                                <p class="text-muted small">Hỗ trợ: PDF, DOC, DOCX (Tối đa 10MB)</p>
                                            </div>
                                            <div v-else class="file-preview">
                                                <div class="file-info">
                                                    <i class="fas fa-file-alt fa-2x text-info mb-2"></i>
                                                    <p class="file-name">{{ dapAnFileName }}</p>
                                                    <p class="file-size text-muted">{{ formatFileSize(form.file_dap_an.size) }}</p>
                                                </div>
                                                <button type="button" @click="removeFile('dap_an')" class="btn btn-sm btn-outline-danger remove-btn">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            <input 
                                                ref="fileDapAnInput"
                                                type="file" 
                                                @change="handleFileDapAnChange"
                                                accept=".pdf,.doc,.docx"
                                                class="file-input"
                                            >
                                        </div>
                                        <div v-if="form.errors.file_dap_an" class="text-danger mt-1">
                                            {{ form.errors.file_dap_an }}
                                        </div>
                                    </div>

                                    <!-- Nút submit -->
                                    <div class="d-flex justify-content-end gap-3">
                                        <a :href="route('dethi.danhsach', ctDangKy.id)" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left me-2"></i>Hủy
                                        </a>
                                        <button 
                                            type="submit" 
                                            class="btn btn-success-custom"
                                            :disabled="form.processing"
                                        >
                                            <i class="fas fa-save me-2"></i>
                                            <span v-if="form.processing">Đang lưu...</span>
                                            <span v-else>Lưu đề thi</span>
                                        </button>
                                    </div>
                                </form>
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

.btn-success-custom {
    background: linear-gradient(135deg, rgb(94, 181, 98), rgb(76, 150, 80));
    border: none;
    color: white;
}

.btn-success-custom:hover {
    background: linear-gradient(135deg, rgb(76, 150, 80), rgb(60, 120, 64));
    color: white;
}

.btn-success-custom:disabled {
    background: #6c757d;
}

.info-box {
    background: linear-gradient(135deg, #f8fff8 0%, #e8fde8 100%);
    border-radius: 12px;
    border: 1px solid rgba(94, 181, 98, 0.2);
    padding: 20px;
    margin-bottom: 0;
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

.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 8px;
}

.form-label.required:after {
    content: " *";
    color: #dc3545;
}

.form-label i {
    color: rgb(94, 181, 98);
}

.upload-area {
    border: 2px dashed rgba(94, 181, 98, 0.3);
    border-radius: 12px;
    padding: 30px;
    text-align: center;
    background: linear-gradient(135deg, #f8fff8 0%, #e8fde8 100%);
    position: relative;
    cursor: pointer;
    transition: all 0.3s ease;
    min-height: 150px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.upload-area:hover {
    border-color: rgb(94, 181, 98);
    background: linear-gradient(135deg, #e8fde8 0%, #d4f5d7 100%);
}

.upload-area.has-file {
    border-color: rgb(94, 181, 98);
    background: linear-gradient(135deg, #e8fde8 0%, #d4f5d7 100%);
}

.upload-placeholder {
    color: #6c757d;
}

.upload-placeholder i {
    color: rgb(94, 181, 98);
}

.file-input {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}

.file-preview {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    padding: 0 20px;
}

.file-info {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex-grow: 1;
}

.file-name {
    font-weight: 600;
    margin-bottom: 4px;
    color: #495057;
    word-break: break-all;
}

.file-size {
    font-size: 0.9em;
    margin-bottom: 0;
}

.remove-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
}

/* Style cho info display */
.info-display {
    padding: 15px;
    background: linear-gradient(135deg, #f8fff8 0%, #e8fde8 100%);
    border-radius: 8px;
    border: 1px solid rgba(94, 181, 98, 0.2);
}

.display-value {
    margin-bottom: 5px;
}

.badge-primary-custom {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    padding: 8px 12px;
    font-size: 0.9em;
    border-radius: 6px;
}
</style>
