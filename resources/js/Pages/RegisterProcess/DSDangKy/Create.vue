<script setup>
import TBMLayout from "@/Layouts/TBMLayout.vue";
import { useForm } from "@inertiajs/vue3";

const form = useForm({
    id: "",
    ten: "",
    id_bo_mon: "",
    thoi_gian: new Date().toISOString().substr(0, 10), // Ngày hiện tại
});

const submit = () => {
    form.post(route("tbm.dsdangky.store"), {
        onSuccess: () => {
            alert("Tạo danh sách đăng ký thành công!");
            form.reset();
        },
        onError: (errors) => {
            alert("Có lỗi xảy ra khi tạo danh sách!");
            console.error(errors);
        },
    });
};
</script>

<template>
    <TBMLayout>
        <!-- Breadcrumb -->
        <template v-slot:sub-link>
            <li class="breadcrumb-item">
                <a :href="route('tbm.dsdangky.index')">Danh sách đăng ký</a>
            </li>
            <li class="breadcrumb-item active">Thêm mới</li>
        </template>

        <!-- Nội dung chính -->
        <template v-slot:content>
            <div class="content">
                <div class="card border-radius-lg shadow-lg animated-fade-in">
                    <!-- Card Header -->
                    <div class="card-header bg-success-tb text-white p-4">
                        <h3 class="mb-0 font-weight-bolder">TẠO DANH SÁCH ĐĂNG KÝ MỚI</h3>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body p-4">
                        <form @submit.prevent="submit">
                            <div class="row">
                               
                                <div class="col-12">
                                    <label for="ten" class="form-label">Tên danh sách</label>
                                    <input 
                                        type="text" 
                                        class="form-control" 
                                        id="ten" 
                                        v-model="form.ten"
                                        required
                                    >
                                    <small v-if="form.errors.ten" class="text-danger">
                                        {{ form.errors.ten }}
                                    </small>
                                </div>

                            </div>

                            <!-- Nút Submit -->
                            <div class="text-end mt-4">
                                <button 
                                    type="submit" 
                                    class="btn btn-success font-weight-bold"
                                    :disabled="form.processing"
                                >
                                    Thêm
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
.bg-success-tb {
    background-color: #28a745;
}

.border-radius-lg {
    border-radius: 0.5rem;
}

.animated-fade-in {
    animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.form-control:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

.btn-success {
    background-color: #28a745;
    border-color: #28a745;
}

.btn-success:hover {
    background-color: #218838;
    border-color: #1e7e34;
}

.form-label {
    font-weight: 500;
    color: #495057;
}
</style>
