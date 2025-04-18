<script setup>
import TKLayout from "@/Layouts/TKLayout.vue";
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
    vien_chucs_dbcl: {
        type: Array,
        required: true
    },
    nhiem_vus: {
        type: Array,
        required: true
    }
});

// Định dạng lại thời gian từ dữ liệu ban đầu
const formatDateTimeForInput = (dateTimeString) => {
    if (!dateTimeString) return null;
    
    // Chuyển đổi chuỗi thời gian thành đối tượng Date
    const date = new Date(dateTimeString);
    
    // Định dạng thành YYYY-MM-DDTHH:MM (định dạng cho input datetime-local)
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    
    return `${year}-${month}-${day}T${hours}:${minutes}`;
};

// Tìm ID của từng nhiệm vụ từ danh sách được truyền xuống
const chuTichId = computed(() => {
    return props.nhiem_vus.find(nv => nv.ten === 'Chủ tịch')?.id;
});

const thuKyId = computed(() => {
    return props.nhiem_vus.find(nv => nv.ten === 'Thư ký')?.id;
});

const uyVienId = computed(() => {
    return props.nhiem_vus.find(nv => nv.ten === 'Ủy viên')?.id;
});

// Khởi tạo form với dữ liệu có sẵn từ biên bản
const form = useForm({
    thoi_gian: formatDateTimeForInput(props.bien_ban.thoi_gian),
    dia_diem: props.bien_ban.dia_diem,
    ds_hop: props.bien_ban.ds_hop.map(member => ({
        id: member.id,
        id_vien_chuc: member.id_vien_chuc,
        id_nhiem_vu: member.id_nhiem_vu,
        from: member.vien_chuc?.id_bo_mon === 'dbcl' ? 'dbcl' : 'khoa'
    }))
});

// Submit form
const submit = () => {
    // Kiểm tra dữ liệu trước khi submit
    const hasEmptyFields = !form.dia_diem || !form.thoi_gian;
    const hasEmptyMembers = form.ds_hop.some(member => !member.id_vien_chuc);
    
    if (hasEmptyFields || hasEmptyMembers) {
        alert('Vui lòng điền đầy đủ thông tin cho biên bản!');
        return;
    }
    
    console.log('Submitting form data:', form);

    form.put(route('tk.dsbienban.update', props.bien_ban.id), {
        preserveScroll: true,
        onSuccess: () => {
            alert('Cập nhật biên bản họp thành công!');
            window.location = route('tk.dsbienban.index');
        },
        onError: (errors) => {
            console.error('Lỗi khi cập nhật biên bản:', errors);
            alert('Có lỗi xảy ra khi cập nhật biên bản họp');
        }
    });
};
</script>

<template>
    <TKLayout>
        <template v-slot:sub-link>
            <li class="breadcrumb-item">
                <Link :href="route('tk.dsbienban.index')">Danh sách biên bản họp</Link>
            </li>
            <li class="breadcrumb-item active">Chỉnh sửa biên bản họp</li>
        </template>

        <template v-slot:content>
            <div class="content">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">CHỈNH SỬA BIÊN BẢN HỌP</h3>
                        <button 
                            class="btn btn-primary"
                            @click="submit"
                        >
                            Lưu thay đổi
                        </button>
                    </div>

                    <div class="card-body">
                        <div class="border rounded p-4 mb-4">
                            <!-- Thông tin đăng ký -->
                            <div class="mb-3">
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

                            <!-- Bảng chi tiết đăng ký -->
                            <div class="mb-3" v-if="bien_ban.ds_dang_ky.ct_d_s_dang_kies && bien_ban.ds_dang_ky.ct_d_s_dang_kies.length > 0">
                                <h6 class="mb-2">Danh sách đăng ký biên soạn:</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-center" style="width: 5%">STT</th>
                                                <th style="width: 25%">Học phần</th>
                                                <th style="width: 20%">Giảng viên</th>
                                                <th class="text-center" style="width: 10%">Số lượng</th>
                                                <th style="width: 20%">Loại ngân hàng</th>
                                                <th style="width: 20%">Hình thức thi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(ct, ctIndex) in bien_ban.ds_dang_ky.ct_d_s_dang_kies" :key="ctIndex">
                                                <td class="text-center">{{ ctIndex + 1 }}</td>
                                                <td>{{ ct.hoc_phan?.ten }}</td>
                                                <td>{{ ct.vien_chuc?.name }}</td>
                                                <td class="text-center">{{ ct.so_luong }}</td>
                                                <td>{{ ct.loai_ngan_hang===1?'Ngân hàng câu hỏi':'Ngân hàng đề thi' }}</td>
                                                <td>{{ ct.hinh_thuc_thi }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="mb-3" v-else>
                                <p class="text-center text-muted">Không có dữ liệu chi tiết đăng ký</p>
                            </div>

                            <!-- Form nhập thông tin biên bản -->
                            <form @submit.prevent="submit">
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
                                        <h6>Danh sách người tham gia:</h6>
                                    </div>

                                    <div 
                                        v-for="(thanhVien, index) in form.ds_hop" 
                                        :key="index"
                                        class="border rounded p-3 mb-2"
                                    >
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>
                                                {{ nhiem_vus.find(nv => nv.id === thanhVien.id_nhiem_vu)?.ten }}
                                                {{ thanhVien.from === 'dbcl' ? '(P.ĐBCL)' : '' }}
                                            </span>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="form-label">Tên <span class="text-danger">*</span></label>
                                                <select 
                                                    v-model="thanhVien.id_vien_chuc"
                                                    class="form-select"
                                                    :class="{ 'is-invalid': form.errors[`ds_hop.${index}.id_vien_chuc`] }"
                                                    required
                                                    
                                                >
                                                    <option :value="null">Chọn viên chức</option>
                                                    <template v-if="thanhVien.from === 'dbcl'">
                                                        <option 
                                                            v-for="vc in vien_chucs_dbcl" 
                                                            :key="vc.id"
                                                            :value="vc.id"
                                                        >
                                                            {{ vc.name }} - {{ vc.bo_mon?.ten }}
                                                        </option>
                                                    </template>
                                                    <template v-else>
                                                        <option 
                                                            v-for="vc in vien_chucs" 
                                                            :key="vc.id"
                                                            :value="vc.id"
                                                        >
                                                            {{ vc.name }} - {{ vc.bo_mon?.ten }}
                                                        </option>
                                                    </template>
                                                </select>
                                                <div 
                                                    class="invalid-feedback" 
                                                    v-if="form.errors[`ds_hop.${index}.id_vien_chuc`]"
                                                >
                                                    {{ form.errors[`ds_hop.${index}.id_vien_chuc`] }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </TKLayout>
</template>

<style scoped>
.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.form-control:focus,
.form-select:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
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

.border {
    border-color: #dee2e6 !important;
}

.border.rounded {
    border-width: 1px;
    padding: 1rem;
    margin-bottom: 1rem;
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