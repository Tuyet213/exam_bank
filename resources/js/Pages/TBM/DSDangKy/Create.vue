<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link, useForm } from "@inertiajs/vue3";

const props = defineProps({
   
    message: {
        type: String,
        default: "",
    },
    success: {
        type: Boolean,
        default: undefined,
    },
    bo_mon: {
        type: String,
        default: "",
    },
    hoc_phans: {
        type: Array,
        default: () => [],
    },
    vien_chucs: {
        type: Array,
        default: () => [],
    },
    ds_nam_hoc: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    hoc_ki: '',
    nam_hoc: '',
    chi_tiet: []
});

const addChiTiet = () => {
    form.chi_tiet.push({
        id_hoc_phan: '',
        id_vien_chuc: [],
        loai_ngan_hang: '',
        so_luong: 0,
        hinh_thuc_thi: ''
    });
};

const removeChiTiet = (index) => {
    form.chi_tiet.splice(index, 1);
};

// Thêm một viên chức vào danh sách
const addVienChuc = (chiTietIndex, vienChucId) => {
    // Nếu không tìm thấy viên chức trong danh sách và giá trị không rỗng
    if (!form.chi_tiet[chiTietIndex].id_vien_chuc.includes(vienChucId) && vienChucId) {
        form.chi_tiet[chiTietIndex].id_vien_chuc.push(vienChucId);
    }
};

// Xóa một viên chức khỏi danh sách
const removeVienChuc = (chiTietIndex, vienChucId) => {
    const index = form.chi_tiet[chiTietIndex].id_vien_chuc.indexOf(vienChucId);
    if (index !== -1) {
        form.chi_tiet[chiTietIndex].id_vien_chuc.splice(index, 1);
    }
};

// Tìm thông tin viên chức từ ID
const getVienChucInfo = (vienChucId) => {
    const vienChuc = props.vien_chucs.find(vc => vc.id === vienChucId);
    return vienChuc ? vienChuc : { id: vienChucId, name: 'Unknown' };
};

const handleSubmit = () => {
    form.post(route('tbm.dsdangky.store'), {
        onSuccess: () => {
            alert('Tạo danh sách đăng ký thành công!');
            form.reset();
            window.location.href = route('tbm.dsdangky.index');
        },
        onError: (errors) => {
            alert('Có lỗi xảy ra khi tạo danh sách đăng ký!');
            console.error(errors);
        }
    });
};
</script>

<template>
    <AppLayout role="tbm">
        <template v-slot:sub-link>
            <li class="breadcrumb-item active">
                <a :href="route('tbm.dsdangky.index')">Danh sách đăng ký</a>
            </li>
        </template>

        <template v-slot:content>
            <div class="content">
                <!-- Form tạo danh sách đăng ký -->
                <div class="card mb-4">
                    <div class="card-header bg-success-tb text-white p-4 ">
                        <h3 class="mb-0">TẠO DANH SÁCH ĐĂNG KÝ (Bộ Môn {{ bo_mon }})</h3>
                    </div>
                    <div class="card-body">
                        <form @submit.prevent="handleSubmit">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="hoc_ki" class="form-label">Học kỳ</label>
                                    <select 
                                        id="hoc_ki" 
                                        class="form-select" 
                                        v-model="form.hoc_ki"
                                        required
                                    >
                                        <option value="">Chọn học kỳ</option>
                                        <option value="1">Học kỳ 1</option>
                                        <option value="2">Học kỳ 2</option>
                                        <option value="Hè">Học kỳ Hè</option>
                                    </select>
                                    <div v-if="form.errors.hoc_ki" class="text-danger">{{ form.errors.hoc_ki }}</div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="nam_hoc" class="form-label">Năm học</label>
                                    <select 
                                        id="nam_hoc" 
                                        class="form-select" 
                                        v-model="form.nam_hoc"
                                        required
                                    >
                                        <option value="">Chọn năm học</option>
                                        <option v-for="nam_hoc in ds_nam_hoc" :key="nam_hoc" :value="nam_hoc">
                                            {{ nam_hoc }}
                                        </option>
                                    </select>
                                    <div v-if="form.errors.nam_hoc" class="text-danger">{{ form.errors.nam_hoc }}</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5>Chi tiết danh sách</h5>
                                    <button type="button" class="btn btn-success" @click="addChiTiet">
                                        <i class="fas fa-plus"></i> Thêm học phần
                                    </button>
                                </div>
                                
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Học phần</th>
                                                <th>Viên chức</th>
                                                <th>Loại ngân hàng</th>
                                                <th>Số lượng</th>
                                                <th>Hình thức thi</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-if="form.chi_tiet.length === 0">
                                                <td colspan="6" class="text-center">Chưa có chi tiết nào</td>
                                            </tr>
                                            <tr v-for="(ct, index) in form.chi_tiet" :key="index">
                                                <td>
                                                    <select 
                                                        class="form-select" 
                                                        v-model="ct.id_hoc_phan"
                                                        required
                                                    >
                                                        <option value="">Chọn học phần</option>
                                                        <option v-for="hp in hoc_phans" :key="hp.id" :value="hp.id">
                                                            {{ hp.id }} - {{ hp.ten }}
                                                        </option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <div class="selected-vien-chuc-list mb-2">
                                                        <div v-if="ct.id_vien_chuc.length === 0" class="text-muted">
                                                            Chưa chọn viên chức nào
                                                        </div>
                                                        <div v-else class="selected-items">
                                                            <div v-for="vcId in ct.id_vien_chuc" :key="vcId" class="selected-item">
                                                                <span>{{ getVienChucInfo(vcId).id }} - {{ getVienChucInfo(vcId).name }}</span>
                                                                <button type="button" class="btn-remove" @click="removeVienChuc(index, vcId)">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="select-wrapper">
                                                        <select 
                                                            class="form-select" 
                                                            @change="addVienChuc(index, $event.target.value); $event.target.value = ''"
                                                        >
                                                            <option value="">Chọn viên chức</option>
                                                            <option v-for="vc in vien_chucs" :key="vc.id" :value="vc.id"
                                                                :disabled="ct.id_vien_chuc.includes(vc.id)">
                                                                {{ vc.id }} - {{ vc.name }}
                                                            </option>
                                                        </select>
                                                        <div v-if="form.errors['chi_tiet.' + index + '.id_vien_chuc']" class="text-danger">
                                                            {{ form.errors['chi_tiet.' + index + '.id_vien_chuc'] }}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <select 
                                                        class="form-select" 
                                                        v-model="ct.loai_ngan_hang"
                                                        required
                                                    >
                                                        <option value="">Chọn loại</option>
                                                        <option value="1">Ngân hàng câu hỏi</option>
                                                        <option value="0">Ngân hàng đề thi</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input 
                                                        type="number" 
                                                        class="form-control" 
                                                        v-model="ct.so_luong"
                                                        min="0"
                                                        required
                                                    />
                                                </td>
                                                <td>
                                                    <select 
                                                        class="form-select" 
                                                        v-model="ct.hinh_thuc_thi"
                                                        required
                                                    >
                                                        <option value="">Chọn hình thức</option>
                                                        <option value="Trắc nghiệm">Trắc nghiệm</option>
                                                        <option value="Tự luận">Tự luận</option>
                                                        <!-- <option value="Trắc nghiệm và tự luận">Trắc nghiệm và tự luận</option> -->
                                                    </select>
                                                </td>
                                                <td>
                                                    <button 
                                                        type="button" 
                                                        class="btn btn-danger btn-sm"
                                                        @click="removeChiTiet(index)"
                                                    >
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary" :disabled="form.processing">
                                    <i class="fas fa-save"></i> Lưu danh sách
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
.btn-success-add {
    background-color: #28a745;
    color: white;
}

.btn-success-add:hover {
    background-color: #218838;
    color: white;
}

.btn-success-edit {
    background-color: #17a2b8;
    color: white;
}

.btn-success-edit:hover {
    background-color: #138496;
    color: white;
}

.table th {
    background-color: #f8f9fa;
    color: #495057;
}



.form-control:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

.btn-info {
    background-color: #17a2b8;
    color: white;
}

.btn-info:hover {
    background-color: #138496;
    color: white;
}

.btn-primary {
    background-color: #007bff;
    color: white;
}

.btn-primary:hover {
    background-color: #0056b3;
    color: white;
}

/* Thêm tooltip styles */
[title] {
    position: relative;
    cursor: pointer;
}

[title]:hover::after {
    content: attr(title);
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    padding: 4px 8px;
    background-color: rgba(0, 0, 0, 0.8);
    color: white;
    border-radius: 4px;
    font-size: 12px;
    white-space: nowrap;
    z-index: 1000;
}

.me-2 {
    margin-right: 0.5rem !important;
}

/* Style cho danh sách viên chức đã chọn */
.selected-vien-chuc-list {
    border: 1px solid #ced4da;
    border-radius: 4px;
    padding: 8px;
    min-height: 60px;
    max-height: 150px;
    overflow-y: auto;
    background-color: #f8f9fa;
}

.selected-items {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.selected-item {
    display: flex;
    align-items: center;
    background-color: #e7f3ff;
    border: 1px solid #b8daff;
    border-radius: 4px;
    padding: 4px 8px;
    font-size: 14px;
}

.btn-remove {
    background: none;
    border: none;
    color: #dc3545;
    margin-left: 6px;
    cursor: pointer;
    padding: 0;
    font-size: 12px;
}

.btn-remove:hover {
    color: #bd2130;
}
</style>