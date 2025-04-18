<script setup>
import TBMLayout from "@/Layouts/TBMLayout.vue";
import { Link } from "@inertiajs/vue3";
import { router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const { danhsachs, message, success, bo_mon, ds_hoc_ki, ds_nam_hoc, filters } = defineProps({
    danhsachs: {
        type: Object,
        required: true,
        default: () => ({
            data: [],
            current_page: 1,
            last_page: 1,
            total: 0,
            links: [],
        }),
    },
    message: {
        type: String,
        default: "",
    },
    success: {
        type: Boolean,
        default: undefined,
    },
    bo_mon: {
        type: String,
        default: "",
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

const searchTerm = ref(filters.search || "");
const hocKi = ref(filters.hoc_ki || "");
const namHoc = ref(filters.nam_hoc || "");
const debounceTimeout = ref(null);

const performSearch = () => {
    if (debounceTimeout.value) {
        clearTimeout(debounceTimeout.value);
    }
    debounceTimeout.value = setTimeout(() => {
        router.get(
            route('tbm.dsdangky.index'),
            { 
                search: searchTerm.value,
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

watch([searchTerm, hocKi, namHoc], () => {
    performSearch();
});

const handleSearch = (event) => {
    if (event.key === "Enter") {
        performSearch();
    }
};
const handleSend = (id) => {
    if (confirm('Bạn có chắc chắn muốn gửi danh sách này?')) {
        router.post(route('tbm.dsdangky.send', id), {}, {
            onSuccess: () => {
                alert('Gửi danh sách đăng ký thành công!');
            },
            onError: (errors) => {
                alert('Có lỗi xảy ra khi gửi danh sách đăng ký!');
                console.error(errors);
            }
        });
    }
};
const handleEdit = (id) => {
    router.get(route('tbm.dsdangky.edit', id));
};
</script>

<template>
    <TBMLayout>
        <template v-slot:sub-link>
            <li class="breadcrumb-item active">
                <a :href="route('tbm.dsdangky.index')">Danh sách đăng ký</a>
            </li>
        </template>

        <template v-slot:content>
            <div class="content">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">DANH SÁCH ĐĂNG KÝ (Bộ Môn {{ bo_mon }})</h3>
                        <div class="d-flex gap-2">
                            <Link 
                                :href="route('tbm.dsdangky.create')"
                                class="btn btn-success-add"
                            >
                                <i class="fas fa-plus"></i> Tạo danh sách đăng ký
                            </Link>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Form tìm kiếm -->
                        <div class="row mb-4">
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
                            <div class="col-md-4 d-flex align-items-end">
                                <button 
                                    class="btn btn-primary w-100" 
                                    @click="performSearch"
                                >
                                    <i class="fas fa-search"></i> Tìm kiếm
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Học kỳ</th>
                                        <th>Năm học</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="danhsachs.data.length === 0">
                                        <td colspan="5" class="text-center">
                                            Không có dữ liệu
                                        </td>
                                    </tr>
                                    <tr v-for="(ds, index) in danhsachs.data" :key="ds.id">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ ds.hoc_ki }}</td>
                                        <td>{{ ds.nam_hoc }}</td>
                                        <td>
                                            <Link 
                                                :href="route('tbm.ctdsdangky.index', ds.id)"
                                                class="btn btn-sm btn-info me-2"
                                                title="Xem chi tiết"
                                            >
                                                <i class="fas fa-eye"></i>
                                            </Link>
                                            <button
                                                :disabled="!ds.can_send"
                                                @click="handleSend(ds.id)"
                                                class="btn btn-sm btn-primary me-2"
                                                title="Gửi danh sách"
                                            >
                                                <i class="fas fa-paper-plane"> </i>
                                            </button>
                                            <button
                                                :disabled="!ds.can_send"
                                                class="btn btn-sm btn-warning me-2"
                                                title="Sửa"
                                                @click="handleEdit(ds.id)"
                                            >
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </TBMLayout>
</template>

<style scoped>
.btn-success-add {
    background-color: #28a745;
    color: white;
}

.btn-success-add:hover {
    background-color: #218838;
    color: white;
}

.btn-success-edit {
    background-color: #17a2b8;
    color: white;
}

.btn-success-edit:hover {
    background-color: #138496;
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

.form-control:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

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

/* Thêm tooltip styles */
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

.me-2 {
    margin-right: 0.5rem !important;
}
</style>