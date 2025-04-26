<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link, useForm } from "@inertiajs/vue3";
import { ref } from "vue";

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

// Chuẩn bị dữ liệu viên chức, đảm bảo là mảng
const initialVienChucs = Array.isArray(props.ctdsdangky.id_vien_chucs) ? 
    props.ctdsdangky.id_vien_chucs : 
    (props.ctdsdangky.id_vien_chuc ? [props.ctdsdangky.id_vien_chuc] : []);

const form = useForm({
    id_hoc_phan: props.ctdsdangky.id_hoc_phan,
    id_vien_chuc: initialVienChucs,
    trang_thai: props.ctdsdangky.trang_thai,
    hinh_thuc_thi: props.ctdsdangky.hinh_thuc_thi,
    so_luong: props.ctdsdangky.so_luong,
    loai_ngan_hang: props.ctdsdangky.loai_ngan_hang
});

// Thêm một viên chức vào danh sách
const addVienChuc = (vienChucId) => {
    if (!form.id_vien_chuc.includes(vienChucId) && vienChucId) {
        form.id_vien_chuc.push(vienChucId);
    }
};

// Xóa một viên chức khỏi danh sách
const removeVienChuc = (vienChucId) => {
    const index = form.id_vien_chuc.indexOf(vienChucId);
    if (index !== -1) {
        form.id_vien_chuc.splice(index, 1);
    }
};

// Tìm thông tin viên chức từ ID
const getVienChucInfo = (vienChucId) => {
    const vienChuc = props.vienchucs.find(vc => vc.id === vienChucId);
    return vienChuc ? vienChuc : { id: vienChucId, name: 'Unknown' };
};

console.log(props.ctdsdangky);
console.log(form);

const submit = () => {
    form.put(route('tbm.ctdsdangky.update', props.ctdsdangky.id), {
        onSuccess: () => {
            alert("Cập nhật thành công!");
            // window.location.href = route('tbm.ctdsdangky.index', props.ctdsdangky.id_ds_dang_ky);
        },
        onError: (errors) => {
            alert("Có lỗi xảy ra khi cập nhật!");
            console.error(errors);
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
                                    <label class="form-label">Giảng viên biên soạn <span class="text-danger">*</span></label>
                                    <div class="selected-vien-chuc-list mb-2">
                                        <div v-if="form.id_vien_chuc.length === 0" class="text-muted">
                                            Chưa chọn viên chức nào
                                        </div>
                                        <div v-else class="selected-items">
                                            <div v-for="vcId in form.id_vien_chuc" :key="vcId" class="selected-item">
                                                <span>{{ getVienChucInfo(vcId).name }}</span>
                                                <button type="button" class="btn-remove" @click="removeVienChuc(vcId)">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="select-wrapper">
                                        <select 
                                            class="form-select"
                                            :class="{ 'is-invalid': form.errors.id_vien_chuc }"
                                            @change="addVienChuc($event.target.value); $event.target.value = ''"
                                        >
                                            <option value="">Chọn viên chức</option>
                                            <option 
                                                v-for="vc in vienchucs"
                                                :key="vc.id"
                                                :value="vc.id"
                                                :disabled="form.id_vien_chuc.includes(vc.id)"
                                            >
                                                {{ vc.name }}
                                            </option>
                                        </select>
                                        <div class="invalid-feedback" v-if="form.errors.id_vien_chuc">
                                            {{ form.errors.id_vien_chuc }}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Loại ngân hàng <span class="text-danger">*</span></label>
                                    <select 
                                        class="form-select"
                                        v-model="form.loai_ngan_hang"
                                        :class="{ 'is-invalid': form.errors.loai_ngan_hang }"
                                    >
                                        <option value="1">Ngân hàng câu hỏi</option>
                                        <option value="0">Ngân hàng đề thi</option>
                                    </select>
                                    <div class="invalid-feedback" v-if="form.errors.loai_ngan_hang">
                                        {{ form.errors.loai_ngan_hang }}
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
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Số lượng</label>
                                    <input 
                                        type="number"
                                        class="form-control"
                                        v-model="form.so_luong"
                                    >
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
    </AppLayout>
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

.selected-vien-chuc-list {
    max-height: 150px;
    overflow-y: auto;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 8px;
    background-color: #f9f9f9;
}

.selected-items {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.selected-item {
    display: flex;
    align-items: center;
    background-color: #e9ecef;
    border-radius: 4px;
    padding: 4px 8px;
    font-size: 14px;
}

.btn-remove {
    background: none;
    border: none;
    color: #dc3545;
    margin-left: 8px;
    padding: 0;
    cursor: pointer;
    font-size: 12px;
}

.btn-remove:hover {
    color: #bd2130;
}
</style>
