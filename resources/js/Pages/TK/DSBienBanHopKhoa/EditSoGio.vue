<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link, useForm } from "@inertiajs/vue3";
import { ref } from 'vue';

const props = defineProps({
    bien_ban: {
        type: Object,
        required: true
    }
});

// Khởi tạo form với dữ liệu có sẵn từ biên bản
const form = useForm({
    ds_hop: props.bien_ban.ds_hop.map(member => ({
        id: member.id,
        id_vien_chuc: member.id_vien_chuc,
        id_nhiem_vu: member.id_nhiem_vu,
        so_gio: member.so_gio || 0
    }))
});

// Format số giờ để đảm bảo hợp lệ
const formatSoGio = (value) => {
    value = parseFloat(value);
    if (isNaN(value) || value < 0) {
        return 0;
    }
    return value;
};

// Submit form
const submit = () => {
    form.put(route('tk.dsbienban.update-so-gio', props.bien_ban.id), {
        preserveScroll: true,
        onSuccess: () => {
            alert('Cập nhật số giờ thành công!');
        }
    });
};
</script>

<template>
    <AppLayout role="tk">
        <template v-slot:sub-link>
            <li class="breadcrumb-item">
                <Link :href="route('tk.dsbienban.index')">Danh sách biên bản họp</Link>
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
                                    <strong>Bộ môn:</strong> 
                                    {{ bien_ban.ds_dang_ky.bo_mon.ten }}
                                </div>
                                <div class="col-md-4">
                                    <strong>Học kỳ:</strong>
                                    {{ bien_ban.ds_dang_ky.hoc_ki }}
                                </div>
                                <div class="col-md-4">
                                    <strong>Năm học:</strong>
                                    {{ bien_ban.ds_dang_ky.nam_hoc }}
                                </div>
                            </div>
                        </div>

                        <form @submit.prevent="submit">
                            <h6 class="mb-3">Thành viên tham gia họp:</h6>
                            
                            <!-- Danh sách thành viên tham gia -->
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
                            <div class="text-end mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Cập nhật số giờ
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
.border {
    border-color: #dee2e6 !important;
}

.border.rounded {
    border-width: 1px;
    padding: 1rem;
    margin-bottom: 1rem;
}

.form-control:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}

.btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
}
</style> 