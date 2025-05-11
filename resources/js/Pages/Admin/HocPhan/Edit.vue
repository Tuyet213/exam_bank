<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import {useForm } from "@inertiajs/vue3";
import { ref, computed, onMounted } from 'vue';

const { hocphan, bacdaotaos, bomons, khoas } = defineProps({
    hocphan: {
        type: Object,
        required: true,
    },
    bacdaotaos: {
        type: Array,
        required: true,
    },
    bomons: {
        type: Array,
        required: true,
    },
    khoas: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    id: hocphan.id,
    ten: hocphan.ten,
    id_bac_dao_tao: hocphan.bac_dao_tao?.id || '',
    id_khoa: hocphan.bo_mon?.khoa?.id || '',
    id_bo_mon: hocphan.bo_mon?.id || '',
    so_tin_chi: hocphan.so_tin_chi,
    chuan_dau_ras: hocphan.chuan_dau_ras?.map(cdr => ({
        id: cdr.id,
        ten: cdr.ten,
        noi_dung: cdr.noi_dung
    })) || [],
    chuongs: hocphan.chuongs?.map(chuong => {
        console.log('Chương:', chuong);
        return {
            id: chuong.id,
            ten: chuong.ten,
            chuan_dau_ras: chuong.chuong_chuan_dau_ra?.map(ccdr => {
                console.log('Liên kết chương-CDR:', ccdr);
                return ccdr.chuan_dau_ra.noi_dung;
            }) || []
        };
    }) || []
});

onMounted(() => {
    console.log('Dữ liệu học phần:', hocphan);
    console.log('Form data:', form);
});

const newChuanDauRa = ref({
    ten: "",
    noi_dung: ""
});

const newChuong = ref({
    ten: "",
    chuan_dau_ras: []
});

const selectedChuanDauRa = ref("");

// Lọc bộ môn theo khoa
const filteredBomons = computed(() => {
    if (!form.id_khoa) return [];
    return bomons.filter(bomon => bomon.id_khoa === form.id_khoa);
});

const addChuanDauRa = () => {
    if (newChuanDauRa.value.ten && newChuanDauRa.value.noi_dung) {
        form.chuan_dau_ras.push({...newChuanDauRa.value});
        newChuanDauRa.value = { ten: "", noi_dung: "" };
    }
};

const removeChuanDauRa = (index) => {
    form.chuan_dau_ras.splice(index, 1);
};

const addChuong = () => {
    form.chuongs.push({
        ten: "",
        chuan_dau_ras: []
    });
};

const removeChuong = (index) => {
    form.chuongs.splice(index, 1);
};

const removeChuanDauRaFromChuong = (chuongIndex, noi_dung) => {
    const cdrIndexInArray = form.chuongs[chuongIndex].chuan_dau_ras.indexOf(noi_dung);
    if (cdrIndexInArray > -1) {
        form.chuongs[chuongIndex].chuan_dau_ras.splice(cdrIndexInArray, 1);
    }
};

const addChuanDauRaToChuong = (chuongIndex) => {
    if (selectedChuanDauRa.value && !form.chuongs[chuongIndex].chuan_dau_ras.includes(selectedChuanDauRa.value)) {
        form.chuongs[chuongIndex].chuan_dau_ras.push(selectedChuanDauRa.value);
        selectedChuanDauRa.value = "";
    }
};

const submit = () => {
    form.put(route("admin.hocphan.update", hocphan.id), {
        onSuccess: () => {
            alert("Cập nhật Học phần thành công!");
        },
        onError: (errors) => {
            alert("Có lỗi xảy ra khi cập nhật Học phần!");
            console.error(errors);
        },
    });
};
</script>

<template>
    <AppLayout role="admin">
        <!-- Breadcrumb -->
        <template v-slot:sub-link>
            <li class="breadcrumb-item"><a :href="route('admin.hocphan.index')">Học phần</a></li>
            <li class="breadcrumb-item active">Chỉnh sửa</li>
        </template>

        <!-- Nội dung chính -->
        <template v-slot:content>
            <div class="content">
                <div class="card border-radius-lg shadow-lg animated-fade-in">
                    <!-- Card Header -->
                    <div class="card-header bg-success-tb text-white p-4">
                        <h3 class="mb-0 font-weight-bolder">CHỈNH SỬA HỌC PHẦN</h3>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body p-4">
                        <form @submit.prevent="submit">
                            <div class="mb-3">
                                <label for="id" class="form-label">ID</label>
                                <input type="text" class="form-control" id="id" v-model="form.id" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="ten" class="form-label">Học phần</label>
                                <input type="text" class="form-control" id="ten" v-model="form.ten" required>
                                <small v-if="form.errors.ten" class="text-danger">
                                    {{ form.errors.ten }}
                                </small>
                            </div>  
                            <div class="mb-3">
                                <label for="so_tin_chi" class="form-label">Số tín chỉ</label>
                                <input type="number" class="form-control" id="so_tin_chi" v-model="form.so_tin_chi" required min="1" step="1">
                                <small v-if="form.errors.so_tin_chi" class="text-danger">
                                    {{ form.errors.so_tin_chi }}
                                </small>
                            </div>

                            <div class="row">
                                <div class="mb-3 col-md-4 col-sm-12">
                                    <label for="id_bac_dao_tao" class="form-label">Bậc đào tạo</label>
                                    <select v-model="form.id_bac_dao_tao" id="id_bac_dao_tao" class="form-control" :class="{ 'has-value': form.id_bac_dao_tao }" required>
                                        <option v-for="bacdaotao in bacdaotaos" :key="bacdaotao.id" :value="bacdaotao.id">{{ bacdaotao.ten }}</option>
                                    </select>
                                    <small v-if="form.errors.id_bac_dao_tao" class="text-danger">
                                        {{ form.errors.id_bac_dao_tao }}
                                    </small>
                                </div>
                                <div class="mb-3 col-md-4 col-sm-12">
                                    <label for="id_khoa" class="form-label">Khoa</label>
                                    <select v-model="form.id_khoa" id="id_khoa" class="form-control" :class="{ 'has-value': form.id_khoa }" required>
                                        <option v-for="khoa in khoas" :key="khoa.id" :value="khoa.id">{{ khoa.ten }}</option>
                                    </select>
                                    <small v-if="form.errors.id_khoa" class="text-danger">
                                        {{ form.errors.id_khoa }}
                                    </small>
                                </div>
                                <div class="mb-3 col-md-4 col-sm-12">
                                    <label for="id_bo_mon" class="form-label">Bộ môn</label>
                                    <select v-model="form.id_bo_mon" id="id_bo_mon" class="form-control" :class="{ 'has-value': form.id_bo_mon }" required>
                                        <option v-for="bomon in filteredBomons" :key="bomon.id" :value="bomon.id">{{ bomon.ten }}</option>
                                    </select>
                                    <small v-if="form.errors.id_bo_mon" class="text-danger">
                                        {{ form.errors.id_bo_mon }}
                                    </small>
                                </div>
                            </div>

                            <!-- Quản lý chuẩn đầu ra -->
                            <div class="mb-4">
                                <h4 class="mb-3">Chuẩn đầu ra</h4>
                                <div class="row mb-3">
                                    <div class="col-md-5">
                                        <input type="text" class="form-control" v-model="newChuanDauRa.ten" placeholder="Tên chuẩn đầu ra">
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" class="form-control" v-model="newChuanDauRa.noi_dung" placeholder="Nội dung">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-primary w-100" @click="addChuanDauRa">Thêm</button>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Tên chuẩn đầu ra</th>
                                                <th>Nội dung</th>
                                                <th>Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(cdr, index) in form.chuan_dau_ras" :key="index">
                                                <td>
                                                    <input type="text" class="form-control" v-model="cdr.ten">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" v-model="cdr.noi_dung">
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger btn-sm" @click="removeChuanDauRa(index)">Xóa</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Quản lý chương -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h4 class="mb-0">Chương</h4>
                                    <button type="button" class="btn btn-primary" @click="addChuong">Thêm chương</button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th width="30%">Tên chương</th>
                                                <th>Chuẩn đầu ra</th>
                                                <th width="10%">Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(chuong, chuongIndex) in form.chuongs" :key="chuongIndex">
                                                <td>
                                                    <input type="text" class="form-control" v-model="chuong.ten" placeholder="Nhập tên chương">
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-wrap gap-1">
                                                        <div v-for="noi_dung in chuong.chuan_dau_ras" :key="noi_dung" 
                                                            class="badge bg-primary d-flex align-items-center">
                                                            {{ form.chuan_dau_ras.find(cdr => cdr.noi_dung == noi_dung)?.ten }}
                                                            <button type="button" class="btn btn-link text-white p-0 ms-2"
                                                                @click="removeChuanDauRaFromChuong(chuongIndex, noi_dung)">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="mt-2">
                                                        <select class="form-control" v-model="selectedChuanDauRa" @change="addChuanDauRaToChuong(chuongIndex)">
                                                            <option value="">Chọn chuẩn đầu ra</option>
                                                            <option v-for="(cdr, index) in form.chuan_dau_ras" 
                                                                :key="index" 
                                                                :value="cdr.noi_dung"
                                                                :disabled="chuong.chuan_dau_ras.includes(cdr.noi_dung)">
                                                                {{ cdr.ten }}
                                                            </option>
                                                        </select>
                                                    </div>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger btn-sm" @click="removeChuong(chuongIndex)">Xóa</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-success font-weight-bold">Lưu</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </AppLayout>
</template>

