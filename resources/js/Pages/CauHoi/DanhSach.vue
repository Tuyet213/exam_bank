<script setup>
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, watch, computed } from 'vue';

const props = defineProps({
    ctDangKy: {
        type: Object,
        required: true
    },
    cauHois: {
        type: Array,
        required: true
    },
    role: {
        type: String,
        required: true
    },
    filters: Object
});

// Thêm các biến reactive cho bộ lọc
const searchCauHoi = ref(props.filters.cau_hoi || '');
const selectedMucDo = ref(props.filters.muc_do || '');
const selectedChuong = ref(props.filters.id_chuong || '');
const selectedChuanDauRa = ref(props.filters.id_chuan_dau_ra || '');

// Tạo danh sách mức độ và chương
const mucDoList = [
    { value: '', label: 'Tất cả mức độ' },
    { value: '1', label: 'Dễ' },
    { value: '2', label: 'Trung bình' },
    { value: '3', label: 'Khó' },
];

const chuongList = props.ctDangKy.hoc_phan.chuongs || [];
const chuanDauRaList = props.ctDangKy.hoc_phan.chuan_dau_ras || [];

// Hàm lọc câu hỏi
const filteredCauHois = computed(() => {
    return props.cauHois.filter(cauHoi => {
        const matchSearch = cauHoi.cau_hoi.toLowerCase().includes(searchCauHoi.value.toLowerCase());
        const matchMucDo = !selectedMucDo.value || cauHoi.muc_do === selectedMucDo.value;
        const matchChuong = !selectedChuong.value || cauHoi.chuong.id === selectedChuong.value;
        const matchChuanDauRa = !selectedChuanDauRa.value || cauHoi.chuan_dau_ra.id === selectedChuanDauRa.value;
        return matchSearch && matchMucDo && matchChuong && matchChuanDauRa;
    });
});

const confirmDeleteQuestion = (id) => {
    if (confirm('Bạn có chắc chắn muốn xóa câu hỏi này?')) {
        // Gửi request xóa lên server
        router.delete(route('cauhoi.xoa', id));
    }
};

watch([searchCauHoi, selectedMucDo, selectedChuong, selectedChuanDauRa], () => {
    router.get(
        route('cauhoi.danhsach', props.ctDangKy.id),
        {
            cau_hoi: searchCauHoi.value,
            muc_do: selectedMucDo.value,
            id_chuong: selectedChuong.value,
            id_chuan_dau_ra: selectedChuanDauRa.value,
        },
        {
            preserveState: true,
            replace: true,
        }
    );
});
</script>

<template>
    <AppLayout :role="role">
        <template #sub-link>
            <li class="breadcrumb-item">
                <a :href="route('cauhoi.hocphan')">Danh sách học phần</a>
            </li>
            <li class="breadcrumb-item active">
                Danh sách câu hỏi
            </li>
        </template>

        <template #content>
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <!-- Thông tin học phần -->
                            <div class="mb-4">
                                <h2 class="text-xl font-semibold">{{ ctDangKy.hoc_phan.ten }}</h2>
                                <p class="text-gray-600">Mã học phần: {{ ctDangKy.hoc_phan.id }}</p>
                            </div>

                            <!-- Bộ lọc -->
                            <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="form-group">
                                    <input 
                                        type="text" 
                                        v-model="searchCauHoi"
                                        class="form-control" 
                                        placeholder="Tìm kiếm câu hỏi..."
                                    >
                                </div>
                                <div class="form-group">
                                    <select v-model="selectedMucDo" class="form-control">
                                        <option v-for="mucDo in mucDoList" :key="mucDo.value" :value="mucDo.value">
                                            {{ mucDo.label }}
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select v-model="selectedChuong" class="form-control">
                                        <option value="">Tất cả chương</option>
                                        <option v-for="chuong in chuongList" :key="chuong.id" :value="chuong.id">
                                            {{ chuong.ten }}
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select v-model="selectedChuanDauRa" class="form-control">
                                        <option value="">Tất cả chuẩn đầu ra</option>
                                        <option v-for="chuanDauRa in chuanDauRaList" :key="chuanDauRa.id" :value="chuanDauRa.id">
                                            {{ chuanDauRa.ten }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- Nút chức năng -->
                            <div class="mb-6 flex space-x-4">
                                <Link :href="route('cauhoi.tao', ctDangKy.id)"
                                    class="btn btn-success">
                                    <i class="fas fa-plus-circle mr-2"></i> Tạo câu hỏi mới
                                </Link>
                                
                                <!-- Hiển thị nút import dựa vào hình thức thi -->
                                <Link v-if="ctDangKy.hinh_thuc_thi == 'Trắc nghiệm'"
                                    :href="route('cauhoi.import', ctDangKy.id)"
                                    class="btn btn-primary">
                                    <i class="fas fa-file-import mr-2"></i> Import câu hỏi trắc nghiệm
                                </Link>
                                <Link v-else
                                    :href="route('cauhoi.import', ctDangKy.id)"
                                    class="btn btn-primary">
                                    <i class="fas fa-file-import mr-2"></i> Import câu hỏi tự luận/vấn đáp
                                </Link>
                            </div>

                            <!-- Danh sách câu hỏi dạng bảng -->
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th scope="col" width="3%">STT</th>
                                            <th scope="col" width="27%">Câu hỏi</th>
                                            <th scope="col" width="10%">Loại câu hỏi</th>
                                            <th scope="col" width="8%">Mức độ</th>
                                            <th scope="col" width="6%">Điểm</th>
                                            <th scope="col" width="18%">Chương</th>
                                            <th scope="col" width="18%">Chuẩn đầu ra</th>
                                            <th scope="col" width="10%">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(cauHoi, index) in filteredCauHois" :key="cauHoi.id">
                                            <td class="text-center">{{ index + 1 }}</td>
                                            <td>
                                                <div style="max-height: 80px; overflow: hidden; text-overflow: ellipsis;">
                                                    {{ cauHoi.cau_hoi }}
                                                </div>
                                            </td>
                                            <td>{{ cauHoi.phan_loai == 0 ? 'Trắc nghiệm' : cauHoi.phan_loai == 1 ? 'Tự luận/Vấn đáp' : 'Tự luận' }}</td>
                                            <td>
                                                <span :class="{
                                                    'badge badge-success': cauHoi.muc_do === 1,
                                                    'badge badge-warning': cauHoi.muc_do === 2,
                                                    'badge badge-danger': cauHoi.muc_do === 3
                                                }">
                                                    {{ cauHoi.muc_do == 1 ? 'Dễ' : cauHoi.muc_do == 2 ? 'Trung bình' : 'Khó' }}
                                                </span>
                                            </td>
                                            <td class="text-center">{{ cauHoi.diem }}</td>
                                            <td>{{ cauHoi.chuong ? cauHoi.chuong.ten : 'Không có' }}</td>
                                            <td>{{ cauHoi.chuan_dau_ra ? cauHoi.chuan_dau_ra.ten : 'Không có' }}</td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <Link :href="route('cauhoi.chitiet', cauHoi.id)" class="btn btn-info btn-sm" title="Xem chi tiết">
                                                        <i class="fas fa-eye"></i>
                                                    </Link>
                                                    <Link :href="route('cauhoi.sua', cauHoi.id)" class="btn btn-warning btn-sm" title="Sửa câu hỏi">
                                                        <i class="fas fa-edit"></i>
                                                    </Link>
                                                    <button @click="confirmDeleteQuestion(cauHoi.id)" class="btn btn-danger btn-sm" title="Xóa câu hỏi">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr v-if="filteredCauHois.length === 0">
                                            <td colspan="8" class="text-center">Không tìm thấy câu hỏi nào</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </AppLayout>
</template> 