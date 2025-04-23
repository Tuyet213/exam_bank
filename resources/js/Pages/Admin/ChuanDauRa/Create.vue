<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm } from "@inertiajs/vue3";

const form = useForm({
    ten: "",
    noi_dung: "",
});

const submit = () => {
    form.post(route("admin.chuandaura.store"), {
        onSuccess: () => {
            alert("Tạo Chuẩn đầu ra thành công!");
            form.reset();
        },
        onError: (errors) => {
            alert("Có lỗi xảy ra khi tạo Chuẩn đầu ra!");
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
                <a :href="route('admin.chuandaura.index')">Chuẩn đầu ra</a>
            </li>
            <li class="breadcrumb-item active">Thêm</li>
        </template>

        <!-- Nội dung chính -->
        <template v-slot:content>
            <div class="content">
                <div class="card border-radius-lg shadow-lg animated-fade-in">
                    <!-- Card Header -->
                    <div class="card-header bg-success-tb text-white p-4">
                        <h3 class="mb-0 font-weight-bolder">CHUẨN ĐẦU RA</h3>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body p-4">
                        <form @submit.prevent="submit">
                            <div class="mb-3">
                                <!-- <div class="mb-3">
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
                                </div> -->
                                <div class="mb-3">
                                    <label for="ten" class="form-label"
                                        >Chuẩn đầu ra</label
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
                                <div class="mb-3">
                                    <label for="noi_dung" class="form-label"
                                        >Nội dung</label
                                    >
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="noi_dung"
                                        v-model="form.noi_dung"
                                        required
                                        placeholder=""  
                                    />
                                    <small v-if="form.errors.noi_dung" class="text-danger">
                                        {{ form.errors.noi_dung }}
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
