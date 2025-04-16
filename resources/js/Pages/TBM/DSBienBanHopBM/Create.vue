<script setup>
import TBMLayout from "@/Layouts/TBMLayout.vue";
import { Link, useForm } from "@inertiajs/vue3";
import { ref, computed } from 'vue';

const props = defineProps({
    ct_ds_dang_kies: {
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

// Tạo form cho mỗi CTDSDangKy
const forms = ref(props.ct_ds_dang_kies.map(ct => useForm({
    id_ct_ds_dang_ky: ct.id,
    dia_diem: '',
    thoi_gian: null,
    ds_hop: [
        { id_vien_chuc: '', id_nhiem_vu: chuTichId.value },
        { id_vien_chuc: '', id_nhiem_vu: thuKyId.value },
        { id_vien_chuc: '', id_nhiem_vu: phanBienId.value },
        { id_vien_chuc: '', id_nhiem_vu: phanBienId.value }
    ]
})));

// Thêm người tham gia họp cho một form cụ thể
const addThanhVien = (form) => {
    form.ds_hop.push({
        id_vien_chuc: '',
        id_nhiem_vu: '',
        so_gio: 0
    });
};

// Xóa người tham gia họp của một form cụ thể
const removeThanhVien = (form, index) => {
    form.ds_hop.splice(index, 1);
};

// Submit tất cả các form
const submitAll = () => {
    let results = [];
    
    const promises = forms.value.map(form => 
        form.post(route('tbm.dsbienban.store'), {
            preserveScroll: true,
            onSuccess: (page) => {
                console.log('Response từ server:', page);
                if (page.props?.flash?.type === 'success') {
                    results.push({
                        status: 'success',
                        id: form.id_ct_ds_dang_ky,
                        message: page.props.flash.message
                    });
                } else {
                    results.push({
                        status: 'error',
                        id: form.id_ct_ds_dang_ky,
                        message: page.props.flash.message || 'Có lỗi xảy ra'
                    });
                }
                alert('Tạo biên bản họp thành công!');
                window.location = route('tbm.dsdangky.index');
            },
            onError: (errors) => {
                console.error('Lỗi khi tạo biên bản cho CT:', form.id_ct_ds_dang_ky, errors);
                results.push({
                    status: 'error',
                    id: form.id_ct_ds_dang_ky,
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
                window.location = route('tbm.dsbienban.index');
            }
        });
};
</script>

<template>
    <TBMLayout>
        <template v-slot:sub-link>
            <li class="breadcrumb-item">
                <Link :href="route('tbm.dsdangky.index')">Danh sách đăng ký</Link>
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
                        <!-- Một form riêng cho mỗi CTDSDangKy -->
                        <div 
                            v-for="(form, formIndex) in forms" 
                            :key="formIndex"
                            class="border rounded p-4 mb-4"
                        >
                            <!-- <h5 class="mb-3">Chi tiết #{{ formIndex + 1 }}</h5> -->
                            
                            <!-- Thông tin chi tiết đăng ký -->
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Học phần:</strong> 
                                        {{ ct_ds_dang_kies[formIndex].hoc_phan.ten }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Viên chức:</strong>
                                        {{ ct_ds_dang_kies[formIndex].vien_chuc.name }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Loại ngân hàng:</strong>
                                        {{ ct_ds_dang_kies[formIndex].loai_ngan_hang == 1 ? 'Ngân hàng câu hỏi' : 'Ngân hàng đề thi' }}
                                    </div>
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
                                        <!-- <button 
                                            type="button"
                                            class="btn btn-success btn-sm"
                                            @click="addThanhVien(form)"
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
                            </form>
                        </div>

                        
                    </div>
                </div>
            </div>
        </template>
    </TBMLayout>
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