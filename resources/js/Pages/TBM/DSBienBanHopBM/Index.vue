<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link, useForm, router } from "@inertiajs/vue3";
import { ref, watch } from 'vue';

const props = defineProps({
    ds_bien_ban: {
        type: Object,
        required: true
    },
    ds_bien_ban_hierarchy: {
        type: Object,
        default: () => ({})
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

// State cho collapse/expand của các phần
const expandedNamHoc = ref({});
const expandedHocKi = ref({});

const hocKi = ref(props.filters.hoc_ki || '');
const namHoc = ref(props.filters.nam_hoc || '');
const debounceTimeout = ref(null);

const handleSearch = () => {
    if (debounceTimeout.value) {
        clearTimeout(debounceTimeout.value);
    }
    
    debounceTimeout.value = setTimeout(() => {
        router.get(
            route('tbm.dsbienban.index'),
            { 
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

// Theo dõi sự thay đổi của các trường lọc và tự động áp dụng
watch([hocKi, namHoc], () => {
    handleSearch();
});

// Toggle expand/collapse cho năm học
const toggleNamHoc = (namHoc) => {
    expandedNamHoc.value[namHoc] = !expandedNamHoc.value[namHoc];
};

// Toggle expand/collapse cho học kỳ
const toggleHocKi = (namHoc, hocKi) => {
    const key = `${namHoc}_${hocKi}`;
    expandedHocKi.value[key] = !expandedHocKi.value[key];
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('vi-VN');
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
            router.post(route('tbm.dsbienban.upload-noi-dung', bienBan.id), formData, {
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

const sendNotification = (bienBanId) => {
    if (confirm('Bạn có chắc chắn muốn gửi thông báo hoàn thành biên soạn đến Phòng ĐBCL?')) {
        router.post(route('tbm.dsbienban.send-notification', bienBanId), {}, {
            onSuccess: () => {
                alert('Đã gửi thông báo hoàn thành biên soạn đến Phòng ĐBCL thành công!');
            },
            onError: (errors) => {
                alert('Có lỗi xảy ra: ' + (errors.message || 'Không thể gửi thông báo'));
                console.error(errors);
            }
        });
    }
};
</script>

<template>
    <AppLayout role="tbm">
        <template v-slot:sub-link>
            <li class="breadcrumb-item active">
                <a :href="route('tbm.dsbienban.index')">Danh sách biên bản họp</a>
            </li>
        </template>

        <template v-slot:content>
            <div class="content">
                <div class="card border-radius-lg shadow-lg animated-fade-in">
                    <div class="card-header bg-success-tb text-white p-4 d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">DANH SÁCH BIÊN BẢN HỌP BỘ MÔN</h3>
                    </div>

                    <div class="card-body pb-0">
                        <!-- Form tìm kiếm -->
                        <div class="row mb-4">
                            <div class="col-md-6">
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
                            <div class="col-md-6">
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
                    </div>

                    <div class="card-body">
                        <div class="thongke-content">
                            <!-- Danh sách trống -->
                            <div v-if="Object.keys(ds_bien_ban_hierarchy).length === 0" class="text-center py-5">
                                <h5 class="text-muted">Không có dữ liệu biên bản họp</h5>
                                <p>Vui lòng chọn các tiêu chí lọc khác để xem danh sách</p>
                            </div>
                            
                            <!-- Danh sách phân cấp: năm học -> học kỳ -> danh sách -->
                            <div v-else class="accordion accordion-custom">
                                <div v-for="(namData, namHoc) in ds_bien_ban_hierarchy" :key="namHoc" class="accordion-item">
                                    <!-- Năm học -->
                                    <div class="accordion-header" @click="toggleNamHoc(namHoc)">
                                        <div class="accordion-button" :class="{ 'collapsed': !expandedNamHoc[namHoc] }">
                                            <i class="fas" :class="expandedNamHoc[namHoc] ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                                            <span class="ms-2">Năm học: {{ namData.ten }}</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Nội dung của năm học -->
                                    <div class="accordion-collapse" :class="{ 'show': expandedNamHoc[namHoc] }">
                                        <div class="accordion-body">
                                            <!-- Danh sách học kỳ -->
                                            <div v-for="(hocKiData, hocKi) in namData.hoc_ki" :key="`${namHoc}_${hocKi}`" class="accordion-item ms-4 mt-2">
                                                <!-- Học kỳ -->
                                                <div class="accordion-header" @click="toggleHocKi(namHoc, hocKi)">
                                                    <div class="accordion-button" :class="{ 'collapsed': !expandedHocKi[`${namHoc}_${hocKi}`] }">
                                                        <i class="fas" :class="expandedHocKi[`${namHoc}_${hocKi}`] ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                                                        <span class="ms-2">{{ hocKiData.ten }}</span>
                                                    </div>
                                                </div>
                                                
                                                <!-- Nội dung của học kỳ -->
                                                <div class="accordion-collapse" :class="{ 'show': expandedHocKi[`${namHoc}_${hocKi}`] }">
                                                    <div class="accordion-body">
                                                        <div class="table-responsive">
                                                            <table class="table table-hover">
                                                                <thead>
                                                                    <tr>
                                                                        <th>STT</th>
                                                                        <th>Học phần</th>
                                                                        <th>Giảng viên</th>
                                                                        <th>Địa điểm</th>
                                                                        <th>File biên bản</th>
                                                                        <th>Trạng thái</th>
                                                                        <th>Thao tác</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr v-if="hocKiData.danh_sach.length === 0">
                                                                        <td colspan="7" class="text-center">
                                                                            Không có dữ liệu
                                                                        </td>
                                                                    </tr>
                                                                    <tr v-for="(bb, index) in hocKiData.danh_sach" :key="bb.id">
                                                                        <td>{{ index + 1 }}</td>
                                                                        <td>{{ bb.ct_d_s_dang_ky?.hoc_phan?.ten }}</td>
                                                                        <td>{{ bb.ct_d_s_dang_ky?.ds_g_v_bien_soans?.map(gv => gv?.vien_chuc?.name || 'Không có tên').join(', ') || 'Chưa có giảng viên' }}</td>
                                                                        <td>{{ bb.dia_diem }}</td>
                                                                        <td class="text-center">
                                                                            <a 
                                                                                v-if="bb.noi_dung"
                                                                                :href="route('tbm.dsbienban.download', bb.id)"
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
                                                                            <span 
                                                                                class="badge"
                                                                                :class="{
                                                                                    'bg-secondary': bb.trang_thai === 'Draft', 
                                                                                    'bg-warning': bb.trang_thai === 'Pending',
                                                                                    'bg-success': bb.trang_thai === 'Approved',
                                                                                    'bg-danger': bb.trang_thai === 'Rejected'
                                                                                }"
                                                                            >
                                                                                {{ bb.trang_thai || 'Draft' }}
                                                                            </span>
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <div class="btn-group">
                                                                                <Link 
                                                                                    :href="bb.trang_thai === 'Draft' || bb.trang_thai === 'Rejected' ? route('tbm.dsbienban.edit', bb.id) : '#'"
                                                                                    class="btn btn-sm btn-warning me-2"
                                                                                    :class="{ 'disabled': !(bb.trang_thai === 'Draft' || bb.trang_thai === 'Rejected') }"
                                                                                    title="Chỉnh sửa"
                                                                                >
                                                                                    <i class="fas fa-edit"></i>
                                                                                </Link>
                                                                                <Link 
                                                                                    :href="bb.trang_thai === 'Draft' || bb.trang_thai === 'Rejected' ? route('tbm.dsbienban.edit-so-gio', bb.id) : '#'"
                                                                                    class="btn btn-sm btn-secondary me-2"
                                                                                    :class="{ 'disabled': !(bb.trang_thai === 'Draft' || bb.trang_thai === 'Rejected') }"
                                                                                    title="Chỉnh sửa số giờ"
                                                                                >
                                                                                    <i class="fas fa-clock"></i>
                                                                                </Link>
                                                                                <button 
                                                                                    class="btn btn-sm btn-info me-2"
                                                                                    title="Thêm nội dung"
                                                                                    @click="bb.trang_thai === 'Draft' || bb.trang_thai === 'Rejected' ? uploadNoiDung(bb) : ''"
                                                                                    :disabled="!(bb.trang_thai === 'Draft' || bb.trang_thai === 'Rejected')"
                                                                                >
                                                                                    <i class="fas fa-file-pdf"></i>
                                                                                </button>
                                                                                <button 
                                                                                    v-if="bb.noi_dung !='' && bb.ct_d_s_dang_ky?.ds_g_v_bien_soans?.some(gv => gv.so_gio > 0)"
                                                                                    class="btn btn-sm btn-success me-2"
                                                                                    title="Gửi thông báo đến P.ĐBCL"
                                                                                    @click="bb.trang_thai === 'Draft' || bb.trang_thai === 'Rejected' ? sendNotification(bb.id) : ''"
                                                                                    :disabled="!(bb.trang_thai === 'Draft' || bb.trang_thai === 'Rejected')"
                                                                                >
                                                                                    <i class="fas fa-paper-plane"></i>
                                                                                </button>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div v-if="Object.keys(namData.hoc_ki).length === 0" class="text-center py-3">
                                                <p class="text-muted">Không có học kỳ nào</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </AppLayout>
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

.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.table {
    width: 100%;
    margin-bottom: 1rem;
    color: #212529;
    vertical-align: top;
    border-color: #dee2e6;
}

.table > :not(caption) > * > * {
    padding: 0.5rem;
    border-bottom-width: 1px;
}

.table th {
    background-color: #f8f9fa;
    color: #495057;
    font-weight: 600;
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

.btn-warning {
    background-color: #ffc107;
    color: #212529;
}

.btn-warning:hover {
    background-color: #e0a800;
    color: #212529;
}

.bg-success-tb {
    background-color: #5cb85c;
}

.accordion-custom .accordion-item {
    border: 1px solid #e9ecef;
    border-radius: 0.5rem;
    margin-bottom: 0.5rem;
    overflow: hidden;
}

.accordion-custom .accordion-header {
    padding: 0;
    cursor: pointer;
}

.accordion-custom .accordion-button {
    padding: 1rem;
    background-color: #f8f9fa;
    display: flex;
    align-items: center;
    font-weight: 500;
    color: #333;
    border: none;
    width: 100%;
    text-align: left;
}

.accordion-custom .accordion-button.collapsed {
    background-color: white;
}

.accordion-custom .accordion-button:not(.collapsed) {
    background-color: #f0f3f5;
    color: #2c3e50;
    box-shadow: none;
}

.accordion-custom .accordion-collapse {
    transition: all 0.2s ease;
    max-height: 0;
    overflow: hidden;
}

.accordion-custom .accordion-collapse.show {
    max-height: 5000px;
}

.accordion-custom .accordion-body {
    padding: 1rem;
}

.animated-fade-in {
    animation: fadeIn 0.5s;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.form-select:focus,
.form-control:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.form-label {
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.disabled {
    opacity: 0.6;
    cursor: not-allowed;
    pointer-events: none;
}

button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style> 