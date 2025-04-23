<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import {useForm } from "@inertiajs/vue3";

const { khoas, bomon } = defineProps({
    khoas: {
        type: Array,
        required: true,
    },
    bomon: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    id: bomon.id,
    ten: bomon.ten,
    id_khoa: bomon.id_khoa,
});

const submit = () => {
    form.put(route("admin.bomon.update", bomon.id), {
        onSuccess: () => {
            alert(" Cập nhật Bộ môn thành công!");
            form.reset();
        },
        onError: (errors) => {
            alert("Có lỗi xảy ra khi cập nhật Bộ môn!");
            console.error(errors);
        },
    });
};
</script>

<template>
    <AppLayout role="admin">
        <!-- Breadcrumb -->
        <template v-slot:sub-link>
            <li class="breadcrumb-item"><a :href="route('admin.bomon.index')">Bộ môn</a></li>
            <li class="breadcrumb-item active">Cập nhật</li>
        </template>

        <!-- Nội dung chính -->
        <template v-slot:content>
            <div class="content">
                <div class="card border-radius-lg shadow-lg animated-fade-in">
                    <!-- Card Header -->
                    <div class="card-header bg-success-tb text-white p-4">
                        <h3 class="mb-0 font-weight-bolder">CẬP NHẬT BỘ MÔN</h3>
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
                                <label for="ten" class="form-label">Bộ môn</label>
                                <input type="text" class="form-control" id="ten" v-model="form.ten" required>
                                <small v-if="form.errors.ten" class="text-danger">
                                    {{ form.errors.ten }}
                                </small>
                            </div>  
                            <div class="mb-3">
                                <label for="id_khoa" class="form-label">Khoa</label>
                                <select v-model="form.id_khoa" id="id_khoa" class="form-control" :class="{ 'has-value': form.id_khoa }" required>
                                    <option v-for="khoa in khoas" :key="khoa.id" :value="khoa.id">{{ khoa.ten }}</option>
                                </select>
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

