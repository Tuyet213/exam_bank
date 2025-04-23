<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm } from "@inertiajs/vue3";

const form = useForm({
    id: "",
    ten: "",
});

const submit = () => {
    form.post(route("admin.bacdaotao.store"), {
        onSuccess: () => {
            alert("Tạo Bậc đào tạo thành công!");
            form.reset();
        },
        onError: (errors) => {
            alert("Có lỗi xảy ra khi tạo Bậc đào tạo!");
            console.error(errors);
        },
    });
};
</script>

<template>
    <AppLayout role="admin">
        <!-- Breadcrumb -->
        <template v-slot:sub-link>
            <li class="breadcrumb-item">
                <a :href="route('admin.bacdaotao.index')">Bậc đào tạo</a>
            </li>
            <li class="breadcrumb-item active">Thêm</li>
        </template>

        <!-- Nội dung chính -->
        <template v-slot:content>
            <div class="content">
                <div class="card border-radius-lg shadow-lg animated-fade-in">
                    <!-- Card Header -->
                    <div class="card-header bg-success-tb text-white p-4">
                        <h3 class="mb-0 font-weight-bolder"> BẬC ĐÀO TẠO </h3>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body p-4">
                        <form @submit.prevent="submit">
                            <div class="mb-3">
                                <div class="mb-3">
                                    <label for="ten" class="form-label"
                                        >ID</label
                                    >
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="id"
                                        v-model="form.id"
                                        required
                                        placeholder=""
                                    />
                                    <small v-if="form.errors.id" class="text-danger">
                                        {{ form.errors.id }}
                                    </small>
                                </div>
                                <div class="mb-3">
                                    <label for="ten" class="form-label"
                                        >Tên</label
                                    >
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="ten"
                                        v-model="form.ten"
                                        required
                                        placeholder=""
                                    />
                                    <small v-if="form.errors.ten" class="text-danger">
                                        {{ form.errors.ten }}
                                    </small>
                                </div>
                            </div>
                            <!-- Nút Create -->
                            <div class="text-end">
                                <button
                                    type="submit"
                                    class="btn btn-success font-weight-bold"
                                >
                                    Lưu
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </AppLayout>
</template>

