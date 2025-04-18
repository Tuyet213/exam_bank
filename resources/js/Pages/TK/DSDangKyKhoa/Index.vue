<script setup>
import TKLayout from "@/Layouts/TKLayout.vue";
import { Link } from "@inertiajs/vue3";
import { router } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';

const props = defineProps({
    ds_dang_ky: {
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
console.log(props.ds_dang_ky);
const boMon = ref(props.filters.bo_mon || "");
const hocKi = ref(props.filters.hoc_ki || "");
const namHoc = ref(props.filters.nam_hoc || "");
const debounceTimeout = ref(null);
const selectedItems = ref([]);

// Lọc danh sách đăng ký chưa có biên bản họp
const dsDangKyChuaCoBienBan = computed(() => {
    return props.ds_dang_ky.filter(item => !item.has_bien_ban);
});

// Kiểm tra xem có đăng ký nào được chọn không
const hasSelectedItems = computed(() => {
    return selectedItems.value.length > 0;
});

// Chọn/bỏ chọn tất cả
const toggleSelectAll = () => {
    if (selectedItems.value.length === dsDangKyChuaCoBienBan.value.length) {
        selectedItems.value = [];
    } else {
        selectedItems.value = dsDangKyChuaCoBienBan.value.map(item => item.id);
    }
};

// Tạo biên bản họp cho các đăng ký đã chọn
const createBienBanForSelected = () => {
    if (selectedItems.value.length === 0) return;
    
    router.get(route('tk.dsbienban.create'), {
        ds_dang_ky_ids: selectedItems.value
    });
};

const performSearch = () => {
    if (debounceTimeout.value) {
        clearTimeout(debounceTimeout.value);
    }
    debounceTimeout.value = setTimeout(() => {
        router.get(
            route('tk.dsdangky.index'),
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

watch([boMon, hocKi, namHoc], () => {
    performSearch();
});
</script>

<template>
    <TKLayout>
        <template v-slot:sub-link>
            <li class="breadcrumb-item active">Danh sách đăng ký biên soạn</li>
        </template>

        <template v-slot:content>
            <div class="content">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">DANH SÁCH ĐĂNG KÝ BIÊN SOẠN</h3>
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

                            <!-- Nút tạo biên bản họp hàng loạt -->
                            <div class="mb-3 text-end" v-if="dsDangKyChuaCoBienBan.length > 0">
                                <button 
                                    class="btn btn-success"
                                    @click="createBienBanForSelected"
                                    :disabled="!hasSelectedItems"
                                >
                                    <i class="fas fa-plus"></i> Tạo biên bản họp hàng loạt
                                </button>
                            </div>

                            <!-- Bảng danh sách -->
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>
                                                <div class="form-check">
                                                    <input 
                                                        class="form-check-input" 
                                                        type="checkbox" 
                                                        :checked="selectedItems.length === dsDangKyChuaCoBienBan.length && dsDangKyChuaCoBienBan.length > 0"
                                                        @change="toggleSelectAll"
                                                    >
                                                </div>
                                            </th>
                                            <th>STT</th>
                                            <th>Bộ môn</th>
                                            <th>Học kỳ</th>
                                            <th>Năm học</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-if="ds_dang_ky.length === 0">
                                            <td colspan="6" class="text-center">
                                                Không có dữ liệu
                                            </td>
                                        </tr>
                                        <tr v-for="(dk, index) in ds_dang_ky" :key="dk.id">
                                            <td>
                                                <div class="form-check" v-if="!dk.has_bien_ban">
                                                    <input 
                                                        class="form-check-input" 
                                                        type="checkbox" 
                                                        :value="dk.id"
                                                        v-model="selectedItems"
                                                    >
                                                </div>
                                            </td>
                                            <td>{{ index + 1 }}</td>
                                            <td>{{ dk.bo_mon?.ten }}</td>
                                            <td>{{ dk.hoc_ki }}</td>
                                            <td>{{ dk.nam_hoc }}</td>
                                            <td>
                                                <div class="btn-group">
                                                </div>
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