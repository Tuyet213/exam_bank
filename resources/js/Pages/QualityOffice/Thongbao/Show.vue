<template>
    <AppLayout role="dbcl">
        <template v-slot:sub-link>
            <li class="breadcrumb-item">
                <a :href="route('quality.thongbao.index')">Danh sách thông báo</a>
            </li>
            <li class="breadcrumb-item active">
                Chi tiết thông báo
            </li>
        </template>

        <template v-slot:content>
            <div class="content">
                <div class="card border-radius-lg shadow-lg animated-fade-in mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center bg-info-qo text-white p-4">
                        <h3 class="mb-0">CHI TIẾT THÔNG BÁO</h3>
                        <div class="timestamp">
                            <i class="far fa-clock me-2"></i>
                            {{ formatDate(thongbao.created_at) }}
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Tiêu đề -->
                        <div class="thongbao-title mb-4">
                            <h4 class="fw-bold">{{ thongbao.title }}</h4>
                        </div>

                        <!-- Nội dung -->
                        <div class="thongbao-content border-top border-bottom py-4 mb-4">
                            <div class="content-text">
                                <p v-for="(paragraph, index) in contentParagraphs" :key="index" class="mb-3">
                                    {{ paragraph }}
                                </p>
                            </div>
                        </div>

                        <!-- File đính kèm -->
                        <div class="thongbao-files mb-4" v-if="thongbao.files && thongbao.files.length > 0">
                            <h5 class="fw-bold mb-3">
                                <i class="fas fa-paperclip me-2"></i>
                                File đính kèm ({{ thongbao.files.length }})
                            </h5>
                            <div class="list-group">
                                <a 
                                    v-for="(file, index) in thongbao.files" 
                                    :key="index"
                                    :href="file.url"
                                    target="_blank"
                                    class="list-group-item list-group-item-action d-flex align-items-center"
                                >
                                    <i :class="getFileIcon(file.name)" class="me-3 fa-lg"></i>
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold">{{ file.name }}</div>
                                    </div>
                                    <i class="fas fa-download ms-3"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Nút điều hướng -->
                        <div class="d-flex justify-content-end mt-4">
                            <Link
                                :href="route('quality.thongbao.index')"
                                class="btn btn-secondary"
                            >
                                <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
                            </Link>
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
import { computed } from 'vue';

const props = defineProps({
    thongbao: Object
});

// Định dạng ngày tháng
const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleString('vi-VN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

// Chia nội dung thành các đoạn văn
const contentParagraphs = computed(() => {
    if (!props.thongbao.content) return [];
    return props.thongbao.content.split('\n').filter(p => p.trim() !== '');
});

// Xác định icon cho loại file
const getFileIcon = (fileName) => {
    const extension = fileName.split('.').pop().toLowerCase();
    
    const iconMap = {
        'pdf': 'fas fa-file-pdf text-danger',
        'doc': 'fas fa-file-word text-primary',
        'docx': 'fas fa-file-word text-primary',
        'xls': 'fas fa-file-excel text-success',
        'xlsx': 'fas fa-file-excel text-success',
        'jpg': 'fas fa-file-image text-info',
        'jpeg': 'fas fa-file-image text-info',
        'png': 'fas fa-file-image text-info',
        'gif': 'fas fa-file-image text-info'
    };
    
    return iconMap[extension] || 'fas fa-file text-secondary';
};
</script>

<style scoped>
.bg-info-qo {
    background-color: #5cb85c;
}

.animated-fade-in {
    animation: fadeIn 0.5s;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.timestamp {
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.8);
}

.thongbao-title h4 {
    color: #333;
    line-height: 1.4;
}

.thongbao-content {
    font-size: 1.05rem;
    line-height: 1.6;
    color: #444;
}

.list-group-item:hover {
    background-color: #f8f9fa;
}

.list-group-item i.fa-download {
    opacity: 0.6;
    transition: opacity 0.2s;
}

.list-group-item:hover i.fa-download {
    opacity: 1;
}
</style> 