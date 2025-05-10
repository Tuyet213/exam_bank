<script setup>
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    cauHoi: {
        type: Object,
        required: true
    },
    role: {
        type: String,
        required: true
    }
});
console.log(props.cauHoi);

const confirmDeleteQuestion = (id) => {
    if (confirm('Bạn có chắc chắn muốn xóa câu hỏi này?')) {
        // Gửi request xóa lên server
        router.delete(route('cauhoi.xoa', id));
    }
};
</script>

<template>
    <AppLayout :role="role">
        <template #sub-link>
            <li class="breadcrumb-item">
                <a :href="route('cauhoi.hocphan')">Danh sách học phần</a>
            </li>
            <li class="breadcrumb-item">
                <a :href="route('cauhoi.danhsach', cauHoi.ct_d_s_dang_ky.id)">Danh sách câu hỏi</a>
            </li>
            <li class="breadcrumb-item active">
                Câu hỏi
            </li>
        </template>

        <template #content>
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <!-- Thông tin học phần -->
                            <div class="mb-4">
                                <h2 class="text-xl font-semibold">{{ cauHoi.ct_d_s_dang_ky.hoc_phan.ten }}</h2>
                                <p class="text-gray-600">Mã học phần: {{ cauHoi.ct_d_s_dang_ky.hoc_phan.id }}</p>
                            </div>

                            <!-- Nút thao tác -->
                            <div class="mb-6 flex space-x-4">
                                <Link :href="route('cauhoi.danhsach', cauHoi.ct_d_s_dang_ky.id)" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left mr-2"></i> Quay lại
                                </Link>
                                <Link :href="route('cauhoi.sua', cauHoi.id)" class="btn btn-warning">
                                    <i class="fas fa-edit mr-2"></i> Sửa câu hỏi
                                </Link>
                                <button @click="confirmDeleteQuestion(cauHoi.id)" class="btn btn-danger btn-sm" title="Xóa câu hỏi">
                                    <i class="fas fa-trash mr-2"></i> Xóa câu hỏi
                                </button>
                            </div>

                            <!-- Thông tin chi tiết câu hỏi -->
                            <div class="card mb-4">
                               
                                <div class="card-body">
                                    <div class="row">
                                        
                                        <div class="col-md-2 col-sm-12 mb-3">
                                            <h4 class="font-weight-bold">Phân loại:</h4>
                                            <p>{{ cauHoi.phan_loai == 0 ? 'Trắc nghiệm' : cauHoi.phan_loai == 1 ? 'Tự luận/Vấn đáp' : 'Tự luận' }}</p>
                                        </div>
                                        <div class="col-md-2 col-sm-12 mb-3">
                                            <h4 class="font-weight-bold">Điểm:</h4>
                                            <p>{{ cauHoi.diem }}</p>
                                        </div>
                                        <div class="col-md-2 col-sm-12 mb-3">
                                            <h4 class="font-weight-bold">Mức độ:</h4>
                                            <span :class="{
                                                'badge badge-success': cauHoi.muc_do === 1,
                                                'badge badge-warning': cauHoi.muc_do === 2,
                                                'badge badge-danger': cauHoi.muc_do === 3
                                            }">
                                                {{ cauHoi.muc_do === 1 ? 'Dễ' : cauHoi.muc_do === 2 ? 'Trung bình' : 'Khó' }}
                                            </span>
                                        </div>
                                       
                                        <div class="col-md-2 col-sm-12 mb-3">
                                            <h4 class="font-weight-bold">Chương:</h4>
                                            <p>{{ cauHoi.chuong ? cauHoi.chuong.ten : 'Không có thông tin' }}</p>
                                        </div>
                                        <div class="col-md-2 col-sm-12 mb-3">
                                            <h4 class="font-weight-bold">Chuẩn đầu ra:</h4>
                                            <p>{{ cauHoi.chuan_dau_ra ? cauHoi.chuan_dau_ra.ten : 'Không có thông tin' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Đáp án -->
                            <div class="card">
                                <div class="col-md-12 mb-3">
                                            <h4 class="font-weight-bold">Câu hỏi: {{ cauHoi.cau_hoi }}</h4>
                                            
                                        </div>
                              
                                <div class="card-body">
                                    <h3 class="card-title">Đáp án</h3>
                                    <div v-if="cauHoi.dap_ans && cauHoi.dap_ans.length > 0">
                                        <div class="table-responsive">
                                            <!-- Hiển thị đáp án trắc nghiệm dạng bảng -->
                                            <table class="table table-bordered">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th scope="col" width="5%">STT</th>
                                                        <th scope="col" width="65%">Nội dung đáp án</th>
                                                        <th scope="col" width="15%">Điểm</th>
                                                        <th v-if="cauHoi.phan_loai == 0" scope="col" width="15%">Đáp án đúng</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(dapAn, index) in cauHoi.dap_ans" :key="dapAn.id">
                                                        <td class="text-center">{{ index + 1 }}</td>
                                                        <td>
                                                            <span v-if="cauHoi.phan_loai == 0">
                                                                {{ String.fromCharCode(65 + index) }}. {{ dapAn.dap_an }}
                                                            </span>
                                                            <span v-else>
                                                                Nội dung ý {{ index + 1 }}: {{ dapAn.dap_an }}
                                                            </span>
                                                        </td>
                                                        <td class="text-center">{{ dapAn.diem }}</td>
                                                        <td class="text-center" v-if="cauHoi.phan_loai == 0">
                                                            
                                                                <i v-if="dapAn.trang_thai==1" class="fas fa-check text-success"></i>
                                                            
                                                           
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                       
                                    </div>
                                    <div v-else class="alert alert-warning">
                                        Chưa có đáp án
                                    </div>
                                </div>
                            </div>
<!-- 
                            <ul v-if="cauHoi.dap_ans && cauHoi.dap_ans.length">
                                <li v-for="dapAn in cauHoi.dap_ans" :key="dapAn.id">
                                    {{ dapAn.dap_an }} (Điểm: {{ dapAn.diem }})
                                </li>
                            </ul> -->
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </AppLayout>
</template> 