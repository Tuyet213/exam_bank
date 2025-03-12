<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Link } from "@inertiajs/vue3";
import { router } from '@inertiajs/vue3';
import { ref, watch } from "vue";

const { lophocphans, khoas, hoc_phans, message, success } = defineProps({
    lophocphans: {
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
    khoas: {
        type: Array,
        required: true,
        default: () => [],
    },
    hoc_phans: {
        type: Array,
        required: true,
        default: () => [],
    },
    message: {
        type: String,
        default: "",
    },
    success: {
        type: Boolean,
        default: undefined,
    },
});

// Biến cho chức năng tìm kiếm
const selectedKyHoc = ref("");
const selectedNamHoc = ref("");
const selectedKhoa = ref("");
const selectedHocPhan = ref("");
const searchTerm = ref("");
const debounceTimeout = ref(null);

const deleteLopHocPhan = (id) => {
    const confirmed = confirm('Bạn có chắc chắn muốn xóa Lớp học phần này?');
    if (confirmed) {
        router.delete(route('admin.lophocphan.destroy', id), {
            onSuccess: () => {
                alert('Lớp học phần đã được xóa thành công!');
            },
            onError: (errors) => {
                alert('Có lỗi xảy ra khi xóa Lớp học phần!');
                console.error(errors);
            },
        });
    }
};

// Hàm thực hiện tìm kiếm
const performSearch = () => {
    if (debounceTimeout.value) {
        clearTimeout(debounceTimeout.value);
    }
    debounceTimeout.value = setTimeout(() => {
        router.get(
            route("admin.lophocphan.index"),
            {
                ky_hoc: selectedKyHoc.value,
                nam_hoc: selectedNamHoc.value,
                id_khoa: selectedKhoa.value,
                id_hoc_phan: selectedHocPhan.value,
                search: searchTerm.value,
            },
            {
                preserveState: true,
                replace: true,
            }
        );
    }, 300); // Delay 300ms
};

// Theo dõi thay đổi của các biến tìm kiếm
watch([selectedKyHoc, selectedNamHoc, selectedKhoa, selectedHocPhan, searchTerm], () => {
    performSearch();
});
</script>

<template>
    <AdminLayout>
        <template v-slot:sub-link>
            <li class="breadcrumb-item active">
                <a :href="route('admin.lophocphan.index')">Lớp học phần</a>
            </li>
        </template>
        <template v-slot:content>
            <div class="content">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">Lớp học phần</h3>
                        <div class="d-flex flex-column gap-2 w-md-auto my-2 my-md-0 mr-2" style="width: 50%;">
                            <!-- Hàng 1: Học kỳ và Năm học -->
                             <div><input type="text" v-model="searchTerm" placeholder="Tìm theo ID hoặc tên Lớp học phần hoặc tên Giảng viên" class="form-control"></div>
                            <div class="d-flex flex-column flex-md-row gap-2">
                                <select v-model="selectedKyHoc" class="form-control" >
                                    <option value="">Tất cả Học kỳ</option>
                                    <option value="1">Học kỳ 1</option>
                                    <option value="2">Học kỳ 2</option>
                                    <option value="3">Học kỳ 3</option>
                                </select>
                                <select v-model="selectedNamHoc" class="form-control" >
                                    <option value="">Tất cả Năm học</option>
                                    <option v-for="year in Array.from({length: 10}, (_, i) => new Date().getFullYear() - i)" :key="year" :value="year">
                                        {{ year }}
                                    </option>
                                </select>
                            </div>
                            <!-- Hàng 2: Khoa và Học phần -->
                            <div class="d-flex flex-column flex-md-row gap-2">
                                <select v-model="selectedKhoa" class="form-control" >
                                    <option value="">Tất cả Khoa</option>
                                    <option v-for="khoa in khoas" :key="khoa.id" :value="khoa.id">
                                        {{ khoa.ten }}
                                    </option>
                                </select>
                                <select v-model="selectedHocPhan" class="form-control" >
                                    <option value="">Tất cả Học phần</option>
                                    <option v-for="hocphan in hoc_phans" :key="hocphan.id" :value="hocphan.id">
                                        {{ hocphan.ten }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <Link :href="route('admin.lophocphan.create')" class="btn btn-success-add">
                            <i class="fas fa-user-plus"></i> Add Lớp học phần
                        </Link>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Lớp học phần</th>
                                        <th>Kỳ học</th>
                                        <th>Năm học</th>
                                        <th>Khoa</th>
                                        <th>Học phần</th>
                                        <th>Giảng viên</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="lophocphans.data.length === 0">
                                        <td colspan="7" class="text-center">
                                            Không có dữ liệu
                                        </td>
                                    </tr>
                                    <tr v-for="lophocphan in lophocphans.data" :key="lophocphan.id">
                                        <td>{{ lophocphan.id }}</td>
                                        <td>{{ lophocphan.ten }}</td>
                                        <td>{{ lophocphan.ky_hoc }}</td>
                                        <td>{{ lophocphan.nam_hoc }}</td>
                                        <td>{{ lophocphan.khoa.ten }}</td>
                                        <td>{{ lophocphan.hoc_phan.ten }}</td>
                                        <td>{{ lophocphan.vien_chuc.name }}</td>
                                        <td>
                                            <Link
                                                :href="route('admin.lophocphan.edit', lophocphan.id)"
                                                class="btn btn-sm btn-success-edit me-2"
                                            >
                                                <i class="fas fa-edit"></i>
                                            </Link>
                                            <button
                                                class="btn btn-sm btn-danger-delete"
                                                @click="deleteLopHocPhan(lophocphan.id)"
                                            >
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Phân trang -->
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center mt-3">
                                <li class="page-item" :class="{ disabled: lophocphans.current_page === 1 }">
                                    <Link
                                        :href="lophocphans.links[0]?.url || '#'"
                                        class="page-link rounded-circle"
                                        :class="{ 'disabled-link': !lophocphans.links[0]?.url }"
                                    >
                                        <i class="fas fa-chevron-left"></i>
                                    </Link>
                                </li>
                                <li
                                    v-for="link in lophocphans.links.slice(1, -1)"
                                    :key="link.label"
                                    class="page-item"
                                    :class="{ active: link.active }"
                                >
                                    <Link
                                        :href="link.url || '#'"
                                        class="page-link rounded-circle"
                                        :class="{ 'active-page': link.active }"
                                    >
                                        {{ link.label }}
                                    </Link>
                                </li>
                                <li
                                    class="page-item"
                                    :class="{ disabled: lophocphans.current_page === lophocphans.last_page }"
                                >
                                    <Link
                                        :href="lophocphans.links[lophocphans.links.length - 1]?.url || '#'"
                                        class="page-link rounded-circle"
                                        :class="{ 'disabled-link': !lophocphans.links[lophocphans.links.length - 1]?.url }"
                                    >
                                        <i class="fas fa-chevron-right"></i>
                                    </Link>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </template>
    </AdminLayout>
</template>
