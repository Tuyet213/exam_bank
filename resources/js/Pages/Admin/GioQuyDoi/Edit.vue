<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm } from "@inertiajs/vue3";

const {     gioQuyDoi } = defineProps({
    gioQuyDoi: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    id: gioQuyDoi.id,
    loai_de_thi: gioQuyDoi.loai_de_thi,
    loai_hanh_dong: gioQuyDoi.loai_hanh_dong,
    gio: gioQuyDoi.gio,
    so_luong: gioQuyDoi.so_luong,
});

const submit = () => {
    form.put(route("admin.gioquydoi.update", { id: form.id }), {
        onSuccess: () => {
            alert("Cập nhật Giờ quy đổi thành công!");
            form.reset();
        },
        onError: (errors) => {
            alert("Có lỗi xảy ra khi cập nhật Giờ quy đổi!");
            console.error(errors);
        },
    });
};
</script>

<template>
    <AppLayout role="admin">
        <!-- Breadcrumb -->
        <template v-slot:sub-link>
            <li class="breadcrumb-item"><a :href="route('admin.gioquydoi.index')">Giờ quy đổi</a></li>
            <li class="breadcrumb-item active">Cập nhật</li>
        </template>

        <!-- Nội dung chính -->
        <template v-slot:content>
            <div class="content">
                <div class="card border-radius-lg shadow-lg animated-fade-in">
                    <!-- Card Header -->
                    <div class="card-header bg-success-tb text-white p-4">
                        <h3 class="mb-0 font-weight-bolder">CẬP NHẬT GIỜ QUY ĐỔI</h3>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body p-4">
                        <form @submit.prevent="submit">
                            <div class="mb-3">
                                <div class="row">
                                    <!-- <div class="mb-3 col-md-6 col-sm-12">
                                        <label for="loai_de_thi" class="form-label">Loại đề thi</label>
                                            <select class="form-control" id="loai_de_thi" v-model="form.loai_de_thi" required>
                                                <option value="0">Trắc nghiệm</option>
                                                <option value="1">Tự luận</option>
                                                <option value="2">Trắc nghiệm + Tự luận</option>
                                            </select>
                                        <small v-if="form.errors.loai_de_thi" class="text-danger">
                                            {{ form.errors.loai_de_thi }}
                                        </small>
                                    </div> -->
                                    <div class="mb-3 col-md-12 col-sm-12">
                                        <label for="loai_hanh_dong" class="form-label">Loại hành động</label>
                                        <select class="form-control" id="loai_hanh_dong" v-model="form.loai_hanh_dong" required>
                                            <option value="0">Biên soạn</option>
                                            <option value="1">Họp phản biện</option>
                                        </select>
                                        <small v-if="form.errors.loai_hanh_dong" class="text-danger">
                                            {{ form.errors.loai_hanh_dong }}
                                        </small>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-md-6 col-sm-12">
                                        <label for="gio" class="form-label">Giờ</label>
                                        <input type="number" class="form-control" id="gio" v-model="form.gio" required min="1" step="1">
                                        <small v-if="form.errors.gio" class="text-danger">
                                            {{ form.errors.gio }}
                                        </small>
                                    </div>
                                    <div class="mb-3 col-md-6 col-sm-12">
                                        <label for="so_luong" class="form-label">Số lượng</label>
                                        <input type="number" class="form-control" id="so_luong" v-model="form.so_luong" required min="1" step="1">
                                        <small v-if="form.errors.so_luong" class="text-danger">
                                            {{ form.errors.so_luong }}
                                        </small>
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-success shadow-lg font-weight-bold">Lưu</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </AppLayout>
</template>
