<script setup>
import TBMLayout from "@/Layouts/TBMLayout.vue";
import { Link, useForm } from "@inertiajs/vue3";

const props = defineProps({
    bien_ban: {
        type: Object,
        required: true
    }
});

// Form để cập nhật số giờ
const form = useForm({
    so_gio_bien_soan: props.bien_ban.ct_d_s_dang_ky.so_gio ? parseFloat(props.bien_ban.ct_d_s_dang_ky.so_gio) : 0,
    ds_hop: props.bien_ban.ds_hop.map(hop => ({
        id: hop.id,
        so_gio: hop.so_gio ? parseFloat(hop.so_gio) : 0
    }))
});

// Hàm format số giờ
const formatSoGio = (value) => {
    const num = parseFloat(value);
    // Nếu số không có phần thập phân (số nguyên), hiển thị nguyên số
// Nếu có phần thập phân, chỉ hiển thị 1 số sau dấu phẩy
    return num % 1 === 0 ? num.toString() : num.toFixed(1);
};

console.log(props.bien_ban);
// Xử lý submit form
const submit = () => {
    form.put(route('tbm.dsbienban.update-so-gio', props.bien_ban.id), {
        preserveScroll: true,
        onSuccess: () => {
            alert('Cập nhật số giờ thành công');
        },
        onError: (errors) => {
            console.error('Lỗi:', errors);
            alert('Có lỗi xảy ra khi cập nhật số giờ');
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
            <li class="breadcrumb-item active">Chỉnh sửa số giờ</li>
        </template>

        <template v-slot:content>
            <div class="content">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">CHỈNH SỬA SỐ GIỜ</h3>
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
                            </div>
                        </div>

                        <form @submit.prevent="submit">
                            <!-- Số giờ người biên soạn -->
                            <div class="mb-3">
                                
                                <div class="border rounded p-3 mb-2">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>{{ bien_ban.ct_d_s_dang_ky.vien_chuc.name }}</strong>
                                            <br>
                                            <small class="text-muted">Người biên soạn</small>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Số giờ <span class="text-danger">*</span></label>
                                            <input 
                                                type="number"
                                                v-model="form.so_gio_bien_soan"
                                                class="form-control"
                                                :class="{ 'is-invalid': form.errors.so_gio_bien_soan }"
                                                min="0"
                                                step="0.5"
                                                required
                                                @input="form.so_gio_bien_soan = formatSoGio($event.target.value)"
                                            >
                                            <div class="invalid-feedback" v-if="form.errors.so_gio_bien_soan">
                                                {{ form.errors.so_gio_bien_soan }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Danh sách người tham gia -->
                            <div class="mb-3">
                                
                                <div 
                                    v-for="(thanhVien, index) in form.ds_hop" 
                                    :key="thanhVien.id"
                                    class="border rounded p-3 mb-2"
                                >
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>{{ bien_ban.ds_hop[index].vien_chuc?.name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ bien_ban.ds_hop[index].nhiem_vu?.ten }}</small>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Số giờ <span class="text-danger">*</span></label>
                                            <input 
                                                type="number"
                                                v-model="thanhVien.so_gio"
                                                class="form-control"
                                                :class="{ 'is-invalid': form.errors[`ds_hop.${index}.so_gio`] }"
                                                min="0"
                                                step="0.5"
                                                required
                                                @input="thanhVien.so_gio = formatSoGio($event.target.value)"
                                            >
                                            <div class="invalid-feedback" v-if="form.errors[`ds_hop.${index}.so_gio`]">
                                                {{ form.errors[`ds_hop.${index}.so_gio`] }}
                                            </div>
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

.gap-2 {
    gap: 0.5rem !important;
}
</style> 