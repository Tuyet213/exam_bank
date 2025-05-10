<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    ctDangKy: {
        type: Object,
        required: true
    },
    chuanDauRas: {
        type: Array,
        required: true
    },
    chuongs: {
        type: Array,
        required: true
    },
    role: {
        type: String,
        required: true
    }
});

// Thêm computed property để hiển thị tên loại câu hỏi
const questionTypeText = computed(() => {
    switch (form.phan_loai) {
        case 0: return 'Trắc nghiệm';
        case 1: return 'Tự luận/Vấn đáp';
        case 2: return 'Tự luận';
        default: return 'Không xác định';
    }
});

// Map giá trị hinh_thuc_thi sang phan_loai
const mapHinhThucThiToPhanLoai = (hinhThucThi) => {
    switch (hinhThucThi) {
        case 'Trắc nghiệm': return 0;
        case 'Tự luận/Vấn đáp': return 1;
        case 'Tự luận': return 2;
        default: return 2; // Mặc định là tự luận
    }
};

// Xác định phân loại ban đầu
const initialPhanLoai = mapHinhThucThiToPhanLoai(props.ctDangKy.hinh_thuc_thi);

const form = useForm({
    cau_hoi: '',
    id_ct_ds_dang_ky: props.ctDangKy.id,
    id_chuan_dau_ra: '',
    id_chuong: '',
    diem: 0,
    phan_loai: initialPhanLoai,
    muc_do: 2, // Mặc định là trung bình
    dap_ans: [
        { noi_dung: '', trang_thai: false, diem: 0 },
        { noi_dung: '', trang_thai: false, diem: 0 },
        { noi_dung: '', trang_thai: false, diem: 0 },
        { noi_dung: '', trang_thai: false, diem: 0 }
    ]
});

// Tự động tính tổng điểm dựa trên điểm của các đáp án đúng
const calculateScore = () => {
    let totalScore = 0;
        form.dap_ans.forEach(dapAn => {
            totalScore += parseFloat(dapAn.diem) || 0;
        });
        form.diem = totalScore;
};

// Theo dõi thay đổi trong đáp án để cập nhật điểm tự động
watch(() => [...form.dap_ans], calculateScore, { deep: true });

// Thêm đáp án mới
const addAnswer = () => {
    form.dap_ans.push({ noi_dung: '', trang_thai: false, diem: 0 });
};

// Xóa đáp án
const removeAnswer = (index) => {
    if (form.dap_ans.length > 1) {
        form.dap_ans.splice(index, 1);
    }
};

const submit = () => {
    // Đảm bảo tất cả các đáp án không chọn có điểm = 0
    if (form.phan_loai == 0) {
        form.dap_ans.forEach(dapAn => {
            if (!dapAn.trang_thai) {
                dapAn.diem = 0;
            }
        });
    }
    
    form.post(route('cauhoi.luu'), {
        onSuccess: () => {
            form.reset();
        }
    });
};
</script>

<template>
    <AppLayout :role="role">
        <template #sub-link>
            <li class="breadcrumb-item">
                <a :href="route('cauhoi.hocphan')">Danh sách học phần</a>
            </li>
            <li class="breadcrumb-item active">
                Tạo câu hỏi mới - {{ ctDangKy.hoc_phan.ten }}
            </li>
        </template>

       <template #content>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit">
                            <!-- Nội dung câu hỏi -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    Nội dung câu hỏi
                                </label>
                                <textarea v-model="form.cau_hoi"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    rows="4" required></textarea>
                            </div>

                            <!-- Loại câu hỏi -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    Loại câu hỏi
                                </label>
                                <select v-model="form.phan_loai"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    disabled>
                                    <option :value="0">Trắc nghiệm</option>
                                    <option :value="1">Tự luận/Vấn đáp</option>
                                    <option :value="2">Tự luận</option>
                                </select>
                                <p class="text-sm text-gray-500 mt-1">
                                    Loại câu hỏi được xác định là <strong>{{ questionTypeText }}</strong> dựa trên hình thức thi của học phần
                                </p>
                            </div>

                            <!-- Mức độ -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    Mức độ
                                </label>
                                <select v-model="form.muc_do"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <option :value="1">Dễ</option>
                                    <option :value="2">Trung bình</option>
                                    <option :value="3">Khó</option>
                                </select>
                            </div>

                            <!-- Điểm -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    Điểm
                                </label>
                                <input type="number" v-model="form.diem"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    min="0" step="0.005" readonly>
                                <p v-if="form.phan_loai == 0" class="text-sm text-gray-500 mt-1">
                                    Điểm được tính tự động từ các đáp án đúng
                                </p>
                                <p v-else>Điểm được tự động tính từ điểm các đáp án</p>
                            </div>

                            <!-- Thêm trường chọn chuẩn đầu ra và chương -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">
                                        Chuẩn đầu ra
                                    </label>
                                    <select v-model="form.id_chuan_dau_ra"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                        <option value="" disabled>Chọn chuẩn đầu ra</option>
                                        <option v-for="cdr in chuanDauRas" :key="cdr.id" :value="cdr.id">
                                            {{ cdr.ten }}
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">
                                        Chương
                                    </label>
                                    <select v-model="form.id_chuong"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                        <option value="" disabled>Chọn chương</option>
                                        <option v-for="chuong in chuongs" :key="chuong.id" :value="chuong.id">
                                            {{ chuong.ten }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- Đáp án (chỉ hiển thị khi là câu hỏi trắc nghiệm) -->
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <label class="block text-gray-700 text-sm font-bold">
                                        Đáp án
                                    </label>
                                    <button type="button" @click="addAnswer" 
                                        class="mt-2 bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded focus:outline-none focus:shadow-outline">
                                        Thêm đáp án
                                    </button>
                                </div>
                                
                                <div v-for="(dapAn, index) in form.dap_ans" :key="index" 
                                    class="bg-gray-50 p-3 mb-2 rounded-lg border border-gray-200">
                                    <div class="flex items-start space-x-2">
                                        <div class="flex-grow">
                                            <input type="text" v-model="dapAn.noi_dung"
                                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mb-2"
                                                :placeholder="'Nội dung đáp án ' + (index + 1)" required>
                                            
                                            <div class="flex items-center space-x-4">
                                                <label :hidden="form.phan_loai != 0" class="flex items-center">
                                                    <input type="checkbox" v-model="dapAn.trang_thai"
                                                        class="form-checkbox h-4 w-4 text-blue-600" :checked="form.phan_loai != 0">
                                            
                                                </label>
                                                
                                                <div class="flex items-center">
                                                    <label class="text-sm mr-2">Điểm:</label>
                                                    <input type="number" v-model="dapAn.diem"
                                                        :min="form.phan_loai != 0 ? 0.25 : 0"
                                                        :max="form.phan_loai != 0 ? 0.5 : 0"
                                                        step="0.01"
                                                        class="shadow appearance-none border rounded w-20 py-1 px-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                                        >
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <button type="button" @click="removeAnswer(index)"
                                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded focus:outline-none focus:shadow-outline"
                                            :disabled="form.dap_ans.length <= 1">
                                            X
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Nút submit -->
                            <div class="flex items-center justify-end">
                                <button type="submit"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                    Lưu câu hỏi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
       </template>
    </AppLayout>
</template> 