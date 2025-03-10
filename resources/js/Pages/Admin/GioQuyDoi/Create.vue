<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { useForm } from "@inertiajs/vue3";

const form = useForm({
    loai_de_thi: "",
    loai_hanh_dong: "",
    gio: "",
    so_luong: "",
});

const submit = () => {
    form.post(route("admin.gioquydoi.store"), {
        onSuccess: () => {
            alert("Tạo Giờ quy đổi thành công!");
            form.reset();
        },
        onError: (errors) => {
            alert("Có lỗi xảy ra khi tạo Giờ quy đổi!");
            console.error(errors);
        },
    });
};
</script>

<template>
    <AdminLayout>
        <!-- Breadcrumb -->
        <template v-slot:sub-link>
            <li class="breadcrumb-item">
                <a :href="route('admin.gioquydoi.index')">Giờ Quy Đổi</a>
            </li>
            <li class="breadcrumb-item active">Create</li>
        </template>

        <!-- Nội dung chính -->
        <template v-slot:content>
            <div class="content">
                <div class="card border-radius-lg shadow-lg animated-fade-in">
                    <!-- Card Header -->
                    <div class="card-header bg-success-tb text-white p-4">
                            <h3 class="mb-0 font-weight-bolder">TẠO GIỜ QUY ĐỔI MỚI</h3>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body p-4">
                        <form @submit.prevent="submit">
                            <div class="mb-3">
                                <div class="row">
                                    <div class="mb-3 col-md-6 col-sm-12">
                                        <label for="loai_de_thi" class="form-label">Loại đề thi</label>
                                            <select class="form-control" id="loai_de_thi" v-model="form.loai_de_thi" required>
                                                <option value="0">Trắc nghiệm</option>
                                                <option value="1">Tự luận</option>
                                                <option value="2">Trắc nghiệm + Tự luận</option>
                                            </select>
                                        <small v-if="form.errors.loai_de_thi" class="text-danger">
                                            {{ form.errors.loai_de_thi }}
                                        </small>
                                    </div>
                                    <div class="mb-3 col-md-6 col-sm-12">
                                        <label for="loai_hanh_dong" class="form-label">Loại hành động</label>
                                        <select class="form-control" id="loai_hanh_dong" v-model="form.loai_hanh_dong" required>
                                            <option value="0">Biên soạn</option>
                                            <option value="1">Họp phản biện cấp Bộ môn</option>
                                            <option value="2">Họp thẩm định cấp Khoa</option>
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
                                <button
                                    type="submit"
                                    class="btn btn-success font-weight-bold"
                                >
                                    ADD
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </AdminLayout>
</template>
<style scoped>
.form-label-tb {
    position: absolute;
    top: 40%;
    transition: all 0.5s ease;
    pointer-events: none;
    color: #6c757d;
    font-size: 1rem;
}
.form-control:focus + .form-label-tb,
.form-control.has-value + .form-label-tb {
    top: 0;
    transform: translateY(-30%);
    font-size: 0.875rem;
    color: #5eb562;
}

.form-control {
    padding-top: 1.5rem;
    padding-bottom: 0.5rem;
    border: none;
    border-bottom: 1px solid #d1d1d1;
    border-radius: 0;
    font-size: 1rem;
    width: 100%;
}

.form-control:focus {
    box-shadow: none;
    border-bottom: 1px solid #5eb562;
}
</style>
