<script setup>
import TBMLayout from "@/Layouts/TBMLayout.vue";
import { Link, useForm } from "@inertiajs/vue3";
import { ref, computed } from 'vue';

const props = defineProps({
    bien_ban: {
        type: Object,
        required: true
    },
    vien_chucs: {
        type: Array,
        required: true
    },
    nhiem_vus: {
        type: Array,
        required: true
    }
});

// Tìm ID của từng nhiệm vụ từ danh sách được truyền xuống
const chuTichId = computed(() => {
    return props.nhiem_vus.find(nv => nv.ten === 'Chủ tịch')?.id;
});

const thuKyId = computed(() => {
    return props.nhiem_vus.find(nv => nv.ten === 'Thư ký')?.id;
});

const phanBienId = computed(() => {
    return props.nhiem_vus.find(nv => nv.ten === 'Cán bộ phản biện')?.id;
});

// Form chính để cập nhật thông tin biên bản
const form = useForm({
    thoi_gian: props.bien_ban.thoi_gian ? props.bien_ban.thoi_gian.replace(' ', 'T').substring(0, 16) : '',
    dia_diem: props.bien_ban.dia_diem,
    ds_hop: props.bien_ban.ds_hop.map(hop => ({
        id: hop.id,
        id_vien_chuc: hop.id_vien_chuc,
        id_nhiem_vu: hop.id_nhiem_vu
    }))
});

// Xử lý submit form
const submit = () => {
    form.put(route('tbm.dsbienban.update', props.bien_ban.id), {
        preserveScroll: true,
        onSuccess: () => {
            alert('Cập nhật biên bản họp thành công');
        },
        onError: (errors) => {
            console.error('Lỗi:', errors);
            alert('Có lỗi xảy ra khi cập nhật biên bản họp');
        }
    });
};
</script>

<template>
    <TBMLayout>
        <template v-slot:sub-link>
            <li class="breadcrumb-item">
                <Link :href="route('tbm.dsbienban.index')">Danh sách biên bản họp</Link>
            </li>
            <li class="breadcrumb-item active">{{ bien_ban.ct_d_s_dang_ky.hoc_phan.ten }}</li>
        </template>

        <template v-slot:content>
            <div class="content">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">CHỈNH SỬA BIÊN BẢN HỌP</h3>
                    </div>

                    <div class="card-body">
                        <!-- Thông tin chi tiết đăng ký -->
                        <div class="mb-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Học phần:</strong> 
                                    {{ bien_ban.ct_d_s_dang_ky.hoc_phan.ten }}
                                </div>
                                <div class="col-md-4">
                                    <strong>Giảng viên:</strong>
                                    {{ bien_ban.ct_d_s_dang_ky.vien_chuc.name }}
                                </div>
                                <div class="col-md-4">
                                    <strong>Loại ngân hàng:</strong>
                                    {{ bien_ban.ct_d_s_dang_ky.loai_ngan_hang == 1 ? 'Ngân hàng câu hỏi' : 'Ngân hàng đề thi' }}
                                </div>
                                <div class="col-md-4">
                                    <strong>Số lượng:</strong>
                                    {{ bien_ban.ct_d_s_dang_ky.so_luong }}
                                </div>
                                <div class="col-md-4">
                                    <strong>Hình thức thi:</strong>
                                    {{ bien_ban.ct_d_s_dang_ky.hinh_thuc_thi }}
                                </div>
                            </div>
                        </div>

                        <form @submit.prevent="submit">
                            <!-- Thông tin cơ bản -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Thời gian <span class="text-danger">*</span></label>
                                    <input 
                                        type="datetime-local"
                                        v-model="form.thoi_gian"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.thoi_gian }"
                                        required
                                    >
                                    <div class="invalid-feedback" v-if="form.errors.thoi_gian">
                                        {{ form.errors.thoi_gian }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Địa điểm <span class="text-danger">*</span></label>
                                    <input 
                                        type="text"
                                        v-model="form.dia_diem"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.dia_diem }"
                                        required
                                    >
                                    <div class="invalid-feedback" v-if="form.errors.dia_diem">
                                        {{ form.errors.dia_diem }}
                                    </div>
                                </div>
                            </div>

                            <!-- Danh sách người tham gia -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6>Danh sách tham gia:</h6>
                                    <!-- <button 
                                        type="button"
                                        class="btn btn-success btn-sm"
                                        @click="addThanhVien"
                                    >
                                        <i class="fas fa-plus"></i> Thêm người tham gia
                                    </button> -->
                                </div>

                                <div 
                                    v-for="(thanhVien, index) in form.ds_hop" 
                                    :key="index"
                                    class="border rounded p-3 mb-2"
                                >
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>{{ nhiem_vus.find(nv => nv.id === thanhVien.id_nhiem_vu)?.ten }}</span>
                                        <!-- <button 
                                            type="button"
                                            class="btn btn-danger btn-sm"
                                            @click="removeThanhVien(index)"
                                        >
                                            <i class="fas fa-trash"></i>
                                        </button> -->
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <label class="form-label">Tên <span class="text-danger">*</span></label>
                                            <select 
                                                v-model="thanhVien.id_vien_chuc"
                                                class="form-select"
                                                required
                                            >
                                                <option value="">Chọn viên chức</option>
                                                <option 
                                                    v-for="vc in vien_chucs" 
                                                    :key="vc.id"
                                                    :value="vc.id"
                                                >
                                                    {{ vc.name }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Nút submit -->
                            <div class="d-flex justify-content-end gap-2">
                                <Link 
                                    :href="route('tbm.dsbienban.index')"
                                    class="btn btn-secondary"
                                >
                                    Hủy
                                </Link>
                                <button 
                                    type="submit"
                                    class="btn btn-primary"
                                    :disabled="form.processing"
                                >
                                    <i class="fas fa-save"></i> Lưu thay đổi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </TBMLayout>
</template>

<style scoped>
.form-control:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

.btn-primary {
    background-color: #28a745;
    border-color: #28a745;
}

.btn-primary:hover {
    background-color: #218838;
    border-color: #1e7e34;
}

.btn-secondary {
    background-color: #6c757d;
    border-color: #6c757d;
}

.btn-secondary:hover {
    background-color: #5a6268;
    border-color: #545b62;
}

.btn-success {
    background-color: #28a745;
    border-color: #28a745;
}

.btn-success:hover {
    background-color: #218838;
    border-color: #1e7e34;
}

.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
}

.btn-danger:hover {
    background-color: #c82333;
    border-color: #bd2130;
}

.gap-2 {
    gap: 0.5rem !important;
}
</style> 