<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link, router } from "@inertiajs/vue3";
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
const props = defineProps({
    dsdangky: {
        type: Object,
        required: true
    },
    chitiet: {
        type: Array,
        required: true
    },
    ct_da_co_bien_ban: {
        type: Array,
        default: () => []
    },
    can_create: {
        type: Boolean,
        required: true
    },
    success: {
        type: String,
        default: ''
    },
    error: {
        type: String,
        default: ''
    },
    selected_ct_ids: {
        type: Array,
        default: () => []
    }
});


const showImportModal = ref(false);
const form = useForm({
    file: null
});

// Thêm state cho checkbox
const selectedCTs = ref(props.selected_ct_ids || []);

// Computed property để kiểm tra có thể tạo biên bản không
const canCreateBienBan = computed(() => {
    return selectedCTs.value.length > 0;
});

// Hàm kiểm tra chi tiết đã có biên bản chưa
const hasBienBan = (ctId) => {
    return props.ct_da_co_bien_ban.includes(ctId);
};

// Hàm xử lý chọn/bỏ chọn tất cả
const selectAll = ref(false);
const toggleSelectAll = () => {
    if (selectAll.value) {
        // Chỉ chọn những mục chưa có biên bản họp
        selectedCTs.value = props.chitiet
            .filter(ct => !hasBienBan(ct.id))
            .map(ct => ct.id);
    } else {
        selectedCTs.value = [];
    }
};

const handleEdit = (id) => {
    router.get(route('tbm.ctdsdangky.edit', id), {}, {
        onError: () => {
            alert('Có lỗi xảy ra khi chuyển trang!');
        }
    });
};
const handleDelete = (id) => {
    if (confirm('Bạn có chắc chắn muốn xóa phân công này?')) {
        router.delete(route('tbm.ctdsdangky.destroy', id), {
            onSuccess: () => {
                alert('Xóa phân công thành công!');
            },
            onError: () => {
                alert('Có lỗi xảy ra khi xóa phân công!');
            }
        });
    }
};

const handleFileChange = (e) => {
    form.file = e.target.files[0];
};

const handleImport = () => {
    form.post(route('tbm.ctdsdangky.import', {
        id_ds_dang_ky: props.dsdangky.id,
    }), {
        onSuccess: () => {
            showImportModal.value = false;
            alert('Import dữ liệu thành công!');
            form.reset();
            setTimeout(() => {
                router.reload();
            }, 1000);
            
        },
        onError: () => {
            alert('Có lỗi xảy ra khi import dữ liệu');
        }
    });
};

// Hàm xử lý tạo biên bản
const handleCreateBienBan = () => {
    if (selectedCTs.value.length === 0) {
        alert('Vui lòng chọn ít nhất một chi tiết để tạo biên bản!');
        return;
    }
    
    router.get(route('tbm.dsbienban.create'), {
        ct_ds_dang_ky_ids: selectedCTs.value
    }, {
        onError: () => {
            alert('Có lỗi xảy ra khi chuyển trang!');
        }
    });
};

// Hàm để xác định class cho badge trạng thái
const getStatusBadgeClass = (status) => {
    const classes = 'badge ';
    switch (status) {
        case 'Approved':
            return classes + 'bg-success';
        case 'Rejected':
            return classes + 'bg-danger';
        case 'Pending':
            return classes + 'bg-info';
        case 'Completed':
            return classes + 'bg-primary';
        case 'Draft':
        default:
            return classes + 'bg-secondary';
    }
};

</script>

<template>
    <AppLayout role="tbm">
        <template v-slot:sub-link>
            <li class="breadcrumb-item">
                <Link :href="route('tbm.dsdangky.index')">Danh sách đăng ký</Link>
            </li>
            <li class="breadcrumb-item active">Học kì {{dsdangky.hoc_ki }} - {{dsdangky.nam_hoc}}</li>
        </template>

        <template v-slot:content>
            <div class="content">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">CHI TIẾT DANH SÁCH XÂY DỰNG NGÂN HÀNG </h3>
                        <div class="d-flex gap-2" v-if="can_create">
                            <button 
                                disabled
                                class="btn btn-sm btn-success me-2"
                            >
                                <i class="fas fa-plus"></i>Thêm mới
                            </button>
                            <button 
                                disabled
                                    class="btn btn-success me-2" 
                                    @click="showImportModal = true"
                                    title="Import từ Excel"
                                >
                                    <i class="fas fa-file-import"></i> Import
                                </button>
                            
                        </div>
                        <div class="d-flex gap-2" v-else>
                            <Link 
                                :href="route('tbm.ctdsdangky.create', dsdangky.id)"
                                class="btn btn-sm btn-success me-2"
                            >
                                <i class="fas fa-plus"></i> Thêm mới
                            </Link>
                            <button 
                               
                                    class="btn btn-success me-2" 
                                    @click="showImportModal = true"
                                    title="Import từ Excel"
                                >
                                    <i class="fas fa-file-import"></i> Import
                                </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="mb-3 d-flex justify-content-between align-items-center">
                            <div class="form-check">
                                <input 
                                    type="checkbox" 
                                    class="form-check-input" 
                                    id="selectAll"
                                    v-model="selectAll"
                                    @change="toggleSelectAll"
                                >
                                <label class="form-check-label" for="selectAll">
                                    Chọn tất cả
                                </label>
                            </div>
                            <button 
                                class="btn btn-primary"
                                :disabled="!canCreateBienBan"
                                @click="handleCreateBienBan"
                            >
                                <i class="fas fa-file-alt"></i> Tạo biên bản họp
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>
                                            <input 
                                                type="checkbox" 
                                                v-model="selectAll"
                                                @change="toggleSelectAll"
                                            >
                                        </th>
                                        <th>STT</th>
                                        <th>Học phần</th>
                                        <th>Viên chức</th>
                                        <th>Số lượng</th>
                                        <th>Loại ngân hàng</th>
                                        <th>Hình thức thi</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="chitiet.length === 0">
                                        <td colspan="9" class="text-center">
                                            Không có dữ liệu
                                        </td>
                                    </tr>
                                    <tr v-for="(ct, index) in chitiet" :key="ct.id">
                                        <td>
                                            <input 
                                                type="checkbox" 
                                                v-model="selectedCTs"
                                                :value="ct.id"
                                                :disabled="hasBienBan(ct.id)"
                                            >
                                        </td>
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ ct.hoc_phan }}</td>
                                        <td>{{ ct.vien_chuc }}</td>
                                        <td>{{ ct.so_luong }}</td>
                                        <td>{{ ct.loai_ngan_hang }}</td>

                                        <td>{{ ct.hinh_thuc_thi }}</td>
                                        <td>
                                            <span :class="getStatusBadgeClass(ct.trang_thai)">
                                                {{ ct.trang_thai }}
                                            </span>
                                        </td>
                                        <td>
                                            <button 
                                                :disabled="ct.trang_thai !== 'Draft' && ct.trang_thai !== 'Rejected'"
                                                @click="handleEdit(ct.id)"
                                                class="btn btn-sm btn-warning me-2"
                                            >
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button 
                                                :disabled="ct.trang_thai !== 'Draft' && ct.trang_thai !== 'Rejected'"
                                                @click="handleDelete(ct.id)"
                                                class="btn btn-sm btn-danger me-2"
                                            >
                                                <i class="fas fa-trash"></i>
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
    </AppLayout>

    <!-- Import Modal -->
    <div class="modal fade" :class="{ 'show d-block': showImportModal }" tabindex="-1" v-if="showImportModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import từ Excel</h5>
                    <button type="button" class="btn-close" @click="showImportModal = false"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Chọn file Excel</label>
                        <input 
                            type="file" 
                            class="form-control" 
                            accept=".xlsx,.xls"
                            @change="handleFileChange"
                        >
                    </div>
                    <div class="alert alert-info">
                        <h6>Cấu trúc file Excel:</h6>
                        <p>Dòng 1: Tiêu đề các cột</p>
                        <p>Các cột bắt buộc:</p>
                        <ul>
                            <li>id_hoc_phan: Mã học phần</li>
                            <li>id_vien_chuc: Mã viên chức</li>
                            <li>so_gio: Số giờ</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="showImportModal = false">Đóng</button>
                    <button 
                        type="button" 
                        class="btn btn-primary" 
                        @click="handleImport"
                        :disabled="form.processing"
                    >
                        Import
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" v-if="showImportModal"></div>
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

.btn-danger-delete {
    background-color: #dc3545;
    color: white;
}

.btn-danger-delete:hover {
    background-color: #c82333;
    color: white;
}

.table th {
    background-color: #f8f9fa;
    color: #495057;
}

.badge {
    padding: 0.5em 0.8em;
    font-size: 0.85em;
}

.modal-backdrop {
    background-color: rgba(0, 0, 0, 0.5);
}

.form-check-input:checked {
    background-color: #28a745;
    border-color: #28a745;
}

.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}

.btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
}

.btn-primary:disabled {
    background-color: #ccc;
    border-color: #ccc;
}
</style>