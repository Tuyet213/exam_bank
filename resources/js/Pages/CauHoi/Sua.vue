<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, computed, onMounted, watch } from 'vue';

const props = defineProps({
    cauHoi: {
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

// Khởi tạo form từ dữ liệu câu hỏi hiện tại
const form = useForm({
    cau_hoi: props.cauHoi.cau_hoi || '',
    id_chuan_dau_ra: props.cauHoi.id_chuan_dau_ra || '',
    id_chuong: props.cauHoi.id_chuong || '',
    diem: props.cauHoi.diem || 0,
    muc_do: props.cauHoi.muc_do || 2,
    dap_ans: props.cauHoi.dap_ans?.map(da => ({
        id: da.id,
        noi_dung: da.dap_an,
        trang_thai: da.trang_thai,
        diem: da.diem
    })) || [
        { id: null, noi_dung: '', trang_thai: false, diem: 0 }
    ]
});

// Hàm tự động tính điểm tổng
const calculateScore = () => {
    let totalScore = 0;
    if (props.cauHoi.phan_loai == 0) {
        form.dap_ans.forEach(dapAn => {
            if (dapAn.trang_thai) {
                totalScore += parseFloat(dapAn.diem) || 0;
            }
        });
    } else {
        form.dap_ans.forEach(dapAn => {
            totalScore += parseFloat(dapAn.diem) || 0;
        });
    }
    form.diem = totalScore;
};

// Theo dõi thay đổi đáp án để tự động tính điểm
watch(
  [() => form.dap_ans.map(d => d.diem), () => form.dap_ans.map(d => d.trang_thai)],
  calculateScore,
  { deep: true }
);

// Thêm đáp án mới
const addDapAn = () => {
    form.dap_ans.push({
        id: null,
        noi_dung: '',
        trang_thai: false,
        diem: 0
    });
};

// Xóa đáp án
const removeDapAn = (index) => {
    if (form.dap_ans.length > 1) {
        form.dap_ans.splice(index, 1);
    }
};

// Xử lý form submit
const submit = () => {
    
    // Kiểm tra nội dung đáp án
    const emptyAnswers = form.dap_ans.filter(da => !da.noi_dung.trim());
    if (emptyAnswers.length > 0) {
        alert('Vui lòng nhập nội dung cho tất cả đáp án!');
        return;
    }
    
    
    
    // Kiểm tra nếu là câu hỏi trắc nghiệm thì phải có ít nhất một đáp án đúng
    if (props.cauHoi.phan_loai === 0) {
        const hasCorrectAnswer = form.dap_ans.some(da => da.trang_thai);
        if (!hasCorrectAnswer) {
            alert('Câu hỏi trắc nghiệm phải có ít nhất một đáp án đúng!');
            return;
        }
        
        // Kiểm tra đáp án đúng phải có điểm > 0
        const correctAnswersWithoutScore = form.dap_ans.filter(da => da.trang_thai && da.diem <= 0);
        if (correctAnswersWithoutScore.length > 0) {
            alert('Đáp án đúng phải có điểm lớn hơn 0!');
            return;
        }
    }
    
    console.log('Dữ liệu gửi đi:', form.data());
    form.post(route('cauhoi.capnhat', props.cauHoi.id), {
        onError: (errors) => {
            console.log('Lỗi validation:', errors);
            // Hiển thị lỗi đầu tiên
            const firstError = Object.values(errors)[0];
            if (firstError) {
                alert('Lỗi: ' + firstError);
            }
        },
        onSuccess: () => {
            console.log('Cập nhật thành công!');
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
            <li class="breadcrumb-item">
                <a :href="route('cauhoi.danhsach', cauHoi.id_ct_ds_dang_ky)">Danh sách câu hỏi</a>
            </li>
            <li class="breadcrumb-item active">
                Sửa câu hỏi
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
                                <p class="text-gray-600">Mã học phần: {{ cauHoi.ct_d_s_dang_ky.hoc_phan.ma_hoc_phan }}</p>
                            </div>

                            <form @submit.prevent="submit">
                                <!-- Nội dung câu hỏi -->
                                <div class="form-group mb-4">
                                    <label for="cau_hoi" class="form-label font-weight-bold">Nội dung câu hỏi:</label>
                                    <textarea
                                        id="cau_hoi"
                                        v-model="form.cau_hoi"
                                        class="form-control"
                                        rows="4"
                                        required
                                    ></textarea>
                                    <div v-if="form.errors.cau_hoi" class="text-danger mt-1">
                                        {{ form.errors.cau_hoi }}
                                    </div>
                                </div>

                                <!-- Chuẩn đầu ra -->
                                <div class="form-group mb-4">
                                    <label for="id_chuan_dau_ra" class="form-label font-weight-bold">Chuẩn đầu ra:</label>
                                    <select
                                        id="id_chuan_dau_ra"
                                        v-model="form.id_chuan_dau_ra"
                                        class="form-control"
                                        required
                                    >
                                        <option value="" disabled>Chọn chuẩn đầu ra</option>
                                        <option v-for="cdr in chuanDauRas" :key="cdr.id" :value="cdr.id">
                                            {{ cdr.ten }}
                                        </option>
                                    </select>
                                    <div v-if="form.errors.id_chuan_dau_ra" class="text-danger mt-1">
                                        {{ form.errors.id_chuan_dau_ra }}
                                    </div>
                                </div>

                                <!-- Chương -->
                                <div class="form-group mb-4">
                                    <label for="id_chuong" class="form-label font-weight-bold">Chương:</label>
                                    <select
                                        id="id_chuong"
                                        v-model="form.id_chuong"
                                        class="form-control"
                                        required
                                    >
                                        <option value="" disabled>Chọn chương</option>
                                        <option v-for="chuong in chuongs" :key="chuong.id" :value="chuong.id">
                                            {{ chuong.ten }}
                                        </option>
                                    </select>
                                    <div v-if="form.errors.id_chuong" class="text-danger mt-1">
                                        {{ form.errors.id_chuong }}
                                    </div>
                                </div>

                                <!-- Mức độ -->
                                <div class="form-group mb-4">
                                    <label class="form-label font-weight-bold">Mức độ:</label>
                                    <div class="d-flex gap-4">
                                        <div class="form-check">
                                            <input
                                                id="muc_do_1"
                                                v-model="form.muc_do"
                                                type="radio"
                                                :value="1"
                                                class="form-check-input"
                                                name="muc_do"
                                            />
                                            <label for="muc_do_1" class="form-check-label">Dễ</label>
                                        </div>
                                        <div class="form-check">
                                            <input
                                                id="muc_do_2"
                                                v-model="form.muc_do"
                                                type="radio"
                                                :value="2"
                                                class="form-check-input"
                                                name="muc_do"
                                            />
                                            <label for="muc_do_2" class="form-check-label">Trung bình</label>
                                        </div>
                                        <div class="form-check">
                                            <input
                                                id="muc_do_3"
                                                v-model="form.muc_do"
                                                type="radio"
                                                :value="3"
                                                class="form-check-input"
                                                name="muc_do"
                                            />
                                            <label for="muc_do_3" class="form-check-label">Khó</label>
                                        </div>
                                    </div>
                                    <div v-if="form.errors.muc_do" class="text-danger mt-1">
                                        {{ form.errors.muc_do }}
                                    </div>
                                </div>

                                <!-- Điểm (nếu là tự luận) -->
                                <div v-if="cauHoi.phan_loai !== 0" class="form-group mb-4">
                                    <label for="diem" class="form-label font-weight-bold">Điểm:</label>
                                    <input
                                        id="diem"
                                        v-model="form.diem"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        class="form-control"
                                        required
                                        readonly
                                        title="Điểm được tính tự động từ tổng điểm các đáp án"
                                    />
                                    <div v-if="form.errors.diem" class="text-danger mt-1">
                                        {{ form.errors.diem }}
                                    </div>
                                </div>

                                <!-- Đáp án -->
                                <div class="mb-4">
                                    <div class="flex justify-between items-center mb-2">
                                        <label class="block text-gray-700 text-sm font-bold">
                                            Đáp án
                                        </label>
                                        <button type="button" @click="addDapAn" 
                                            class="mt-2 bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded focus:outline-none focus:shadow-outline flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Thêm đáp án
                                        </button>
                                    </div>
                                    <div v-for="(dapAn, index) in form.dap_ans" :key="index" 
                                        class="bg-gray-50 p-3 mb-2 rounded-lg border border-gray-200">
                                        <div class="flex items-start space-x-2">
                                            <div class="flex-grow">
                                                <textarea
                                                    v-model="dapAn.noi_dung"
                                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mb-2"
                                                    :placeholder="'Nội dung đáp án ' + (index + 1)"
                                                    rows="3"
                                                    required
                                                ></textarea>
                                                <div class="flex items-center space-x-4">
                                                    <label v-if="props.cauHoi.phan_loai == 0" class="flex items-center">
                                                        <input type="checkbox" v-model="dapAn.trang_thai"
                                                            class="form-checkbox h-4 w-4 text-blue-600"
                                                            :checked="dapAn.trang_thai">
                                                        <span class="ml-2">Đáp án đúng</span>
                                                    </label>
                                                    <div class="flex items-center">
                                                        <label class="text-sm mr-2">Điểm:</label>
                                                        <input type="number" v-model="dapAn.diem"
                                                            :disabled="props.cauHoi.phan_loai == 0 && !dapAn.trang_thai"
                                                            class="shadow appearance-none border rounded w-20 py-1 px-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                                            min="0" step="0.01">
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" @click="removeDapAn(index)"
                                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded focus:outline-none focus:shadow-outline"
                                                :disabled="form.dap_ans.length <= 1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Nút submit -->
                                <div class="d-flex gap-3">
                                    <Link :href="route('cauhoi.danhsach', cauHoi.id_ct_ds_dang_ky)" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left mr-2"></i> Quay lại
                                    </Link>
                                    <button type="submit" class="btn btn-primary" :disabled="form.processing">
                                        <i class="fas fa-save mr-2"></i> Lưu thay đổi
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