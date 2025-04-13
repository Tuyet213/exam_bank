<script setup>
import TBMLayout from "@/Layouts/TBMLayout.vue";
import { Link, useForm } from "@inertiajs/vue3";

const props = defineProps({
    dsdangky: {
        type: Object,
        required: true
    },
    message: {
        type: String,
        default: ""
    },
    success: {
        type: Boolean,
        default: undefined
    }
});

const form = useForm({
    hoc_ki: props.dsdangky.hoc_ki,
    thoi_gian: props.dsdangky.thoi_gian
});

const submit = () => {
    form.put(route('tbm.dsdangky.update', props.dsdangky.id), {
        onSuccess: () => {
            alert('Cập nhật danh sách đăng ký thành công!');
            window.location.href = route('tbm.dsdangky.index');
        },
        onError: (errors) => {
            alert('Có lỗi xảy ra khi cập nhật danh sách!');
            console.error(errors);
        }
    });
};
</script>

<template>
    <TBMLayout>
        <!-- Breadcrumb -->
        <template v-slot:sub-link>
            <li class="breadcrumb-item">
                <Link :href="route('tbm.dsdangky.index')">Danh sách đăng ký</Link>
            </li>
            <li class="breadcrumb-item active">Cập nhật</li>
        </template>

        <!-- Nội dung chính -->
        <template v-slot:content>
            <div class="content">
                <div class="card border-radius-lg shadow-lg animated-fade-in">
                    <!-- Card Header -->
                    <div class="card-header bg-success-tb text-white p-4">
                        <h3 class="mb-0 font-weight-bolder">CẬP NHẬT DANH SÁCH ĐĂNG KÝ</h3>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body p-4">
                        <form @submit.prevent="submit">
                            <div class="row">
                                <div class="col-12">
                                    <label for="ten" class="form-label">Học kì</label>
                                    <select 
                                        id="hoc_ki" 
                                        class="form-select" 
                                        v-model="form.hoc_ki"
                                        required
                                    >   
                                        <option value="1">Học kì 1</option> 
                                        <option value="2">Học kì 2</option>
                                        <option value="Hè">Học kì Hè</option>
                                    </select>
                                    <small v-if="form.errors.hoc_ki" class="text-danger">
                                        {{ form.errors.hoc_ki }}
                                    </small>
                                </div>

                                
                            </div>

                            <!-- Buttons -->
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                
                                <button 
                                    type="submit" 
                                    class="btn btn-success"
                                    :disabled="form.processing"
                                >
                                    Cập nhật
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

.btn-secondary {
    background-color: #6c757d;
    border-color: #6c757d;
}

.btn-secondary:hover {
    background-color: #5a6268;
    border-color: #545b62;
}

.form-label {
    font-weight: 500;
    color: #495057;
}
</style>
