<template>
    <AppLayout role="dbcl">
        <template v-slot:sub-link>
            <li class="breadcrumb-item">
                <a :href="route('quality.thongbao.index')">Danh sách thông báo</a>
            </li>
            <li class="breadcrumb-item active">
                Thêm mới thông báo
            </li>
        </template>

        <template v-slot:content>
            <div class="content">
                <div class="card border-radius-lg shadow-lg animated-fade-in">
                    <div class="card-header d-flex justify-content-between align-items-center bg-info-qo text-white p-4 bg-success-tb">
                        <h3 class="mb-0">THÊM MỚI THÔNG BÁO</h3>
                    </div>

                    <div class="card-body">
                        <form @submit.prevent="submit" enctype="multipart/form-data">
                            <!-- Tiêu đề -->
                            <div class="mb-4">
                                <label for="title" class="form-label fw-bold">Tiêu đề <span class="text-danger">*</span></label>
                                <input 
                                    type="text" 
                                    id="title" 
                                    v-model="form.title" 
                                    class="form-control" 
                                    :class="{ 'is-invalid': form.errors.title }"
                                    placeholder="Nhập tiêu đề thông báo"
                                    required
                                />
                                <div v-if="form.errors.title" class="invalid-feedback">
                                    {{ form.errors.title }}
                                </div>
                            </div>

                            <!-- Nội dung -->
                            <div class="mb-4">
                                <label for="content" class="form-label fw-bold">Nội dung <span class="text-danger">*</span></label>
                                <textarea 
                                    id="content" 
                                    v-model="form.content" 
                                    class="form-control" 
                                    :class="{ 'is-invalid': form.errors.content }"
                                    rows="6"
                                    placeholder="Nhập nội dung thông báo"
                                    required
                                ></textarea>
                                <div v-if="form.errors.content" class="invalid-feedback">
                                    {{ form.errors.content }}
                                </div>
                            </div>

                            <!-- File đính kèm -->
                            <div class="mb-4">
                                <label for="files" class="form-label fw-bold">File đính kèm</label>
                                <div class="input-group">
                                    <input 
                                        type="file" 
                                        id="files" 
                                        ref="fileInput"
                                        class="form-control" 
                                        :class="{ 'is-invalid': form.errors.files }"
                                        multiple
                                        @change="handleFileUpload"
                                    />
                                    <button 
                                        type="button" 
                                        class="btn btn-outline-secondary"
                                        @click="clearFiles"
                                    >
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div v-if="form.errors.files" class="text-danger mt-1">
                                    {{ form.errors.files }}
                                </div>
                                <div class="form-text">
                                    Hỗ trợ các định dạng: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, GIF. Kích thước tối đa: 10MB/file
                                </div>
                                
                                <!-- Hiển thị file đã chọn -->
                                <div v-if="selectedFiles.length > 0" class="mt-3">
                                    <p class="mb-2">Đã chọn {{ selectedFiles.length }} file:</p>
                                    <ul class="list-group">
                                        <li v-for="(file, index) in selectedFiles" :key="index" class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <i :class="getFileIcon(file.type)" class="me-2"></i>
                                                {{ file.name }} ({{ formatFileSize(file.size) }})
                                            </div>
                                            <button 
                                                type="button" 
                                                class="btn btn-sm btn-outline-danger"
                                                @click="removeFile(index)"
                                            >
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Nút xử lý -->
                            <div class="d-flex justify-content-end mt-4">
                                <Link
                                    :href="route('quality.thongbao.index')"
                                    class="btn btn-secondary me-2"
                                >
                                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                                </Link>
                                <button 
                                    type="submit" 
                                    class="btn btn-primary"
                                    :disabled="form.processing"
                                >
                                    <i class="fas fa-paper-plane me-2"></i>
                                    <span v-if="form.processing">Đang gửi...</span>
                                    <span v-else>Gửi thông báo</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({
    title: '',
    content: '',
    files: []
});

const selectedFiles = ref([]);
const fileInput = ref(null);

// Xử lý khi upload file
const handleFileUpload = (event) => {
    const files = Array.from(event.target.files);
    selectedFiles.value = files;
    form.files = files;
};

// Xóa một file cụ thể
const removeFile = (index) => {
    selectedFiles.value.splice(index, 1);
    form.files = selectedFiles.value;
};

// Xóa tất cả file
const clearFiles = () => {
    selectedFiles.value = [];
    form.files = [];
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

// Format kích thước file
const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

// Lấy icon phù hợp với loại file
const getFileIcon = (mimeType) => {
    if (mimeType.includes('pdf')) {
        return 'fas fa-file-pdf text-danger';
    } else if (mimeType.includes('word') || mimeType.includes('doc')) {
        return 'fas fa-file-word text-primary';
    } else if (mimeType.includes('excel') || mimeType.includes('sheet') || mimeType.includes('xls')) {
        return 'fas fa-file-excel text-success';
    } else if (mimeType.includes('image')) {
        return 'fas fa-file-image text-info';
    } else {
        return 'fas fa-file text-secondary';
    }
};

// Gửi form
const submit = () => {
    form.post(route('quality.thongbao.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            selectedFiles.value = [];
        }
    });
};
</script>
