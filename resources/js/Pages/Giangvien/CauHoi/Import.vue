<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm } from "@inertiajs/vue3";
import { ref, computed } from 'vue';

const props = defineProps({
    khoas: {
        type: Array,
        required: true
    },
    bo_mons: {
        type: Array,
        required: true
    },
    hoc_phans: {
        type: Array,
        required: true
    },
    ct_ds_dang_kies: {
        type: Array,
        required: true
    }
});

const form = useForm({
    file: null,
    loai: '',
    id_ct_ds_dang_ky: ''
});

const submit = () => {
    form.post(route('giangvien.cauhoi.import'), {
        onSuccess: () => {
            alert('Import câu hỏi thành công!');
            form.reset();
        },
    });
};
</script>

<template>
    <AppLayout role="gv">
        <template v-slot:sub-link>
            <li class="breadcrumb-item"><a :href="route('giangvien.cauhoi.import')">Câu hỏi</a></li>
            <li class="breadcrumb-item active">Import</li>
        </template>

        <template v-slot:content>
            <div class="content">
                <div class="card border-radius-lg shadow-lg animated-fade-in">
                    <div class="card-header bg-success-tb text-white p-4">
                        <h3 class="mb-0 font-weight-bolder">IMPORT CÂU HỎI</h3>
                    </div>

                    <div class="card-body p-4">
                        <form @submit.prevent="submit">
                            <div class="mb-3">
                                <label class="form-label">File Word (.docx)</label>
                                <input type="file" 
                                    class="form-control" 
                                    @input="form.file = $event.target.files[0]"
                                    accept=".docx"
                                    required>
                                <small v-if="form.errors.file" class="text-danger">
                                    {{ form.errors.file }}
                                </small>
                            </div>
<!-- --------------------------------------------------------------------------------------------------------- -->
                            <div class="mb-3">
                                <label class="form-label">Loại câu hỏi</label>
                                <select v-model="form.loai" class="form-control" required>
                                    <option value="">Chọn loại câu hỏi</option>
                                    <option value="0">Tự luận/Vấn đáp</option>
                                    <option value="1">Trắc nghiệm</option>
                                </select>
                                <small v-if="form.errors.loai" class="text-danger">
                                    {{ form.errors.loai }}
                                </small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Danh sách đăng ký</label>
                                <select v-model="form.id_ct_ds_dang_ky" class="form-control" required>
                                    <option value="">Chọn danh sách đăng ký</option>
                                    <option v-for="ct in ct_ds_dang_kies" :key="ct.id" :value="ct.id">
                                        {{ ct.ten }}
                                    </option>
                                </select>
                                <small v-if="form.errors.id_ct_ds_dang_ky" class="text-danger">
                                    {{ form.errors.id_ct_ds_dang_ky }}
                                </small>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-success" :disabled="form.processing">
                                    Import
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </AppLayout>
</template> 