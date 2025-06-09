<script setup>
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    ctdsdangkies: {
        type: Array,
        required: true
    },
    role: {
        type: String,
        required: true
    },
    filters: {
        type: Object,
        default: () => ({})
    }
});

// Thêm các biến reactive cho bộ lọc
const searchTerm = ref(props.filters.search || '');
const selectedNamHoc = ref(props.filters.nam_hoc || '');
const selectedHocKy = ref(props.filters.hoc_ky || '');
const selectedHinhThucThi = ref(props.filters.hinh_thuc_thi || '');
const debounceTimeout = ref(null);

// Hàm tạo danh sách năm học trong 10 năm gần nhất
const generateNamHocList = () => {
    const currentYear = new Date().getFullYear();
    const namHocList = [
        { value: '', label: 'Tất cả năm học' }
    ];
    
    // Tạo danh sách năm học từ năm hiện tại trở về trước 10 năm
    for (let i = 0; i < 10; i++) {
        const year = currentYear - i;
        const nextYear = year + 1;
        namHocList.push({
            value: `${year}-${nextYear}`,
            label: `${year}-${nextYear}`
        });
    }
    
    return namHocList;
};

const namHocList = generateNamHocList();

const hocKyList = [
    { value: '', label: 'Tất cả học kỳ' },
    { value: '1', label: 'Học kỳ 1' },
    { value: '2', label: 'Học kỳ 2' },
    { value: 'Hè', label: 'Học kỳ hè' },
];

const hinhThucThiList = [
    { value: '', label: 'Tất cả hình thức thi' },
    { value: 'Tự luận/Vấn đáp', label: 'Tự luận/Vấn đáp' },
    { value: 'Trắc nghiệm', label: 'Trắc nghiệm' },
    { value: 'Tự luận', label: 'Tự luận' },
];

// Hàm thực hiện tìm kiếm với debounce
const performSearch = () => {
    if (debounceTimeout.value) {
        clearTimeout(debounceTimeout.value);
    }
    debounceTimeout.value = setTimeout(() => {
        router.get(
            route('dethi.hocphan'),
            {
                search: searchTerm.value,
                nam_hoc: selectedNamHoc.value,
                hoc_ky: selectedHocKy.value,
                hinh_thuc_thi: selectedHinhThucThi.value,
            },
            {
                preserveState: true,
                replace: true,
            }
        );
    }, 300); // Delay 300ms để tránh gọi API quá nhiều
};

// Watch các giá trị thay đổi để thực hiện tìm kiếm
watch([searchTerm, selectedNamHoc, selectedHocKy, selectedHinhThucThi], () => {
    performSearch();
});
</script>

<template>
    <AppLayout :role="role">
        <template #sub-link>
            <li class="breadcrumb-item">
                <a :href="route('dethi.hocphan')">Danh sách học phần</a>
            </li>
            <li class="breadcrumb-item active">
                Ngân hàng đề thi
            </li>
        </template>
        <template #content>
            <div class="container py-4">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-success-custom">
                                <h4 class="card-title text-white">
                                    <i class="fas fa-file-alt me-2"></i>NGÂN HÀNG ĐỀ THI - DANH SÁCH HỌC PHẦN
                                </h4>
                            </div>
                            <div class="card-body">
                                <!-- Bộ lọc -->
                                <div class="row mb-4">
                                    <div class="col-md-3 mb-3">
                                        <div class="input-group">
                                            <span class="input-group-text bg-success-light">
                                                <i class="fas fa-search"></i>
                                            </span>
                                            <input 
                                                type="text" 
                                                v-model="searchTerm"
                                                class="form-control" 
                                                placeholder="Tìm theo tên/mã học phần..."
                                            >
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="input-group">
                                            <span class="input-group-text bg-success-light">
                                                <i class="fas fa-calendar-alt"></i>
                                            </span>
                                            <select v-model="selectedNamHoc" class="form-control">
                                                <option v-for="namHoc in namHocList" :key="namHoc.value" :value="namHoc.value">
                                                    {{ namHoc.label }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="input-group">
                                            <span class="input-group-text bg-success-light">
                                                <i class="fas fa-calendar"></i>
                                            </span>
                                            <select v-model="selectedHocKy" class="form-control">
                                                <option v-for="hocKy in hocKyList" :key="hocKy.value" :value="hocKy.value">
                                                    {{ hocKy.label }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="input-group">
                                            <span class="input-group-text bg-success-light">
                                                <i class="fas fa-clipboard-check"></i>
                                            </span>
                                            <select v-model="selectedHinhThucThi" class="form-control">
                                                <option v-for="hinhThuc in hinhThucThiList" :key="hinhThuc.value" :value="hinhThuc.value">
                                                    {{ hinhThuc.label }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div v-for="ctDangKy in ctdsdangkies" :key="ctDangKy.id" 
                                        class="col-md-4 mb-4">
                                        <div class="de-thi-card" @click="() => router.get(route('dethi.danhsach', ctDangKy.id))" style="cursor:pointer;">
                                            <div class="card-icon">
                                                <i class="fas fa-file-alt"></i>
                                            </div>
                                            <h3 class="de-thi-title">{{ ctDangKy.hoc_phan?.ten }}</h3>
                                            <p class="de-thi-info">
                                                <i class="fas fa-code"></i>
                                                Mã HP: {{ ctDangKy.hoc_phan?.id }}
                                            </p>
                                            <p class="de-thi-info">
                                                <i class="fas fa-calendar-alt"></i>
                                                Năm học: {{ ctDangKy.ds_dang_ky?.nam_hoc }}
                                            </p>
                                            <p class="de-thi-info">
                                                <i class="fas fa-calendar"></i>
                                                Học kỳ: {{ ctDangKy.ds_dang_ky?.hoc_ki }}
                                            </p>
                                            <p class="de-thi-info">
                                                <i class="fas fa-clipboard-check"></i>
                                                {{ ctDangKy.hinh_thuc_thi }}
                                            </p>
                                            <div class="card-action">
                                                <span class="action-text">Nhấn để xem đề thi</span>
                                                <i class="fas fa-arrow-right"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="ctdsdangkies.length === 0" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                        <p class="text-muted fs-5">Không tìm thấy học phần nào</p>
                                        <p class="text-muted">Vui lòng thử lại với từ khóa khác</p>
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
/* Màu chủ đạo - xanh lá */
.bg-success-custom {
    background: linear-gradient(135deg, rgb(94, 181, 98), rgb(76, 150, 80)) !important;
}

.bg-success-light {
    background-color: rgba(94, 181, 98, 0.1);
    color: rgb(94, 181, 98);
}

.de-thi-card {
    background: linear-gradient(135deg, #f8fff8 0%, #e8fde8 100%);
    border-radius: 20px;
    border: 2px solid rgb(94, 181, 98);
    box-shadow: 0 6px 20px rgba(94, 181, 98, 0.12);
    padding: 32px 24px;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.de-thi-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, rgb(94, 181, 98), rgb(76, 150, 80));
}

.de-thi-card:hover {
    box-shadow: 0 12px 32px rgba(94, 181, 98, 0.2);
    transform: translateY(-6px) scale(1.02);
    border-color: rgb(76, 150, 80);
}

.card-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, rgb(94, 181, 98), rgb(76, 150, 80));
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
    box-shadow: 0 4px 12px rgba(94, 181, 98, 0.3);
}

.card-icon i {
    font-size: 24px;
    color: white;
}

.de-thi-title {
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 20px;
    color: rgb(76, 150, 80);
    text-align: center;
    line-height: 1.4;
}

.de-thi-info {
    color: #424242;
    margin-bottom: 12px;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.de-thi-info i {
    width: 16px;
    color: rgb(94, 181, 98);
    font-size: 12px;
}

.card-action {
    margin-top: 20px;
    padding-top: 16px;
    border-top: 1px solid rgba(94, 181, 98, 0.2);
    display: flex;
    align-items: center;
    gap: 8px;
    color: rgb(76, 150, 80);
    font-weight: 600;
    font-size: 14px;
    opacity: 0.8;
    transition: opacity 0.3s ease;
}

.de-thi-card:hover .card-action {
    opacity: 1;
}

.card-action i {
    font-size: 12px;
    transition: transform 0.3s ease;
}

.de-thi-card:hover .card-action i {
    transform: translateX(4px);
}

.empty-state {
    max-width: 400px;
    margin: 0 auto;
}
</style>
