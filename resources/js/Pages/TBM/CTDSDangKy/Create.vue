<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link, useForm } from "@inertiajs/vue3";

const props = defineProps({
    dsdangky: {
        type: Object,
        required: true
    },
    hocphans: {
        type: Array,
        required: true
    },
    vienchucs: {
        type: Array,
        required: true
    }
});

const form = useForm({
    id_ds_dang_ky: props.dsdangky.id,
    id_hoc_phan: '',
    id_vien_chuc: '',
    hinh_thuc_thi: '',
    so_luong: '',
    trang_thai: 'Draft'
});

const submit = () => {
    form.post(route('tbm.ctdsdangky.store'), {
        onSuccess: () => {
            alert("Thêm mới thành công!");
            form.reset();
        },
        onError: () => {
            alert("Thêm mới thất bại!");
        }
    });
};
</script>

<template>
    <AppLayout role="tbm">
        <template v-slot:sub-link>
            <li class="breadcrumb-item">
                <Link :href="route('tbm.dsdangky.index')">Danh sách đăng ký</Link>
            </li>
            <li class="breadcrumb-item">
                <Link :href="route('tbm.ctdsdangky.index', dsdangky.id)">{{ dsdangky.ten }}</Link>
            </li>
            <li class="breadcrumb-item active">Thêm mới</li>
        </template>

        <template v-slot:content>
            <div class="content">
                <div class="card">
                    <div class="card-header bg-success-tb text-white p-4 ">
                        <h3 class="mb-0">THÊM GIẢNG VIÊN BIÊN SOẠN <div
                            class="col-4      "
                        >
                            
                        </div>
                        </h3>
                    </div>

                    <div class="card-body">
                        <form @submit.prevent="submit">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Học phần <span class="text-danger">*</span></label>
                                    <select 
                                        class="form-select"
                                        v-model="form.id_hoc_phan"
                                        :class="{ 'is-invalid': form.errors.id_hoc_phan }"
                                    >
                                        <option value="">Chọn học phần</option>
                                        <option 
                                            v-for="hp in hocphans"
                                            :key="hp.id"
                                            :value="hp.id"
                                        >
                                            {{ hp.ten }}
                                        </option>
                                    </select>
                                    <div class="invalid-feedback" v-if="form.errors.id_hoc_phan">
                                        {{ form.errors.id_hoc_phan }}
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Viên chức <span class="text-danger">*</span></label>
                                    <select 
                                        class="form-select"
                                        v-model="form.id_vien_chuc"
                                        :class="{ 'is-invalid': form.errors.id_vien_chuc }"
                                    >
                                        <option value="">Chọn viên chức</option>
                                        <option 
                                            v-for="vc in vienchucs"
                                            :key="vc.id"
                                            :value="vc.id"
                                        >
                                            {{ vc.name }}
                                        </option>
                                    </select>
                                    <div class="invalid-feedback" v-if="form.errors.id_vien_chuc">
                                        {{ form.errors.id_vien_chuc }}
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Hình thức thi <span class="text-danger">*</span></label>
                                    <select 
                                        class="form-select"
                                        v-model="form.hinh_thuc_thi"
                                        :class="{ 'is-invalid': form.errors.hinh_thuc_thi }"
                                    >
                                        <option value="">Chọn hình thức thi</option>
                                        <option value="Trắc nghiệm">Trắc nghiệm</option>
                                        <option value="Tự luận">Tự luận</option>
                                        <option value="Trắc nghiệm và tự luận">Trắc nghiệm và tự luận</option>
                                    </select>
                                    <div class="invalid-feedback" v-if="form.errors.hinh_thuc_thi">
                                        {{ form.errors.hinh_thuc_thi }}
                                    </div>
                                   
                                    <div class="invalid-feedback" v-if="form.errors.hinh_thuc_thi">
                                        {{ form.errors.hinh_thuc_thi }}
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Số lượng <span class="text-danger">*</span></label>
                                    <input 
                                        type="number"
                                        class="form-control"
                                        v-model="form.so_luong"
                                        :class="{ 'is-invalid': form.errors.so_luong }"
                                    >
                                    <div class="invalid-feedback" v-if="form.errors.so_luong">
                                        {{ form.errors.so_luong }}
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                
                                <button 
                                    type="submit" 
                                    class="btn btn-success"
                                    :disabled="form.processing"
                                >
                                    Thêm mới
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

.bg-success-tb {
    background-color: #28a745;
    color: white;
}
.btn-success {
    background-color: #28a745;
    color: white;
}

.btn-success:hover {
    background-color: #218838;
    color: white;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background-color: #5a6268;
    color: white;
}

.form-label {
    font-weight: 500;
}

</style>
