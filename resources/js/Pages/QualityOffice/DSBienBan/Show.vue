<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    bienban: Object,
    ctDSDangKy: Object,
    hocPhan: Object,
    boMon: Object,
    khoa: Object,
    giangVien: Object,
    thanhViens: Array,
    errors: Object,
    success: {
        type: String,
        default: ''
    }
});

const showRejectForm = ref(false);
const rejectForm = useForm({
    ly_do: ''
});

const approveForm = useForm({});

const getStatusClass = computed(() => {
    if (props.ctDSDangKy.trang_thai === 'Completed') {
        return 'badge bg-success';
    } else if (props.bienban.duyet) {
        return 'badge bg-success';
    } else {
        return 'badge bg-warning';
    }
});

const getStatusText = computed(() => {
    if (props.ctDSDangKy.trang_thai === 'Completed') {
        return 'Đã hoàn thành';
    } else {
        return 'Chờ duyệt';
    }
});

// Xử lý an toàn cho năm học và học kỳ
const namHoc = computed(() => {
    return props.ctDSDangKy?.dsDangKy?.nam_hoc || props.ctDSDangKy?.ds_dang_ky?.nam_hoc || 'Không có thông tin';
});

const hocKy = computed(() => {
    return props.ctDSDangKy?.dsDangKy?.hoc_ki || props.ctDSDangKy?.ds_dang_ky?.hoc_ki || 'Không có thông tin';
});

const submitApprove = () => {
    approveForm.post(route('quality.dsbienban.approve-with-email', props.bienban.id));
};

const submitReject = () => {
    rejectForm.post(route('quality.dsbienban.reject-with-email', props.bienban.id));
};

const toggleRejectForm = () => {
    showRejectForm.value = !showRejectForm.value;
};
</script>

<template>
    <AppLayout role="dbcl">
        <template v-slot:sub-link>
            <li class="breadcrumb-item">
                <a :href="route('quality.dsbienban.index')">Danh sách biên bản họp bộ môn</a>
            </li>
            <li class="breadcrumb-item active">
                Chi tiết biên bản
            </li>
        </template>

        <template v-slot:content>
            <div class="content">
                <!-- Hiển thị thông báo thành công -->
                <div v-if="success" class="alert alert-success mb-4">
                    {{ success }}
                </div>

                <div class="card border-radius-lg shadow-lg animated-fade-in mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center bg-info-qo text-white p-4">
                        <h3 class="mb-0">CHI TIẾT BIÊN BẢN HỌP BỘ MÔN</h3>
                        <span :class="getStatusClass" class="badge fs-5">{{ getStatusText }}</span>
                    </div>

                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5 class="fw-bold mb-3">Thông tin học phần</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <th width="30%">Mã học phần:</th>
                                                <td>{{ hocPhan.id }}</td>
                                            </tr>
                                            <tr>
                                                <th>Tên học phần:</th>
                                                <td>{{ hocPhan.ten }}</td>
                                            </tr>
                                            <tr>
                                                <th>Khoa:</th>
                                                <td>{{ khoa.ten }}</td>
                                            </tr>
                                            <tr>
                                                <th>Bộ môn:</th>
                                                <td>{{ boMon.ten }}</td>
                                            </tr>
                                            <tr>
                                                <th>Năm học:</th>
                                                <td>{{ namHoc }}</td>
                                            </tr>
                                            <tr>
                                                <th>Học kỳ:</th>
                                                <td>{{ hocKy }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h5 class="fw-bold mb-3">Thông tin người biên soạn</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <th width="30%">Mã giảng viên:</th>
                                                <td>{{ giangVien.id }}</td>
                                            </tr>
                                            <tr>
                                                <th>Họ tên:</th>
                                                <td>{{ giangVien.name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Email:</th>
                                                <td>{{ giangVien.email }}</td>
                                            </tr>
                                            <tr>
                                                <th>Số điện thoại:</th>
                                                <td>{{ giangVien.sdt || 'Không có thông tin' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Thời gian họp:</th>
                                                <td>{{ bienban.thoi_gian || 'Không có thông tin' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Số giờ quy đổi:</th>
                                                <td class="fw-bold">{{ ctDSDangKy.so_gio || 0 }} giờ</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <h5 class="fw-bold mb-3">Danh sách thành viên tham gia họp</h5>
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>STT</th>
                                        <th>Mã viên chức</th>
                                        <th>Họ tên</th>
                                        <th>Nhiệm vụ</th>
                                        <th>Email</th>
                                        <th>Số giờ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(tv, index) in thanhViens" :key="tv.id">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ tv.vien_chuc.id }}</td>
                                        <td>{{ tv.vien_chuc.name }}</td>
                                        <td>{{ tv.nhiem_vu?.ten }}</td>
                                        <td>{{ tv.vien_chuc.email }}</td>
                                        <td class="fw-bold">{{ tv.so_gio || 0 }} giờ</td>
                                    </tr>
                                    <tr v-if="!thanhViens || thanhViens.length === 0">
                                        <td colspan="5" class="text-center">Không có thành viên tham gia</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <h5 class="fw-bold mb-3">Nội dung biên bản</h5>
                        <div class="card mb-4">
                            <div class="card-body p-4 bg-light text-center">
                                <p class="mb-3">Để xem nội dung chi tiết của biên bản, vui lòng tải xuống file biên bản dưới đây:</p>
                                <a :href="route('quality.dsbienban.download', bienban.id)" class="btn btn-primary">
                                    <i class="fas fa-download me-2"></i> Tải xuống biên bản
                                </a>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <!-- <a :href="route('quality.dsbienban.index')" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i> Quay lại
                            </a> -->

                            <div v-if="!showRejectForm">
                                <button 
                                    v-if="!bienban.duyet && ctDSDangKy.trang_thai !== 'Completed'" 
                                    @click="submitApprove" 
                                    class="btn btn-success me-2"
                                    :disabled="approveForm.processing"
                                >
                                    <i class="fas fa-check me-2"></i> Duyệt biên bản
                                </button>
                                <button 
                                    v-if="!showRejectForm" 
                                    @click="toggleRejectForm" 
                                    class="btn btn-danger"
                                >
                                    <i class="fas fa-times me-2"></i> Từ chối
                                </button>
                            </div>

                            <div v-else class="rejection-form w-100">
                                <div class="card border-danger">
                                    <div class="card-header bg-danger text-white">
                                        <h5 class="mb-0">Lý do từ chối</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="ly_do" class="form-label">Nhập lý do từ chối biên bản <span class="text-danger">*</span></label>
                                            <textarea 
                                                id="ly_do" 
                                                v-model="rejectForm.ly_do" 
                                                class="form-control" 
                                                rows="4" 
                                                placeholder="Vui lòng nhập lý do từ chối biên bản..."
                                                :class="{ 'is-invalid': errors?.ly_do }"
                                            ></textarea>
                                            <div v-if="errors?.ly_do" class="invalid-feedback">
                                                {{ errors.ly_do }}
                                            </div>
                                            <div class="form-text">Lý do từ chối sẽ được gửi qua email đến Trưởng bộ môn.</div>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <button @click="toggleRejectForm" class="btn btn-secondary me-2">Hủy</button>
                                            <button 
                                                @click="submitReject" 
                                                class="btn btn-danger"
                                                :disabled="rejectForm.processing || !rejectForm.ly_do"
                                            >
                                                <i class="fas fa-paper-plane me-2"></i> Gửi lý do từ chối
                                            </button>
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
.bg-info-qo {
    background-color: #5cb85c;
}

.table th {
    background-color: #f8f9fa;
}

.card-header h3 {
    font-size: 1.5rem;
}

.animated-fade-in {
    animation: fadeIn 0.5s;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.rejection-form {
    transition: all 0.3s ease;
}
</style> 