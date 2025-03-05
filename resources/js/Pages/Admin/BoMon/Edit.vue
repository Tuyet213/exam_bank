<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
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
    <AdminLayout>
        <!-- Breadcrumb -->
        <template v-slot:sub-link>
            <li class="breadcrumb-item"><a :href="route('admin.bomon.index')">Bộ môn</a></li>
            <li class="breadcrumb-item active">Edit</li>
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
                                    <label for="ten" class="form-label-tb">Bộ môn</label>
                                    <small v-if="form.errors.ten" class="text-danger">{{ form.errors.ten }}</small>
                                </div>
                                <div class="mb-3 form-floating form-group" :class="{ 'has-value': form.id_khoa }" >
                                    <select v-model="form.id_khoa" id="id_khoa" class="form-control" :class="{ 'has-value': form.id_khoa }" required>
                                        
                                        <option v-for="khoa in khoas" :key="khoa.id" :value="khoa.id">{{ khoa.ten }}</option>
                                    </select>
                                    <label for="id_khoa" class="form-label-tb">Khoa</label>
                                    <small v-if="form.errors.id_khoa" class="text-danger">{{ form.errors.id_khoa }}</small>
                                </div>
                              
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-success font-weight-bold">UPDATE</button>
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
