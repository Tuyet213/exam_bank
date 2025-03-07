<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import {useForm } from "@inertiajs/vue3";

const { khoas, hoc_phans, vien_chucs } = defineProps({
    khoas: {
        type: Array,
        required: true,
    },
    hoc_phans: {
        type: Array,
        required: true,
    },
    vien_chucs: {
        type: Array,
        required: true,
    },
});
console.log(vien_chucs);

const form = useForm({
    ten: "",
    ky_hoc: "",
    nam_hoc: "",
    so_luong_sinh_vien: "",
    id_khoa: "",
    id_hoc_phan: "",
    id_vien_chuc: "",
});

const submit = () => {
        form.post(route("admin.lophocphan.store"), {
        onSuccess: () => {
            alert(" Thêm Lớp học phần thành công!");
            form.reset();
        },
        onError: (errors) => {
            alert("Có lỗi xảy ra khi thêm Lớp học phần!");
            console.error(errors);
        },
    });
};
</script>

<template>
    <AdminLayout>
        <!-- Breadcrumb -->
        <template v-slot:sub-link>
            <li class="breadcrumb-item"><a :href="route('admin.lophocphan.index')">Lớp học phần</a></li>
            <li class="breadcrumb-item active">Create</li>
        </template>

        <!-- Nội dung chính -->
        <template v-slot:content>
            <div class="content">
                <div class="card border-radius-lg shadow-lg animated-fade-in">
                    <!-- Card Header -->
                    <div class="card-header bg-success-tb text-white p-4">
                        <h3 class="mb-0 font-weight-bolder">THÊM LỚP HỌC PHẦN</h3>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body p-4">
                        <form @submit.prevent="submit">
                            <div class="mb-3">
                                <label for="ten" class="form-label">Tên lớp học phần</label>
                                <input type="text" class="form-control" id="ten" v-model="form.ten" required >
                                <small v-if="form.errors.ten" class="text-danger">
                                    {{ form.errors.ten }}
                                </small>
                            </div>
                            <div class="mb-3">
                                    <label for="so_luong_sinh_vien" class="form-label">Số lượng sinh viên</label>
                                    <input type="number" class="form-control" id="so_luong_sinh_vien" v-model="form.so_luong_sinh_vien" required min="1" step="1">
                                    <small v-if="form.errors.so_luong_sinh_vien" class="text-danger">
                                        {{ form.errors.so_luong_sinh_vien }}
                                    </small>
                            </div>

                            <div class="row">
                                <div class="mb-3 col-md-6 col-sm-12">
                                <label for="nam_hoc" class="form-label">Năm học</label>
                                    <input type="text" class="form-control" id="nam_hoc" v-model="form.nam_hoc" required>
                                    <small v-if="form.errors.nam_hoc" class="text-danger">
                                        {{ form.errors.nam_hoc }}
                                    </small>
                                </div>
                                <div class="mb-3 col-md-6 col-sm-12">
                                    <label for="ky_hoc" class="form-label">Kỳ học</label>
                                    <select v-model="form.ky_hoc" id="ky_hoc" class="form-control" :class="{ 'has-value': form.ky_hoc }" required>
                                        <option value="1">Học kỳ 1</option>
                                        <option value="2">Học kỳ 2</option>
                                        <option value="3">Học kỳ 3</option>
                                    </select>
                                    <small v-if="form.errors.ky_hoc" class="text-danger">
                                        {{ form.errors.ky_hoc }}
                                    </small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="mb-3 col-md-6 col-sm-12">
                                    <label for="id_khoa" class="form-label">Khoa</label>
                                    <select v-model="form.id_khoa" id="id_khoa" class="form-control" :class="{ 'has-value': form.id_khoa }" required>
                                        <option v-for="khoa in khoas" :key="khoa.id" :value="khoa.id">{{ khoa.ten }}</option>
                                    </select>
                                    <small v-if="form.errors.id_khoa" class="text-danger">
                                        {{ form.errors.id_khoa }}
                                        </small>
                                </div>
                                <div class="mb-3 col-md-6 col-sm-12">
                                    <label for="id_hoc_phan" class="form-label">Học phần</label>
                                    <select v-model="form.id_hoc_phan" id="id_hoc_phan" class="form-control" :class="{ 'has-value': form.id_hoc_phan }" required>
                                        <option v-for="hoc_phan in hoc_phans" :key="hoc_phan.id" :value="hoc_phan.id">{{ hoc_phan.ten }}</option>
                                    </select>
                                    <small v-if="form.errors.id_hoc_phan" class="text-danger">
                                        {{ form.errors.id_hoc_phan }}
                                    </small>
                                </div>
                                
                            </div>
                            <div class="mb-3 ">
                                    <label for="id_vien_chuc" class="form-label">Giảng viên</label>
                                    <select v-model="form.id_vien_chuc" id="id_vien_chuc" class="form-control" :class="{ 'has-value': form.id_vien_chuc }" required>
                                        <option v-for="vien_chuc in vien_chucs" :key="vien_chuc.id" :value="vien_chuc.id">{{ vien_chuc.name }}</option>
                                    </select>
                                    <small v-if="form.errors.id_vien_chuc" class="text-danger">
                                        {{ form.errors.id_vien_chuc }}
                                    </small>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-success font-weight-bold">ADD</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </AdminLayout>
</template>

