<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/vue3";
import { router } from "@inertiajs/vue3";
import { ref, watch } from "vue";

const { gioQuyDois, message, success } = defineProps({
    gioQuyDois: {
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
});

console.log(gioQuyDois);

// Biến cho chức năng tìm kiếm
const searchTerm = ref("");
const selectedLoaiDeThi = ref("");
const selectedLoaiHanhDong = ref("");
const debounceTimeout = ref(null);

const deleteGioQuyDoi = (id) => {
    console.log("Bắt đầu xử lý xóa, ID:", id);
    const confirmed = confirm("Bạn có chắc chắn muốn xóa Giờ Quy Đổi này?");
    console.log("Kết quả confirm:", confirmed);
    if (confirmed) {
        console.log("Gửi yêu cầu xóa cho ID:", id);
        router.delete(route("admin.gioquydoi.destroy", id), {
            onSuccess: () => {
                console.log("Xóa thành công");
                alert("Giờ Quy Đổi đã được xóa thành công!");
            },
            onError: (errors) => {
                console.log("Xóa thất bại", errors);
                alert("Có lỗi xảy ra khi xóa Giờ Quy Đổi!");
                console.error(errors);
            },
        });
    } else {
        console.log("Hủy xóa, không gửi yêu cầu");
    }
};

// Hàm thực hiện tìm kiếm
const performSearch = () => {
    if (debounceTimeout.value) {
        clearTimeout(debounceTimeout.value);
    }
    debounceTimeout.value = setTimeout(() => {
        router.get(
            route("admin.gioquydoi.index"),
            {
                search: searchTerm.value,
                loai_de_thi: selectedLoaiDeThi.value,
                loai_hanh_dong: selectedLoaiHanhDong.value,
            },
            {
                preserveState: true,
                replace: true,
            }
        );
    }, 300); // Delay 300ms
};

console.log(gioQuyDois);
console.log(searchTerm.value);
console.log(selectedLoaiDeThi.value);
console.log(selectedLoaiHanhDong.value);
console.log(debounceTimeout.value);

// Theo dõi thay đổi của các biến tìm kiếm
watch([searchTerm, selectedLoaiDeThi, selectedLoaiHanhDong], () => {
    performSearch();
});
</script>

<template>
    <AppLayout role="admin">
        <template v-slot:sub-link>
            <li class="breadcrumb-item active">
                <a :href="route('admin.gioquydoi.index')">Giờ Quy Đổi</a>
            </li>
        </template>
        <template v-slot:content>
            <div class="content">
                <div class="card">
                    <div class="card-header bg-success-tb text-white p-4">
                        <div
                            class="row justify-content-between align-items-center"
                        >
                            <div class="col-md-8">
                                <h3 class="mb-0 font-weight-bolder">
                                    DANH SÁCH GIỜ QUY ĐỔI
                                </h3>
                            </div>
                            <div class="col-md-4 text-end">
                                <Link
                                    :href="route('admin.gioquydoi.create')"
                                    class="btn btn-light"
                                >
                                    <i class="fas fa-plus"></i> Thêm mới
                                </Link>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pb-0">
                        <div class="row mb-4">
                            <div class="col-md-4 col-sm-12 mb-3">
                                <div class="input-group">
                                    <input
                                        type="text"
                                        v-model="searchTerm"
                                        placeholder="Tìm theo ID"
                                        class="form-control"
                                    />
                                </div>
                            </div>
                            <!-- <div class="col-md-4 col-sm-12 mb-3">
                                <select
                                    v-model="selectedLoaiDeThi"
                                        class="form-control"
                                    >
                                        <option value="">
                                            Tất cả Loại đề thi
                                        </option>
                                        <option value="0">Trắc nghiệm</option>
                                        <option value="1">Tự luận</option>
                                        <option value="2">
                                            Trắc nghiệm + Tự luận
                                        </option>
                                    </select>
                                </div> -->
                            <div class="col-md-4 col-sm-12 mb-3">
                                <select v-model="selectedLoaiHanhDong" class="form-control">
                                    <option value="">Tất cả Loại hành động</option>
                                    <option value="0">Biên soạn</option>
                                    <option value="1">Họp phản biện</option>
                                    
                                </select>
                            </div>
                           
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <!-- <th>Loại đề thi</th> -->
                                        <th>Loại hành động</th>
                                        <th>Giờ</th>
                                        <th>Số lượng</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="gioQuyDois.data.length === 0">
                                        <td colspan="6" class="text-center">
                                            Không có dữ liệu
                                        </td>
                                    </tr>
                                    <tr
                                        v-for="gioQuyDoi in gioQuyDois.data"
                                        :key="gioQuyDoi.id"
                                    >
                                        <td>{{ gioQuyDoi?.id }}</td>
                                        <!-- <td>
                                            {{
                                                gioQuyDoi?.loai_de_thi == 0
                                                    ? "Trắc nghiệm"
                                                    : gioQuyDoi?.loai_de_thi ==
                                                      1
                                                    ? "Tự luận"
                                                    : "Trắc nghiệm + Tự luận"
                                            }}
                                        </td> -->
                                        <td>
                                            {{
                                                gioQuyDoi?.loai_hanh_dong == 0
                                                    ? "Biên soạn"
                                                    : "Họp phản biện"
                                            }}
                                        </td>
                                        <td>{{ gioQuyDoi?.gio }}</td>
                                        <td>{{ gioQuyDoi?.so_luong }}</td>
                                        <td>
                                            <Link
                                                :href="
                                                    route(
                                                        'admin.gioquydoi.edit',
                                                        gioQuyDoi.id
                                                    )
                                                "
                                                class="btn btn-sm btn-success-edit me-2"
                                            >
                                                <i class="fas fa-edit"></i>
                                            </Link>
                                            <button
                                                class="btn btn-sm btn-danger-delete"
                                                @click="
                                                    deleteGioQuyDoi(
                                                        gioQuyDoi.id
                                                    )
                                                "
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
                                <li
                                    class="page-item"
                                    :class="{
                                        disabled: gioQuyDois.current_page === 1,
                                    }"
                                >
                                    <Link
                                        :href="gioQuyDois.links[0]?.url || '#'"
                                        class="page-link rounded-circle"
                                        :class="{
                                            'disabled-link':
                                                !gioQuyDois.links[0]?.url,
                                        }"
                                    >
                                        <i class="fas fa-chevron-left"></i>
                                    </Link>
                                </li>
                                <li
                                    v-for="link in gioQuyDois.links.slice(
                                        1,
                                        -1
                                    )"
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
                                    :class="{
                                        disabled:
                                            gioQuyDois.current_page ===
                                            gioQuyDois.last_page,
                                    }"
                                >
                                    <Link
                                        :href="
                                            gioQuyDois.links[
                                                gioQuyDois.links.length - 1
                                            ]?.url || '#'
                                        "
                                        class="page-link rounded-circle"
                                        :class="{
                                            'disabled-link':
                                                !gioQuyDois.links[
                                                    gioQuyDois.links.length - 1
                                                ]?.url,
                                        }"
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
    </AppLayout>
</template>

<style scoped>
.btn-success-edit {
    background-color: #f0ad4e;
    color: white;
    border-radius: 0;
}

.btn-danger-delete {
    background-color: #d9534f;
    color: white;
    border-radius: 0;
}

</style>

