<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link, useForm } from "@inertiajs/vue3";
import { ref, computed, watch } from 'vue';

const props = defineProps({
    bien_ban: {
        type: Object,
        required: true
    },
    gio_quy_doi_phan_bien: {
        type: Object,
        required: true
    },
    gio_quy_doi_bien_soan: {
        type: Object,
        required: true
    }
});

const loai_ngan_hang = computed(() => {
    return props.bien_ban.ct_d_s_dang_ky.loai_ngan_hang === 1 ? 'câu' : 'đề';
});

// Refs cho dropdown selection
const selectedGioQuyDoiBienSoanId = ref(null);
const selectedGioQuyDoiPhanBienId = ref(null);
const tongSoGioError = ref('');
const tongSoGioBienSoanError = ref('');

// Form để cập nhật số giờ
const form = useForm({
    ds_g_v_bien_soans: props.bien_ban.ct_d_s_dang_ky.ds_g_v_bien_soans?.map(gv => ({
        id: gv.id,
        id_vien_chuc: gv.vien_chuc?.id,
        ten: gv.vien_chuc?.name || 'Không có tên',
        so_gio: parseFloat(gv.so_gio || 0)
    })) || [],
    ds_hop: props.bien_ban.ds_hop.map(hop => ({
        id: hop.id,
        so_gio: hop.so_gio ? parseFloat(hop.so_gio) : 0,
        vien_chuc: hop.vien_chuc,
        nhiem_vu: hop.nhiem_vu
    })),
    id_gio_quy_doi_bien_soan: null,
    id_gio_quy_doi_phan_bien: null
});

// Hàm format số giờ
const formatSoGio = (value) => {
    const num = parseFloat(value);
    return isNaN(num) ? 0 : (num % 1 === 0 ? num : parseFloat(num.toFixed(1)));
};

// Tính tổng số giờ của các giảng viên biên soạn
const tongSoGioBienSoan = computed(() => {
    return form.ds_g_v_bien_soans.reduce((total, gv) => total + (gv.so_gio || 0), 0);
});

// Xử lý khi chọn giờ quy đổi cho người biên soạn
const handleChonGioQuyDoiBienSoan = () => {
    const selectedItem = props.gio_quy_doi_bien_soan.find(
        item => item.id === selectedGioQuyDoiBienSoanId.value
    );
    
    if (selectedItem) {
        // Tính giờ: (số lượng câu / số lượng câu quy đổi) * giờ quy đổi
        const soLuong = props.bien_ban.ct_d_s_dang_ky.so_luong || 0;
        const gioQuyDoi = selectedItem.gio || 0;
        const soLuongCauQuyDoi = selectedItem.so_luong || 1;
        
        const soGio = (soLuong / soLuongCauQuyDoi) * gioQuyDoi;
        const gioMoiGiangVien = formatSoGio(soGio / form.ds_g_v_bien_soans.length);
        
        // Cập nhật số giờ cho mỗi giảng viên
        form.ds_g_v_bien_soans.forEach(gv => {
            gv.so_gio = gioMoiGiangVien;
        });
        
        form.id_gio_quy_doi_bien_soan = selectedItem.id;
    }
};

// Xử lý khi chọn giờ quy đổi cho người phản biện
const handleChonGioQuyDoiPhanBien = () => {
    const selectedItem = props.gio_quy_doi_phan_bien.find(
        item => item.id === selectedGioQuyDoiPhanBienId.value
    );
    
    if (selectedItem) {
        form.id_gio_quy_doi_phan_bien = selectedItem.id;
    }
};

// Tính tổng số giờ của người phản biện
const tongSoGioPhanBien = computed(() => {
    return form.ds_hop.reduce((total, hop) => total + (hop.so_gio || 0), 0);
});

// Xử lý submit form
const submit = () => {
    tongSoGioError.value = '';
    tongSoGioBienSoanError.value = '';
    
    // Kiểm tra giờ biên soạn nếu đã chọn giờ quy đổi biên soạn
    if (form.id_gio_quy_doi_bien_soan) {
        const selectedItem = props.gio_quy_doi_bien_soan.find(
            item => item.id === form.id_gio_quy_doi_bien_soan
        );
        
        if (selectedItem) {
            const soLuong = props.bien_ban.ct_d_s_dang_ky.so_luong || 0;
            const gioQuyDoi = selectedItem.gio || 0;
            const soLuongCauQuyDoi = selectedItem.so_luong || 1;
            
            const tongSoGioDuKien = formatSoGio((soLuong / soLuongCauQuyDoi) * gioQuyDoi);
            
            if (Math.abs(tongSoGioBienSoan.value - tongSoGioDuKien) > 0.1) {
                tongSoGioBienSoanError.value = `Tổng số giờ biên soạn (${tongSoGioBienSoan.value}) khác với số giờ quy định (${tongSoGioDuKien})`;
                return;
            }
        }
    }
    
    // Kiểm tra giờ phản biện nếu đã chọn giờ quy đổi phản biện
    if (form.id_gio_quy_doi_phan_bien) {
        const selectedItem = props.gio_quy_doi_phan_bien.find(
            item => item.id === form.id_gio_quy_doi_phan_bien
        );
        
        if (selectedItem) {
            const soLuong = props.bien_ban.ct_d_s_dang_ky.so_luong || 0;
            const gioQuyDoi = selectedItem.gio || 0;
            const soLuongCauQuyDoi = selectedItem.so_luong || 1;
            
            const tongSoGioDuKien = formatSoGio((soLuong / soLuongCauQuyDoi) * gioQuyDoi);
            
            if (Math.abs(tongSoGioPhanBien.value - tongSoGioDuKien) > 0.1) {
                tongSoGioError.value = `Tổng số giờ phản biện (${tongSoGioPhanBien.value}) khác với số giờ quy định (${tongSoGioDuKien})`;
                return;
            }
        }
    }
    
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
    <AppLayout role="tbm">
        <template v-slot:sub-link>
            <li class="breadcrumb-item">
                <Link :href="route('tbm.dsbienban.index')">Danh sách biên bản họp</Link>
            </li>
            <li class="breadcrumb-item active">Thêm giờ</li>
        </template>

        <template v-slot:content>
            <div class="content">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">THÊM GIỜ</h3>
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
                                    {{ (bien_ban.ct_d_s_dang_ky.ds_g_v_bien_soans || []).map(gv => gv?.vien_chuc?.name || 'Không có tên').join(', ') || 'Chưa có giảng viên' }}
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
                            <!-- Số giờ người biên soạn -->
                            <div class="mb-3">
                                <div class="mb-3">
                                    <label class="form-label">Chọn giờ quy đổi cho người biên soạn <span class="text-danger">*</span></label>
                                    <select 
                                        class="form-select"
                                        v-model="selectedGioQuyDoiBienSoanId"
                                        @change="handleChonGioQuyDoiBienSoan"
                                    >
                                        <option value="">Chọn giờ quy đổi</option>
                                        <option 
                                            v-for="gqd in gio_quy_doi_bien_soan" 
                                            :key="gqd.id" 
                                            :value="gqd.id"
                                        >
                                            {{ gqd.gio }} giờ / {{ gqd.so_luong }} {{ loai_ngan_hang }}
                                        </option>
                                    </select>
                                </div>
                                
                                <div v-if="tongSoGioBienSoanError" class="alert alert-danger">
                                    {{ tongSoGioBienSoanError }}
                                </div>
                                
                                <div class="d-flex justify-content-between mb-2">
                                    <h5>Danh sách giảng viên biên soạn</h5>
                                    <div>Tổng số giờ: <strong>{{ tongSoGioBienSoan }}</strong></div>
                                </div>
                                
                                <div 
                                    v-for="(giangVien, index) in form.ds_g_v_bien_soans" 
                                    :key="index"
                                    class="border rounded p-3 mb-2"
                                >
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>{{ giangVien.ten }}</strong>
                                            <br>
                                            <small class="text-muted">Người biên soạn</small>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Số giờ <span class="text-danger">*</span></label>
                                            <input 
                                                type="number"
                                                v-model="giangVien.so_gio"
                                                class="form-control"
                                                :class="{ 'is-invalid': form.errors[`ds_g_v_bien_soans.${index}.so_gio`] }"
                                                min="0"
                                                step="0.5"
                                                required
                                                @input="giangVien.so_gio = formatSoGio($event.target.value)"
                                            >
                                            <div class="invalid-feedback" v-if="form.errors[`ds_g_v_bien_soans.${index}.so_gio`]">
                                                {{ form.errors[`ds_g_v_bien_soans.${index}.so_gio`] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Danh sách người tham gia -->
                            <div class="mb-3">
                                <div class="mb-3">
                                    <label class="form-label">Chọn giờ quy đổi cho người phản biện <span class="text-danger">*</span></label>
                                    <select 
                                        class="form-select"
                                        v-model="selectedGioQuyDoiPhanBienId"
                                        @change="handleChonGioQuyDoiPhanBien"
                                    >
                                        <option value="">Chọn giờ quy đổi</option>
                                        <option 
                                            v-for="gqd in gio_quy_doi_phan_bien" 
                                            :key="gqd.id" 
                                            :value="gqd.id"
                                        >
                                            {{ gqd.gio }} giờ / {{ gqd.so_luong }} {{ loai_ngan_hang }}
                                        </option>
                                    </select>
                                </div>
                                
                                <div v-if="tongSoGioError" class="alert alert-danger">
                                    {{ tongSoGioError }}
                                </div>
                                
                                <div class="d-flex justify-content-between mb-2">
                                    <h5>Danh sách người tham gia phản biện</h5>
                                    <div>Tổng số giờ: <strong>{{ tongSoGioPhanBien }}</strong></div>
                                </div>
                                
                                <div 
                                    v-for="(thanhVien, index) in form.ds_hop" 
                                    :key="thanhVien.id"
                                    class="border rounded p-3 mb-2"
                                >
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>{{ thanhVien.vien_chuc?.name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ thanhVien.nhiem_vu?.ten }}</small>
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
    </AppLayout>
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