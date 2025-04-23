<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link, useForm } from "@inertiajs/vue3";
import { ref, computed } from 'vue';

const props = defineProps({
    ds_dang_kies: {
        type: Array,
        required: true
    },
    vien_chucs: {
        type: Array,
        required: true
    },
    nhiem_vus: {
        type: Array,
        required: true
    },
    vien_chucs_dbcl: {
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

const uyVienId = computed(() => {
    return props.nhiem_vus.find(nv => nv.ten === 'Ủy viên')?.id;
});

// Tạo form cho mỗi DSDangKy
const forms = ref(props.ds_dang_kies.map(dk => useForm({
    id_ds_dang_ky: dk.id,
    dia_diem: '',
    thoi_gian: null,
    ds_hop: [
        { id_vien_chuc: '', id_nhiem_vu: chuTichId.value, from: 'khoa' },
        { id_vien_chuc: '', id_nhiem_vu: thuKyId.value, from: 'khoa' },
        { id_vien_chuc: '', id_nhiem_vu: uyVienId.value, from: 'khoa' },
        { id_vien_chuc: '', id_nhiem_vu: uyVienId.value, from: 'khoa' },
        { id_vien_chuc: '', id_nhiem_vu: uyVienId.value, from: 'dbcl' }
    ]
})));

// Xử lý khi chọn viên chức
const handleVienChucChange = (form, index, value) => {
    console.log('Changing viên chức:', {
        formIndex: index,
        oldValue: form.ds_hop[index].id_vien_chuc,
        newValue: value,
        valueType: typeof value
    });
    // Chuyển đổi value thành số nguyên
    form.ds_hop[index].id_vien_chuc = parseInt(value) || '';
};

// Submit tất cả các form
const submitAll = () => {
    let results = [];
    
    // Kiểm tra dữ liệu trước khi submit
    const invalidForms = forms.value.filter(form => {
        const hasEmptyFields = !form.dia_diem || !form.thoi_gian;
        const hasEmptyMembers = form.ds_hop.some(member => !member.id_vien_chuc);
        return hasEmptyFields || hasEmptyMembers;
    });

    if (invalidForms.length > 0) {
        alert('Vui lòng điền đầy đủ thông tin cho tất cả các biên bản!');
        return;
    }

    const promises = forms.value.map(form => 
        form.post(route('tk.dsbienban.store'), {
            preserveScroll: true,
            onSuccess: (page) => {
                console.log('Response từ server:', page);
                if (page.props?.flash?.type === 'success') {
                    results.push({
                        status: 'success',
                        id: form.id_ds_dang_ky,
                        message: page.props.flash.message
                    });
                } else {
                    results.push({
                        status: 'error',
                        id: form.id_ds_dang_ky,
                        message: page.props.flash.message || 'Có lỗi xảy ra'
                    });
                }
                alert('Tạo biên bản họp thành công!');
                window.location = route('tk.dsdangky.index');
            },
            onError: (errors) => {
                console.error('Lỗi khi tạo biên bản cho CT:', form.id_ds_dang_ky, errors);
                results.push({
                    status: 'error',
                    id: form.id_ds_dang_ky,
                    message: Object.values(errors).flat().join(', ')
                });
                alert('Hãy điền tất cả thông tin!');
            }
        })
    );

    Promise.allSettled(promises)
        .then(() => {
            const failedResults = results.filter(r => r.status === 'error');
            if (failedResults.length > 0) {
                const messages = failedResults.map(r => `ID ${r.id}: ${r.message}`).join('\n');
                alert(`Có ${failedResults.length} biên bản tạo thất bại:\n${messages}`);
            } else if (results.length === forms.value.length) {
                alert('Tạo tất cả biên bản thành công!');
                window.location = route('tk.dsbienban.index');
            }
        });
};
</script>

<template>
    <AppLayout role="tk">
        <template v-slot:sub-link>
            <li class="breadcrumb-item">
                <Link :href="route('tk.dsdangky.index')">Danh sách đăng ký</Link>
            </li>
            <li class="breadcrumb-item active">Tạo biên bản họp</li>
        </template>

        <template v-slot:content>
            <div class="content">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">TẠO BIÊN BẢN HỌP</h3>
                        <button 
                            class="btn btn-primary"
                            @click="submitAll"
                        >
                            Lưu tất cả
                        </button>
                    </div>

                    <div class="card-body">
                        <!-- Một form riêng cho mỗi DSDangKy -->
                        <div 
                            v-for="(form, formIndex) in forms" 
                            :key="formIndex"
                            class="border rounded p-4 mb-4"
                        >
                            <!-- Thông tin đăng ký -->
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Bộ môn:</strong> 
                                        {{ ds_dang_kies[formIndex].bo_mon.ten }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Học kỳ:</strong>
                                        {{ ds_dang_kies[formIndex].hoc_ki }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Năm học:</strong>
                                        {{ ds_dang_kies[formIndex].nam_hoc }}
                                    </div>
                                </div>
                            </div>

                            <!-- Bảng chi tiết đăng ký -->
                            <div class="mb-3">
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
                                            <tr v-for="(ct, ctIndex) in ds_dang_kies[formIndex].ct_d_s_dang_kies" :key="ctIndex">
                                                <td class="text-center">{{ ctIndex + 1 }}</td>
                                                <td>{{ ct.hoc_phan?.ten }}</td>
                                                <td>{{ ct.vien_chuc?.name }}</td>
                                                <td class="text-center">{{ ct.so_luong }}</td>
                                                <td>{{ ct.loai_ngan_hang===1?'Ngân hàng câu hỏi':'Ngân hàng đề thi' }}</td>
                                                <td>{{ ct.hinh_thuc_thi }}</td>
                                            </tr>
                                            <tr v-if="ds_dang_kies[formIndex].ct_d_s_dang_kies.length === 0">
                                                <td colspan="6" class="text-center">Không có dữ liệu</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Form nhập thông tin biên bản -->
                            <form @submit.prevent>
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
                                                    <option value="">Chọn viên chức</option>
                                                    <template v-if="thanhVien.from === 'dbcl'">
                                                        <option 
                                                            v-for="vc in vien_chucs_dbcl" 
                                                            :key="vc.id"
                                                            :value="vc.id"
                                                        >
                                                            {{ vc.name }} - {{ vc.bo_mon.ten }}
                                                        </option>
                                                    </template>
                                                    <template v-else>
                                                        <option 
                                                            v-for="vc in vien_chucs" 
                                                            :key="vc.id"
                                                            :value="vc.id"
                                                        >
                                                            {{ vc.name }} - {{ vc.bo_mon.ten }}
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
    </AppLayout>
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