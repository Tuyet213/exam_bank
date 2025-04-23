<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import {useForm } from "@inertiajs/vue3";

const { bacdaotaos, bomons, hocphan } = defineProps({
    bacdaotaos: {
        type: Array,
        required: true,
    },
    bomons: {
        type: Array,
        required: true,
    },
    hocphan: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    id: hocphan.id,
    ten: hocphan.ten,
    id_bac_dao_tao: hocphan.id_bac_dao_tao,
    id_bo_mon: hocphan.id_bo_mon,
    so_tin_chi: hocphan.so_tin_chi
});

const submit = () => {
        form.put(route("admin.hocphan.update", hocphan.id), {
        onSuccess: () => {
            alert(" Cập nhật Học phần thành công!");
            form.reset();
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
            <li class="breadcrumb-item active">Cập nhật</li>
        </template>

        <!-- Nội dung chính -->
        <template v-slot:content>
            <div class="content">
                <div class="card border-radius-lg shadow-lg animated-fade-in">
                    <!-- Card Header -->
                    <div class="card-header bg-success-tb text-white p-4">
                        <h3 class="mb-0 font-weight-bolder">CẬP NHẬT HỌC PHẦN</h3>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body p-4">
                        <form @submit.prevent="submit">
                            <div class="mb-3">
                                <label for="id" class="form-label">ID</label>
                                <input type="text" class="form-control" id="id" v-model="form.id" required disabled>
                                <small v-if="form.errors.id" class="text-danger">
                                    {{ form.errors.id }}
                                </small>
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
                                <div class="mb-3 col-md-6 col-sm-12">
                                    <label for="id_bac_dao_tao" class="form-label">Bậc đào tạo</label>
                                    <select v-model="form.id_bac_dao_tao" id="id_bac_dao_tao" class="form-control" :class="{ 'has-value': form.id_bac_dao_tao }" required>
                                        <option v-for="bacdaotao in bacdaotaos" :key="bacdaotao.id" :value="bacdaotao.id">{{ bacdaotao.ten }}</option>
                                    </select>
                                    <small v-if="form.errors.id_bac_dao_tao" class="text-danger">
                                        {{ form.errors.id_bac_dao_tao }}
                                        </small>
                                </div>
                                <div class="mb-3 col-md-6 col-sm-12">
                                    <label for="id_bo_mon" class="form-label">Bộ môn</label>
                                    <select v-model="form.id_bo_mon" id="id_bo_mon" class="form-control" :class="{ 'has-value': form.id_bo_mon }" required>
                                        <option v-for="bomon in bomons" :key="bomon.id" :value="bomon.id">{{ bomon.ten }}</option>
                                    </select>
                                    <small v-if="form.errors.id_bo_mon" class="text-danger">
                                        {{ form.errors.id_bo_mon }}
                                    </small>
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

