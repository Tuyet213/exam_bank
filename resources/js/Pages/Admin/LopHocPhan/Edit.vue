<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import {useForm } from "@inertiajs/vue3";
import { computed } from 'vue';

const { khoas, hoc_phans, vien_chucs, lophocphan } = defineProps({
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
    lophocphan: {
        type: Object,
        required: true,
    },
});
console.log(vien_chucs);

// Tạo mảng năm học theo định dạng "năm - năm+1"
const schoolYears = computed(() => {
    const currentYear = new Date().getFullYear();
    return Array.from({ length: 10 }, (_, i) => {
        const startYear = currentYear - i;
        const endYear = startYear + 1;
        return {
            value: `${startYear}-${endYear}`,
            label: `${startYear} - ${endYear}`
        };
    });
});

const form = useForm({
    ten: lophocphan.ten,
    ky_hoc: lophocphan.ky_hoc,
    nam_hoc: lophocphan.nam_hoc,
    id_khoa: lophocphan.id_khoa,
    id_hoc_phan: lophocphan.id_hoc_phan,
    id_vien_chuc: lophocphan.id_vien_chuc,
});

const submit = () => {
        form.put(route("admin.lophocphan.update", lophocphan.id), {
        onSuccess: () => {
            alert(" Cập nhật Lớp học phần thành công!");
            form.reset();
        },
        onError: (errors) => {
                alert("Có lỗi xảy ra khi cập nhật Lớp học phần!");
            console.error(errors);
        },
    });
};
</script>

<template>
    <AppLayout role="admin">
        <!-- Breadcrumb -->
        <template v-slot:sub-link>
            <li class="breadcrumb-item"><a :href="route('admin.lophocphan.index')">Lớp học phần</a></li>
            <li class="breadcrumb-item active">Cập nhật</li>
        </template>

        <!-- Nội dung chính -->
        <template v-slot:content>
            <div class="content">
                <div class="card border-radius-lg shadow-lg animated-fade-in">
                    <!-- Card Header -->
                    <div class="card-header bg-success-tb text-white p-4">
                        <h3 class="mb-0 font-weight-bolder">CẬP NHẬT LỚP HỌC PHẦN</h3>
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

                            <div class="row">
                                <div class="mb-3 col-md-6 col-sm-12">
                                <label for="nam_hoc" class="form-label">Năm học</label>
                                    <select v-model="form.nam_hoc" id="nam_hoc" class="form-select" required>
                                        <option v-for="year in schoolYears" :key="year.value" :value="year.value">
                                            {{ year.label }}
                                        </option>
                                    </select>
                                    <small v-if="form.errors.nam_hoc" class="text-danger">
                                        {{ form.errors.nam_hoc }}
                                    </small>
                                </div>
                                <div class="mb-3 col-md-6 col-sm-12">
                                    <label for="ky_hoc" class="form-label">Kỳ học</label>
                                    <select v-model="form.ky_hoc" id="ky_hoc" class="form-select" :class="{ 'has-value': form.ky_hoc }" required>
                                        <option value="1">Học kỳ 1</option>
                                        <option value="2">Học kỳ 2</option>
                                        <option value="Hè">Học kỳ Hè</option>
                                    </select>
                                    <small v-if="form.errors.ky_hoc" class="text-danger">
                                        {{ form.errors.ky_hoc }}
                                    </small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="mb-3 col-md-6 col-sm-12">
                                    <label for="id_khoa" class="form-label">Khoa</label>
                                    <select v-model="form.id_khoa" id="id_khoa" class="form-select" :class="{ 'has-value': form.id_khoa }" required>
                                        <option v-for="khoa in khoas" :key="khoa.id" :value="khoa.id">{{ khoa.ten }}</option>
                                    </select>
                                    <small v-if="form.errors.id_khoa" class="text-danger">
                                        {{ form.errors.id_khoa }}
                                        </small>
                                </div>
                                <div class="mb-3 col-md-6 col-sm-12">
                                    <label for="id_hoc_phan" class="form-label">Học phần</label>
                                    <select v-model="form.id_hoc_phan" id="id_hoc_phan" class="form-select" :class="{ 'has-value': form.id_hoc_phan }" required>
                                        <option v-for="hoc_phan in hoc_phans" :key="hoc_phan.id" :value="hoc_phan.id">{{ hoc_phan.ten }}</option>
                                    </select>
                                    <small v-if="form.errors.id_hoc_phan" class="text-danger">
                                        {{ form.errors.id_hoc_phan }}
                                    </small>
                                </div>
                                
                            </div>
                            <div class="mb-3 ">
                                    <label for="id_vien_chuc" class="form-label">Giảng viên</label>
                                    <select v-model="form.id_vien_chuc" id="id_vien_chuc" class="form-select" :class="{ 'has-value': form.id_vien_chuc }" required>
                                        <option v-for="vien_chuc in vien_chucs" :key="vien_chuc.id" :value="vien_chuc.id">{{ vien_chuc.name }}</option>
                                    </select>
                                    <small v-if="form.errors.id_vien_chuc" class="text-danger">
                                        {{ form.errors.id_vien_chuc }}
                                    </small>
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

