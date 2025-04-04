<script setup>
import TBMLayout from "@/Layouts/TBMLayout.vue";
import { Link, router } from "@inertiajs/vue3";
import { ref } from 'vue';
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
    can_create: {
        type: Boolean,
        required: true
    },
    success: {
        type: String,
        required: true
    },
    error: {
        type: String,
        required: true
    }
});


const showImportModal = ref(false);
const form = useForm({
    file: null
});

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

</script>

<template>
    <TBMLayout>
        <template v-slot:sub-link>
            <li class="breadcrumb-item">
                <Link :href="route('tbm.dsdangky.index')">Danh sách đăng ký</Link>
            </li>
            <li class="breadcrumb-item active">{{ dsdangky.ten }}</li>
        </template>

        <template v-slot:content>
            <div class="content">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">CHI TIẾT DANH SÁCH PHÂN CÔNG</h3>
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
                       

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Học phần</th>
                                        <th>Viên chức</th>
                                        <th>Số giờ</th>
                                        <th>Trạng thái</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="chitiet.length === 0">
                                        <td colspan="7" class="text-center">
                                            Không có dữ liệu
                                        </td>
                                    </tr>
                                    <tr v-for="(ct, index) in chitiet" :key="ct.id">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ ct.hoc_phan }}</td>
                                        <td>{{ ct.vien_chuc }}</td>
                                        <td>{{ ct.so_gio }}</td>
                                        <td>
                                            <span 
                                                class="badge"
                                                :class="{
                                                    'bg-warning': ct.trang_thai === 'Draft',
                                                    'bg-info': ct.trang_thai === 'Pending',
                                                    'bg-success': ct.trang_thai === 'Approved',
                                                    'bg-danger': ct.trang_thai === 'Rejected'
                                                }"
                                            >
                                                {{ ct.trang_thai }}
                                            </span>
                                        </td>
                                        <td>
                                            <button 
                                                :disabled="ct.trang_thai !== 'Draft'"
                                                @click="handleEdit(ct.id)"
                                                class="btn btn-sm btn-warning me-2"
                                            >
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button 
                                                :disabled="ct.trang_thai !== 'Draft'"
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
    </TBMLayout>

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
</style>