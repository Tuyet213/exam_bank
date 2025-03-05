<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { useForm } from "@inertiajs/vue3";

const { khoa } = defineProps({
    khoa: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    id: khoa.id,
    ten: khoa.ten,
});

const submit = () => {
    form.put(route("admin.khoa.update", { id: form.id }), {
        onSuccess: () => {
            alert("Cập nhật Khoa thành công!");
            form.reset();
        },
        onError: (errors) => {
            alert("Có lỗi xảy ra khi cập nhật Khoa!");
            console.error(errors);
        },
    });
};
</script>

<template>
    <AdminLayout>
        <!-- Breadcrumb -->
        <template v-slot:sub-link>
            <li class="breadcrumb-item"><a :href="route('admin.khoa.index')">Khoa</a></li>
            <li class="breadcrumb-item active">Update</li>
        </template>

        <!-- Nội dung chính -->
        <template v-slot:content>
            <div class="content">
                <div class="card border-radius-lg shadow-lg animated-fade-in">
                    <!-- Card Header -->
                    <div class="card-header bg-success-tb text-white p-4">
                        <h3 class="mb-0 font-weight-bolder">CẬP NHẬT KHOA</h3>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body p-4">
                        <form @submit.prevent="submit">
                            <div class="mb-3">
                                <div class="mb-3 form-floating">
                                    <input
                                        v-model="form.id"
                                        type="text"
                                        id="id"
                                        class="form-control"
                                        :class="{ 'has-value': form.id }"
                                        required
                                        disabled
                                    />
                                    <label for="id" class="form-label-tb">ID</label>
                                    <small v-if="form.errors.id" class="text-danger">{{ form.errors.id }}</small>
                                </div>
                                <div class="mb-3 form-floating">
                                    <input
                                        v-model="form.ten"
                                        type="text"
                                        id="ten"
                                        class="form-control"
                                        :class="{ 'has-value': form.ten }"
                                        required
                                    />
                                    <label for="ten" class="form-label-tb">Khoa</label>
                                    <small v-if="form.errors.ten" class="text-danger">{{ form.errors.ten }}</small>
                                </div>
                              
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-success shadow-lg font-weight-bold">UPDATE</button>
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
