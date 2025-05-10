<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { onMounted } from 'vue';

const props = defineProps({
    ctDangKy: Object,
    role: String,
    downloadTemplateUrl: String
});

const form = useForm({
    file: null,
    id_ct_ds_dang_ky: props.ctDangKy.id
});

onMounted(() => {
    // Kiểm tra hình thức thi để hiển thị đúng loại form
    console.log('Hình thức thi: ', props.ctDangKy.hinh_thuc_thi);
});
</script>

<template>
    <AppLayout :role="role">
        <template #sub-link>
            <li class="breadcrumb-item">
                <a :href="route('cauhoi.hocphan')">Danh sách học phần</a>
            </li>
            <li class="breadcrumb-item">
                <a :href="route('cauhoi.danhsach', ctDangKy.id)">Danh sách câu hỏi</a>
            </li>
            <li class="breadcrumb-item active">
                Import câu hỏi - {{ ctDangKy.hoc_phan.ten }}
            </li>
        </template>

        <template #content>
            <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h4 class="card-title mb-0">Import Câu Hỏi</h4>
                            </div>
                            
                            <div class="p-6">
                                <div v-if="ctDangKy.hinh_thuc_thi === 0">
                                    <div class="btn-group mb-4">
                                        <a :href="route('cauhoi.download_mau_import', { type: 'trac_nghiem' })" class="btn btn-info">
                                            Tải file mẫu chuẩn
                                        </a>
                                        <a :href="downloadTemplateUrl" class="btn btn-success">
                                            Tải file mẫu tùy chỉnh
                                        </a>
                                    </div>
                                    
                                    <div class="alert alert-info">
                                        <h5 class="font-weight-bold">Hướng dẫn nhập câu hỏi trắc nghiệm:</h5>
                                        <ol class="mb-0 pl-4">
                                            <li>Mỗi câu hỏi bắt đầu với "Câu X:" hoặc "Câu hỏi X:"</li>
                                            <li>Các đáp án được đánh số A, B, C, D</li>
                                            <li>Đáp án đúng được đánh dấu bằng [+] hoặc (+) ở cuối</li>
                                            <li>Hoặc ghi "Đáp án đúng: X" (X là A, B, C hoặc D) ở dòng riêng</li>
                                            <li>Có thể thêm thông tin "Điểm:" và "Mức độ:" (Dễ/Trung bình/Khó)</li>
                                            <li class="text-success"><strong>Mẫu tùy chỉnh</strong> đã được điền sẵn thông tin học phần, chuẩn đầu ra, chương và giảng viên</li>
                                        </ol>
                                    </div>
                                </div>
                                <div v-else>
                                    <div class="btn-group mb-4">
                                        <a :href="route('cauhoi.download_mau_import', { type: 'tu_luan' })" class="btn btn-info">
                                            Tải file mẫu chuẩn
                                        </a>
                                        <a :href="downloadTemplateUrl" class="btn btn-success">
                                            Tải file mẫu tùy chỉnh
                                        </a>
                                    </div>
                                    
                                    <div class="alert alert-info">
                                        <h5 class="font-weight-bold">Hướng dẫn nhập câu hỏi tự luận/vấn đáp:</h5>
                                        <ol class="mb-0 pl-4">
                                            <li>Mỗi câu hỏi bắt đầu với "Câu X:" hoặc "Câu hỏi X:"</li>
                                            <li>Đáp án bắt đầu với "Đáp án:", "Trả lời:" hoặc "Gợi ý:"</li>
                                            <li>Có thể thêm thông tin "Điểm:" và "Mức độ:" (Dễ/Trung bình/Khó)</li>
                                            <li class="text-success"><strong>Mẫu tùy chỉnh</strong> đã được điền sẵn thông tin học phần, chuẩn đầu ra, chương và giảng viên</li>
                                        </ol>
                                    </div>
                                </div>
                                
                                <form @submit.prevent="form.post(route('cauhoi.upload'))">
                                    <input type="hidden" v-model="form.id_ct_ds_dang_ky">
                                    
                                    <div class="form-group mt-4">
                                        <label for="file" class="font-weight-bold">Chọn file Word (.docx, .doc):</label>
                                        <input type="file" id="file" @input="form.file = $event.target.files[0]" class="form-control-file" accept=".docx,.doc">
                                        <div v-if="form.errors.file" class="text-danger">{{ form.errors.file }}</div>
                                    </div>
                                    
                                    <div class="mt-4">
                                        <button type="submit" :disabled="form.processing" class="btn btn-primary">
                                            <span v-if="form.processing">Đang xử lý...</span>
                                            <span v-else>Import</span>
                                        </button>
                                        <Link :href="route('cauhoi.danhsach', ctDangKy.id)" class="btn btn-secondary ml-2">Quay lại</Link>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </template>
    </AppLayout>
</template> 