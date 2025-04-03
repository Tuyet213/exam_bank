<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { useForm } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import axios from "axios";
import { onMounted } from "vue";

const { chucvus, bomons, roles, permissions } = defineProps({
    chucvus: {
        type: Array,
        required: true,
    },
    bomons: {
        type: Array,
        required: true,
    },
    roles: {
        type: Array,
        required: true,
    },
    permissions: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    id: "",
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    sdt: "",
    dia_chi: "",
    ngay_sinh: new Date().toISOString().split('T')[0],
    gioi_tinh: true,
    id_chuc_vu: " ",
    id_bo_mon: " ",
    tinh_name: "",
    quan_name: "",
    xa_name: "",
    more_address: "",
    roles: [],
    permissions: [],
});

//ref: theo dõi thay đổi cùng vs watch (ở dưới)
const tinhs = ref([]);
const quans = ref([]);
const xas = ref([]);


const fetchTinhThanh = async () => {
    try {
        const response = await axios.get("/proxy/tinh");
        if (response.data.error === 0) {
            tinhs.value = response.data.data;
            console.log(tinhs.value);
        }
    } catch (error) {
        console.error("Error fetching provinces:", error);
    }
};

const fetchQuanHuyen = async () => {
    if (!form.tinh_name) {
        // Kiểm tra form.tinh_name thay vì form.tinh_id
        quans.value = [];
        xas.value = [];
        form.quan_name = "";
        form.xa_name = "";
        return;
    }

    try {
        const selectedTinh = tinhs.value.find(tinh => tinh.full_name === form.tinh_name);
        if (selectedTinh) {
            const response = await axios.get(`/proxy/quan/${selectedTinh.id}`);
        if (response.data.error === 0) {
            quans.value = response.data.data;
            form.quan_name = "";
            xas.value = [];
            form.xa_name = "";
        }
        }
        
    } catch (error) {
        console.error("Error fetching districts:", error);
    }
};

const fetchXaPhuong = async () => {
    if (!form.quan_name) {
        // Kiểm tra form.quan_name thay vì form.quan_id
        xas.value = [];
        form.xa_name = "";
        return;
    }

    try {
        const selectedQuan = quans.value.find(quan => quan.full_name === form.quan_name);
        if (selectedQuan) {
            const response = await axios.get(`/proxy/xa/${selectedQuan.id}`);
        if (response.data.error === 0) {
            xas.value = response.data.data;
            form.xa_name = "";
        }
        }
        
    } catch (error) {
        console.error("Error fetching wards:", error);
    }
};
watch(
    () => form.tinh_name,
    (newTinhName) => {
        form.tinh_id = newTinhName;
    }
);

watch(
    () => form.quan_name,
    (newQuanName) => {
        form.quan_id = newQuanName;
    }
);

watch(
    () => form.xa_name,
    (newXaName) => {
        form.xa_id = newXaName;
    }
);
onMounted(async () => {
    await fetchTinhThanh();
});

// Hàm submit form
const submit = () => {
    form.post(route("admin.user.store"), {
        onSuccess: () => {
            alert("Tạo tài khoản thành công!");
            form.reset();
            // Đặt lại các giá trị mặc định
            form.tinh_name = "";
            form.quan_name = "";
            form.xa_name = "";
            fetchTinhThanh(); // Tải lại danh sách tỉnh
        },
        onError: (errors) => {
            alert("Có lỗi xảy ra khi tạo tài khoản!");
            console.error(errors);
        },
    });
};
</script>

<template>
    <AdminLayout>
        <!-- Breadcrumb -->
        <template v-slot:sub-link>
            <li class="breadcrumb-item">
                <a :href="route('admin.user.index')">Người dùng</a>
            </li>
            <li class="breadcrumb-item active">Tạo tài khoản</li>
        </template>

        <!-- Nội dung chính -->
        <template v-slot:content>
            <div class="content">
                <div class="card border-radius-lg shadow-lg animated-fade-in">
                    <!-- Card Header -->
                    <div class="card-header bg-success-tb text-white p-4">
                        <h3 class="mb-0 font-weight-bolder">
                            TẠO TÀI KHOẢN MỚI
                        </h3>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body p-4">
                        <form @submit.prevent="submit">
                            <div class="mb-3">
                                <div class="row">
                                    <!-- ID -->
                                    <div
                                        class="col-md-6 col-sm-12 mb-3 form-floating"
                                    >
                                        <input
                                            v-model="form.id"
                                            type="text"
                                            id="id"
                                            class="form-control"
                                            :class="{ 'has-value': form.id }"
                                            required
                                        />
                                        <label for="id" class="form-label"
                                            >ID</label
                                        >
                                        <small
                                            v-if="form.errors.id"
                                            class="text-danger"
                                        >
                                            {{ form.errors.id }}
                                        </small>
                                    </div>

                                    <!-- Tên tài khoản -->
                                    <div
                                        class="col-md-6 col-sm-12 mb-3 form-floating"
                                    >
                                        <input
                                            v-model="form.name"
                                            type="text"
                                            id="name"
                                            class="form-control"
                                            :class="{ 'has-value': form.name }"
                                            required
                                        />
                                        <label for="name" class="form-label"
                                            >Tên tài khoản</label
                                        >
                                        <small
                                            v-if="form.errors.name"
                                            class="text-danger"
                                        >
                                            {{ form.errors.name }}
                                        </small>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Số điện thoại -->
                                    <div
                                        class="col-md-6 col-sm-12 mb-3 form-floating form-group"
                                    >
                                        <input
                                            v-model="form.sdt"
                                            type="text"
                                            id="sdt"
                                            class="form-control"
                                            :class="{ 'has-value': form.sdt }"
                                            required
                                        />
                                        <label for="sdt" class="form-label"
                                            >Số điện thoại</label
                                        >
                                        <small
                                            v-if="form.errors.sdt"
                                            class="text-danger"
                                        >
                                            {{ form.errors.sdt }}
                                        </small>
                                    </div>
                                    <!-- Email -->
                                    <div
                                        class="col-md-6 col-sm-12 mb-3 form-floating form-group"
                                    >
                                        <input
                                            v-model="form.email"
                                            type="email"
                                            id="email"
                                            class="form-control"
                                            :class="{ 'has-value': form.email }"
                                            required
                                        />
                                        <label for="email" class="form-label"
                                            >Email</label
                                        >
                                        <small
                                            v-if="form.errors.email"
                                            class="text-danger"
                                        >
                                            {{ form.errors.email }}
                                        </small>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Mật khẩu -->
                                    <div
                                        class="col-md-6 col-sm-12 mb-3 form-floating form-group"
                                    >
                                        <input
                                            v-model="form.password"
                                            type="password"
                                            id="password"
                                            class="form-control"
                                            :class="{
                                                'has-value': form.password,
                                            }"
                                            required
                                        />
                                        <label for="password" class="form-label"
                                            >Mật khẩu</label
                                        >
                                        <small
                                            v-if="form.errors.password"
                                            class="text-danger"
                                        >
                                            {{ form.errors.password }}
                                        </small>
                                    </div>

                                    <!-- Nhập lại mật khẩu -->
                                    <div
                                        class="col-md-6 col-sm-12 mb-3 form-floating form-group"
                                    >
                                        <input
                                            v-model="form.password_confirmation"
                                            type="password"
                                            id="password_confirmation"
                                            class="form-control"
                                            :class="{
                                                'has-value':
                                                    form.password_confirmation,
                                            }"
                                            required
                                        />
                                        <label
                                            for="password_confirmation"
                                            class="form-label"
                                            >Nhập lại mật khẩu</label
                                        >
                                        <small
                                            v-if="
                                                form.errors
                                                    .password_confirmation
                                            "
                                            class="text-danger"
                                        >
                                            {{
                                                form.errors
                                                    .password_confirmation
                                            }}
                                        </small>
                                    </div>
                                </div>
                                <!-- //////////////////////////////////////////Địa chỉ: Tỉnh, Quận, Xã ////////////////////////////////////-->
                                <div class="row">
                                    <div
                                        class="col-md-4 col-sm-12 mb-3 form-floating form-group"
                                    >
                                        <select
                                            v-model="form.tinh_name"
                                            @change="fetchQuanHuyen"
                                            class="form-select"
                                        >
                                            <option value="">Chọn tỉnh</option>
                                            <option
                                                v-for="tinh in tinhs"
                                                :key="tinh.id"
                                                :value="tinh.full_name"
                                            >
                                                {{ tinh.full_name }}
                                            </option>
                                        </select>
                                        
                                        <small
                                            v-if="form.errors.tinh_name"
                                            class="text-danger"
                                        >
                                            {{ form.errors.tinh_name }}
                                        </small>
                                    </div>
                                    <div
                                        class="col-md-4 col-sm-12 mb-3 form-floating form-group"
                                    >
                                        <select
                                            v-model="form.quan_name"
                                            @change="fetchXaPhuong"
                                            class="form-select"
                                        >
                                            <option value="">Chọn quận</option>
                                            <option
                                                v-for="quan in quans"
                                                :key="quan.id"
                                                :value="quan.full_name"
                                            >
                                                {{ quan.full_name }}
                                            </option>
                                        </select>
                                       
                                        <small
                                            v-if="form.errors.quan_name"
                                            class="text-danger"
                                        >
                                            {{ form.errors.quan_name }}
                                        </small>
                                    </div>
                                    <div
                                        class="col-md-4 col-sm-12 mb-3 form-floating form-group"
                                    >
                                        <select v-model="form.xa_name" class="form-select">
                                            <option value="">Chọn xã</option>
                                            <option
                                                v-for="xa in xas"
                                                :key="xa.id"
                                                :value="xa.full_name"
                                            >
                                                {{ xa.full_name }}
                                            </option>
                                        </select>
                                       
                                        <small
                                            v-if="form.errors.xa_name"
                                            class="text-danger"
                                        >
                                            {{ form.errors.xa_name }}
                                        </small>
                                    </div>
                                </div>
                                <div class="row">
                                    <div
                                        class="col-12 mb-3 form-floating form-group"
                                    >
                                        <input
                                            v-model="form.more_address"
                                            type="text"
                                            id="more_address"
                                            class="form-control"
                                            :class="{
                                                'has-value': form.more_address,
                                            }"
                                        />
                                        <label
                                            for="more_address"
                                            class="form-label"
                                            >Địa chỉ chi tiết</label
                                        >
                                        <small
                                            v-if="form.errors.more_address"
                                            class="text-danger"
                                        >
                                            {{ form.errors.more_address }}
                                        </small>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Ngày sinh -->
                                    <div
                                        class="col-md-6 col-sm-12 mb-3 form-floating form-group"
                                    >
                                        <input
                                            v-model="form.ngay_sinh"
                                            type="date"
                                            id="ngay_sinh"
                                            class="form-control"
                                            :class="{
                                                'has-value': form.ngay_sinh,
                                            }"
                                            required
                                        />
                                        <label
                                            for="ngay_sinh"
                                            class="form-label"
                                            >Ngày sinh</label
                                        >
                                        <small
                                            v-if="form.errors.ngay_sinh"
                                            class="text-danger"
                                        >
                                            {{ form.errors.ngay_sinh }}
                                        </small>
                                    </div>

                                    <!-- Giới tính -->
                                    <div
                                        class="col-md-6 col-sm-12 mb-3 form-floating form-group"
                                    >
                                        <select
                                            v-model="form.gioi_tinh"
                                            id="gioi_tinh"
                                            class="form-control"
                                            :class="{
                                                'has-value': form.gioi_tinh,
                                            }"
                                            required
                                        >
                                            <option value="">
                                                Chọn giới tính
                                            </option>
                                            <option value="true">Nam</option>
                                            <option value="false">Nữ</option>
                                        </select>
                                        <label
                                            for="gioi_tinh"
                                            class="form-label"
                                            >Giới tính</label
                                        >
                                        <small
                                            v-if="form.errors.gioi_tinh"
                                            class="text-danger"
                                        >
                                            {{ form.errors.gioi_tinh }}
                                        </small>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Chức vụ -->
                                    <div
                                        class="col-md-6 col-sm-12 mb-3 form-floating form-group"
                                    >
                                        <select
                                            v-model="form.id_chuc_vu"
                                            id="id_chuc_vu"
                                            class="form-control"
                                            :class="{
                                                'has-value': form.id_chuc_vu,
                                            }"
                                            required
                                        >
                                            <option value="">
                                                Chọn chức vụ
                                            </option>
                                            <option
                                                v-for="chucvu in chucvus"
                                                :key="chucvu.id"
                                                :value="chucvu.id"
                                            >
                                                {{ chucvu.ten }}
                                            </option>
                                        </select>
                                        <label
                                            for="id_chucvu"
                                            class="form-label"
                                            >Chức vụ</label
                                        >
                                        <small
                                            v-if="form.errors.id_chuc_vu"
                                            class="text-danger"
                                        >
                                            {{ form.errors.id_chuc_vu }}
                                        </small>
                                    </div>

                                    <!-- Bộ môn -->
                                    <div
                                        class="col-md-6 col-sm-12 mb-3 form-floating form-group"
                                    >
                                        <select
                                            v-model="form.id_bo_mon"
                                            id="id_bo_mon"
                                            class="form-control"
                                            :class="{
                                                'has-value': form.id_bo_mon,
                                            }"
                                            required
                                        >
                                            <option value="">
                                                Chọn bộ môn
                                            </option>
                                            <option
                                                v-for="bomon in bomons"
                                                :key="bomon.id"
                                                :value="bomon.id"
                                            >
                                                {{ bomon.ten }}
                                            </option>
                                        </select>
                                        <label for="id_bomon" class="form-label"
                                            >Bộ môn</label
                                        >
                                        <small
                                            v-if="form.errors.id_bo_mon"
                                            class="text-danger"
                                        >
                                            {{ form.errors.id_bo_mon }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <!-- Thêm phần Role và Permission vào trước nút Submit -->
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <h5 class="mb-0">Vai trò (Roles)</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="role-list">
                                                <div v-for="role in roles" :key="role.id" class="form-check">
                                                    <input
                                                        type="checkbox"
                                                        :id="'role-' + role.id"
                                                        :value="role.name"
                                                        v-model="form.roles"
                                                        class="form-check-input"
                                                    />
                                                    <label :for="'role-' + role.id" class="form-check-label">
                                                        {{ role.name }}
                                                    </label>
                                                </div>
                                            </div>
                                            <small v-if="form.errors.roles" class="text-danger">
                                                {{ form.errors.roles }}
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <h5 class="mb-0">Quyền (Permissions)</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="permission-list">
                                                <div v-for="permission in permissions" :key="permission.id" class="form-check">
                                                    <input
                                                        type="checkbox"
                                                        :id="'permission-' + permission.id"
                                                        :value="permission.name"
                                                        v-model="form.permissions"
                                                        class="form-check-input"
                                                    />
                                                    <label :for="'permission-' + permission.id" class="form-check-label">
                                                        {{ permission.name }}
                                                    </label>
                                                </div>
                                            </div>
                                            <small v-if="form.errors.permissions" class="text-danger">
                                                {{ form.errors.permissions }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Nút Create -->
                            <div class="text-end">
                                <button
                                    type="submit"
                                    class="btn btn-success font-weight-bold"
                                >
                                    ADD
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </AdminLayout>
</template>
<style scoped>
.role-list,
.permission-list {
    max-height: 300px;
    overflow-y: auto;
    padding: 10px;
}
</style>