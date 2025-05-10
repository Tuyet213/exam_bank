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
    { value: '3', label: 'Học kỳ hè' },
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
            route('cauhoi.hocphan'),
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
                <a :href="route('cauhoi.hocphan')">Danh sách học phần</a>
            </li>
            <li class="breadcrumb-item active">
                Danh sách câu hỏi
            </li>
        </template>
        <template #content>
            <div class="container py-4">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-success-tb">
                                <h4 class="card-title text-white">DANH SÁCH HỌC PHẦN</h4>
                            </div>
                            <div class="card-body">
                                <!-- Bộ lọc -->
                                <div class="row mb-4">
                                    <div class="col-md-3 mb-3">
                                        <input 
                                            type="text" 
                                            v-model="searchTerm"
                                            class="form-control" 
                                            placeholder="Tìm theo tên/mã học phần..."
                                        >
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <select v-model="selectedNamHoc" class="form-control">
                                            <option v-for="namHoc in namHocList" :key="namHoc.value" :value="namHoc.value">
                                                {{ namHoc.label }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <select v-model="selectedHocKy" class="form-control">
                                            <option v-for="hocKy in hocKyList" :key="hocKy.value" :value="hocKy.value">
                                                {{ hocKy.label }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <select v-model="selectedHinhThucThi" class="form-control">
                                            <option v-for="hinhThuc in hinhThucThiList" :key="hinhThuc.value" :value="hinhThuc.value">
                                                {{ hinhThuc.label }}
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div v-for="ctDangKy in ctdsdangkies" :key="ctDangKy.id" 
                                        class="col-md-4 mb-4">
                                        <div class="hoc-phan-card" @click="() => router.get(route('cauhoi.danhsach', ctDangKy.id))" style="cursor:pointer;">
                                            <h3 class="hoc-phan-title">{{ ctDangKy.hoc_phan.ten }}</h3>
                                            <p class="hoc-phan-info">Mã học phần: {{ ctDangKy.hoc_phan.id }}</p>
                                            <p class="hoc-phan-info">Năm học: {{ ctDangKy.ds_dang_ky.nam_hoc }}</p>
                                            <p class="hoc-phan-info">Học kỳ: {{ ctDangKy.ds_dang_ky.hoc_ki }}</p>
                                            <p class="hoc-phan-info">Hình thức thi: {{ 
                                                ctDangKy.hinh_thuc_thi
                                            }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="ctdsdangkies.length === 0" class="text-center py-4">
                                    <p class="text-muted">Không tìm thấy học phần nào</p>
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
.hoc-phan-card {
    background: linear-gradient(135deg, #e3f2fd 0%, #f1f8e9 100%);
    border-radius: 16px;
    border: 2px solid #4caf50;
    box-shadow: 0 4px 16px rgba(76, 175, 80, 0.10);
    padding: 28px 20px;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    transition: box-shadow 0.3s, transform 0.3s;
}

.hoc-phan-card:hover {
    box-shadow: 0 8px 24px rgba(33, 150, 243, 0.18);
    transform: translateY(-4px) scale(1.03);
}

.hoc-phan-title {
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 16px;
    color: #2e7d32;
    text-align: center;
}

.hoc-phan-info {
    color: #333;
    margin-bottom: 10px;
    font-size: 15px;
    text-align: center;
}

.button-group {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.btn {
    display: block;
    width: 100%;
    text-align: center;
    padding: 8px 12px;
    border-radius: 4px;
    font-weight: 500;
    font-size: 14px;
    cursor: pointer;
    border: none;
    color: white;
}

.btn-primary {
    background-color: #2196f3;
}

.btn-primary:hover {
    background-color: #1976d2;
}

.btn-success {
    background-color: #4caf50;
}

.btn-success:hover {
    background-color: #388e3c;
}

.btn-warning {
    background-color: #ff9800;
    color: #fff;
}

.btn-warning:hover {
    background-color: #f57c00;
}
</style> 