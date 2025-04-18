<script setup>
import TKLayout from "@/Layouts/TKLayout.vue";
import { Link } from "@inertiajs/vue3";
import { router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    ds_bien_ban: {
        type: Array,
        required: true
    },
    ds_bo_mon: {
        type: Array,
        required: true
    },
    ds_hoc_ki: {
        type: Array,
        required: true
    },
    ds_nam_hoc: {
        type: Array,
        required: true
    },
    filters: {
        type: Object,
        required: true
    }
});
console.log(props.ds_bien_ban);
const boMon = ref(props.filters.bo_mon || "");
const hocKi = ref(props.filters.hoc_ki || "");
const namHoc = ref(props.filters.nam_hoc || "");
const debounceTimeout = ref(null);

const performSearch = () => {
    if (debounceTimeout.value) {
        clearTimeout(debounceTimeout.value);
    }
    debounceTimeout.value = setTimeout(() => {
        router.get(
            route('tk.dsbienban.index'),
            { 
                bo_mon: boMon.value,
                hoc_ki: hocKi.value,
                nam_hoc: namHoc.value
            },
            { 
                preserveState: true,
                replace: true 
            }
        );
    }, 300);
};

const uploadNoiDung = (bienBan) => {
    // Tạo input file ẩn
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'application/pdf';
    
    input.onchange = (e) => {
        const file = e.target.files[0];
        if (file) {
            const formData = new FormData();
            formData.append('noi_dung', file);
            
            // Gửi file lên server
            router.post(route('tk.dsbienban.upload-noi-dung', bienBan.id), formData, {
                preserveScroll: true,
                onSuccess: () => {
                    alert('Upload nội dung thành công!');
                },
                onError: () => {
                    alert('Có lỗi xảy ra khi upload file!');
                }
            });
        }
    };
    
    input.click();
};

watch([boMon, hocKi, namHoc], () => {
    performSearch();
});
</script>

<template>
    <TKLayout>
        <template v-slot:sub-link>
            <li class="breadcrumb-item active">Danh sách biên bản họp</li>
        </template>

        <template v-slot:content>
            <div class="content">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">DANH SÁCH BIÊN BẢN HỌP</h3>
                        </div>

                        <div class="card-body">
                            <!-- Form tìm kiếm -->
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <label for="bo_mon" class="form-label">Bộ môn</label>
                                    <select 
                                        id="bo_mon" 
                                        class="form-select" 
                                        v-model="boMon"
                                    >
                                        <option value="">Tất cả bộ môn</option>
                                        <option v-for="bm in ds_bo_mon" :key="bm.id" :value="bm.id">
                                            {{ bm.ten }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="hoc_ki" class="form-label">Học kỳ</label>
                                    <select 
                                        id="hoc_ki" 
                                        class="form-select" 
                                        v-model="hocKi"
                                    >
                                        <option value="">Tất cả học kỳ</option>
                                        <option v-for="hk in ds_hoc_ki" :key="hk" :value="hk">
                                            Học kỳ {{ hk }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="nam_hoc" class="form-label">Năm học</label>
                                    <select 
                                        id="nam_hoc" 
                                        class="form-select" 
                                        v-model="namHoc"
                                    >
                                        <option value="">Tất cả năm học</option>
                                        <option v-for="nam in ds_nam_hoc" :key="nam" :value="nam">
                                            {{ nam }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- Bảng danh sách -->
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>STT</th>
                                            <th>Bộ môn</th>
                                            <th>Thời gian</th>
                                            <th>Địa điểm</th>
                                            <th>Học kỳ</th>
                                            <th>Năm học</th>
                                            <th>File biên bản</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-if="ds_bien_ban.length === 0">
                                            <td colspan="8" class="text-center">
                                                Không có dữ liệu
                                            </td>
                                        </tr>
                                        <tr v-for="(bb, index) in ds_bien_ban" :key="bb.id">
                                            <td>{{ index + 1 }}</td>
                                            <td>{{ bb.ds_dang_ky?.bo_mon?.ten }}</td>
                                            <td>{{ bb.thoi_gian }}</td>
                                            <td>{{ bb.dia_diem }}</td>
                                            <td>{{ bb.ds_dang_ky?.hoc_ki }}</td>
                                            <td>{{ bb.ds_dang_ky?.nam_hoc }}</td>
                                            <td class="text-center">
                                                <a 
                                                    v-if="bb.noi_dung"
                                                    :href="route('tk.dsbienban.download', bb.id)"
                                                    class="btn btn-sm btn-primary"
                                                    title="Tải xuống"
                                                >
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <span v-else class="text-muted">
                                                    Chưa có
                                                </span>
                                            </td>
                                            <td>
                                                <Link 
                                                    :href="route('tk.dsbienban.edit', bb.id)"
                                                    class="btn btn-sm btn-warning"
                                                    title="Chỉnh sửa"
                                                >
                                                    <i class="fas fa-edit"></i>
                                                </Link>
                                                <Link 
                                                    :href="route('tk.dsbienban.edit-so-gio', bb.id)"
                                                    class="btn btn-sm btn-secondary me-2"
                                                    title="Chỉnh sửa số giờ"
                                                >
                                                    <i class="fas fa-clock"></i>
                                                </Link>
                                                <button 
                                                    class="btn btn-sm btn-info me-2"
                                                    title="Thêm nội dung"
                                                    @click="uploadNoiDung(bb)"
                                                >
                                                    <i class="fas fa-file-pdf"></i>
                                                </button>

                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </TKLayout>
</template>

<style scoped>
.btn-info {
    background-color: #17a2b8;
    color: white;
}

.btn-info:hover {
    background-color: #138496;
    color: white;
}

.btn-primary {
    background-color: #007bff;
    color: white;
}

.btn-primary:hover {
    background-color: #0056b3;
    color: white;
}

.table th {
    background-color: #f8f9fa;
    color: #495057;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

[title] {
    position: relative;
    cursor: pointer;
}

[title]:hover::after {
    content: attr(title);
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    padding: 4px 8px;
    background-color: rgba(0, 0, 0, 0.8);
    color: white;
    border-radius: 4px;
    font-size: 12px;
    white-space: nowrap;
    z-index: 1000;
}
</style> 