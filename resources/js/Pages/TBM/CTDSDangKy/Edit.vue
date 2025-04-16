<script setup>
import TBMLayout from "@/Layouts/TBMLayout.vue";
import { Link, useForm } from "@inertiajs/vue3";

const props = defineProps({
    ctdsdangky: {
        type: Object,
        required: true
    },
    dsdangky: {
        type: Object,
        required: true
    },
    nam_hoc: {
        type: String,
        required: true
    },
    hocphans: {
        type: Array,
        required: true
    },
    vienchucs: {
        type: Array,
        required: true
    },
    message: {
        type: String,
        default: ""
    },
    success: {
        type: Boolean,
        default: undefined
    }
});

const form = useForm({
    id_hoc_phan: props.ctdsdangky.id_hoc_phan,
    id_vien_chuc: props.ctdsdangky.id_vien_chuc,
    trang_thai: props.ctdsdangky.trang_thai
});

 console.log(props.ctdsdangky);
 console.log(form);
const submit = () => {
    form.put(route('tbm.ctdsdangky.update', props.ctdsdangky.id), {
        onSuccess: () => {
            alert("Cập nhật phân công thành công!");
            // window.location.href = route('tbm.ctdsdangky.index', props.ctdsdangky.id_ds_dang_ky);
        },
        onError: (errors) => {
            alert("Có lỗi xảy ra khi cập nhật phân công!");
            console.error(errors);
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
            <li class="breadcrumb-item">
                <Link :href="route('tbm.ctdsdangky.index', dsdangky.id)">Học kì {{ dsdangky.hoc_ki }}-{{ nam_hoc }}</Link>
            </li>
            <li class="breadcrumb-item active">Cập nhật</li>
        </template>

        <template v-slot:content>
            <div class="content">
                <!-- Thông báo thành công/thất bại -->
                <div v-if="message" class="alert" :class="{ 'alert-success': success, 'alert-danger': !success }" role="alert">
                    {{ message }}
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">CẬP NHẬT PHÂN CÔNG</h3>
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
                                    <label class="form-label">Trạng thái</label>
                                    <input 
                                        type="text"
                                        class="form-control"
                                        v-model="form.trang_thai"
                                        disabled
                                    >
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <Link
                                    :href="route('tbm.ctdsdangky.index', ctdsdangky.id_ds_dang_ky)"
                                    class="btn btn-secondary"
                                >
                                    Quay lại
                                </Link>
                                <button 
                                    type="submit" 
                                    class="btn btn-success"
                                    :disabled="form.processing"
                                >
                                    Cập nhật
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

.card-header {
    background-color: #f8f9fa;
}
</style>
