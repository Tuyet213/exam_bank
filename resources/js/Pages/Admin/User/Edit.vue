<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm } from "@inertiajs/vue3";
import { ref, watch, computed } from "vue";
import axios from "axios";
import { onMounted } from "vue";

const { chucvus, bomons, user, roles, permissions, khoas } = defineProps({
    chucvus: {
        type: Array,
        required: true,
    },
    bomons: {
        type: Array,
        required: true,
    },
    khoas: {
        type: Array,
        required: true,
    },
    user: {
        type: Object,
        required: true,
    },
    roles: {
        type: Array,
        
    },
    permissions: {
        type: Array,
        
    },
});

const form = useForm({
    id: user.id,
    name: user.name,
    email: user.email,
    sdt: user.sdt,
    dia_chi: user.dia_chi,
    ngay_sinh: user.ngay_sinh ? new Date(user.ngay_sinh).toISOString().split('T')[0] : new Date().toISOString().split('T')[0],
    gioi_tinh: user.gioi_tinh ? true : false,
    id_chuc_vu: user.id_chuc_vu,
    id_bo_mon: user.id_bo_mon,
    id_khoa: user.bo_mon?.id_khoa,
    tinh_name: user.tinh_name,
    quan_name: user.quan_name,
    xa_name: user.xa_name,
    more_address: user.more_address,
    roles: user.role_names || [], 
    permissions: user.permission_names || [],
});

const selectedKhoa = ref(user.bomon?.id_khoa || "");
const filteredBomons = computed(() => {
    if (!selectedKhoa.value) return [];
    return bomons.filter(bm => bm.id_khoa == selectedKhoa.value);
});

//ref: theo dõi thay đổi cùng vs watch
const tinhs = ref([]);
const quans = ref([]);
const xas = ref([]);

const fetchTinhThanh = async () => {
    try {
        const response = await axios.get("/proxy/tinh");
        if (response.data.error === 0) {
            tinhs.value = response.data.data;
            //console.log(tinhs.value);
        }
    } catch (error) {
        console.error("Error fetching provinces:", error);
    }
};

const fetchQuanHuyen = async () => {
    if (!form.tinh_name) {
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
    await loadInitialData();
});

// Hàm submit form
const submit = () => {
    form.gioi_tinh = form.gioi_tinh === "true" || form.gioi_tinh === true ? true : false;
    form.put(route("admin.user.update", user.id), {
        onSuccess: () => {
            alert("Cập nhật tài khoản thành công!");
            form.reset();
        },
        onError: (errors) => {
            alert("Có lỗi xảy ra khi cập nhật tài khoản!");
            console.error(errors);
        },
    });
};

// Thêm hàm mới
const loadInitialData = async () => {
    if (form.tinh_name) {
        // Load quận huyện
        const selectedTinh = tinhs.value.find(tinh => tinh.full_name === form.tinh_name);
        if (selectedTinh) {
            try {
                const response = await axios.get(`/proxy/quan/${selectedTinh.id}`);
                if (response.data.error === 0) {
                    quans.value = response.data.data;
                    
                    // Nếu có quận, load xã phường
                    if (form.quan_name) {
                        const selectedQuan = quans.value.find(quan => quan.full_name === form.quan_name);
                        if (selectedQuan) {
                            try {
                                const xaResponse = await axios.get(`/proxy/xa/${selectedQuan.id}`);
                                if (xaResponse.data.error === 0) {
                                    xas.value = xaResponse.data.data;
                                }
                            } catch (error) {
                                console.error("Error fetching wards:", error);
                            }
                        }
                    }
                }
            } catch (error) {
                console.error("Error fetching districts:", error);
            }
        }
    }
};
</script>

<template>
    <AppLayout role="admin">
        <!-- Breadcrumb -->
        <template v-slot:sub-link>
            <li class="breadcrumb-item">
                <a :href="route('admin.user.index')">Người dùng</a>
            </li>
            <li class="breadcrumb-item active">Cập nhật</li>
        </template>

        <!-- Nội dung chính -->
        <template v-slot:content>
            <div class="content">
                <div class="card border-radius-lg shadow-lg animated-fade-in">
                    <!-- Card Header -->
                    <div class="card-header bg-success-tb text-white p-4">
                        <h3 class="mb-0 font-weight-bolder">
                            CẬP NHẬT TÀI KHOẢN
                        </h3>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body p-4">
                        <form @submit.prevent="submit">
                            <div class="mb-3">
                                <div class="row">
                                    <!-- ID -->
                                    <div class="col-md-6 col-sm-12 mb-3 form-floating">
                                        <input
                                            v-model="form.id"
                                            type="text"
                                            id="id"
                                            class="form-control"
                                            :class="{ 'has-value': form.id }"
                                            required
                                            disabled
                                        />
                                        <label for="id" class="form-label">ID</label>
                                        <small v-if="form.errors.id" class="text-danger">
                                            {{ form.errors.id }}
                                        </small>
                                    </div>

                                    <!-- Tên tài khoản -->
                                    <div class="col-md-6 col-sm-12 mb-3 form-floating">
                                        <input
                                            v-model="form.name"
                                            type="text"
                                            id="name"
                                            class="form-control"
                                            :class="{ 'has-value': form.name }"
                                            required
                                        />
                                        <label for="name" class="form-label">Tên tài khoản</label>
                                        <small v-if="form.errors.name" class="text-danger">
                                            {{ form.errors.name }}
                                        </small>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Số điện thoại -->
                                    <div class="col-md-6 col-sm-12 mb-3 form-floating form-group">
                                        <input
                                            v-model="form.sdt"
                                            type="text"
                                            id="sdt"
                                            class="form-control"
                                            :class="{ 'has-value': form.sdt }"
                                            required
                                        />
                                        <label for="sdt" class="form-label">Số điện thoại</label>
                                        <small v-if="form.errors.sdt" class="text-danger">
                                            {{ form.errors.sdt }}
                                        </small>
                                    </div>
                                    <!-- Email -->
                                    <div class="col-md-6 col-sm-12 mb-3 form-floating form-group">
                                        <input
                                            v-model="form.email"
                                            type="email"
                                            id="email"
                                            class="form-control"
                                            :class="{ 'has-value': form.email }"
                                            required
                                        />
                                        <label for="email" class="form-label">Email</label>
                                        <small v-if="form.errors.email" class="text-danger">
                                            {{ form.errors.email }}
                                        </small>
                                    </div>
                                </div>

                                <!-- Địa chỉ: Tỉnh, Quận, Xã -->
                                <div class="row">
                                    <div class="col-md-4 col-sm-12 mb-3 form-floating form-group">
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
                                                :selected="tinh.full_name === form.tinh_name"
                                            >
                                                {{ tinh.full_name }}
                                            </option>
                                        </select>
                                        <small v-if="form.errors.tinh_name" class="text-danger">
                                            {{ form.errors.tinh_name }}
                                        </small>
                                    </div>
                                    <div class="col-md-4 col-sm-12 mb-3 form-floating form-group">
                                        <select
                                            v-model="form.quan_name"
                                            @change="fetchXaPhuong"
                                            class="form-select"
                                            :disabled="!form.tinh_name"
                                        >
                                            <option value="">Chọn quận</option>
                                            <option
                                                v-for="quan in quans"
                                                :key="quan.id"
                                                :value="quan.full_name"
                                                :selected="quan.full_name === form.quan_name"
                                            >
                                                {{ quan.full_name }}
                                            </option>
                                        </select>
                                        <small v-if="form.errors.quan_name" class="text-danger">
                                            {{ form.errors.quan_name }}
                                        </small>
                                    </div>
                                    <div class="col-md-4 col-sm-12 mb-3 form-floating form-group">
                                        <select 
                                            v-model="form.xa_name" 
                                            class="form-select"
                                            :disabled="!form.quan_name"
                                        >
                                            <option value="">Chọn xã</option>
                                            <option
                                                v-for="xa in xas"
                                                :key="xa.id"
                                                :value="xa.full_name"
                                                :selected="xa.full_name === form.xa_name"
                                            >
                                                {{ xa.full_name }}
                                            </option>
                                        </select>
                                        <small v-if="form.errors.xa_name" class="text-danger">
                                            {{ form.errors.xa_name }}
                                        </small>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 mb-3 form-floating form-group">
                                        <input
                                            v-model="form.more_address"
                                            type="text"
                                            id="more_address"
                                            class="form-control"
                                            :class="{ 'has-value': form.more_address }"
                                            placeholder="Nhập địa chỉ chi tiết"
                                        />
                                        <label for="more_address" class="form-label">Địa chỉ chi tiết</label>
                                        <small v-if="form.errors.more_address" class="text-danger">
                                            {{ form.errors.more_address }}
                                        </small>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Ngày sinh -->
                                    <div class="col-md-6 col-sm-12 mb-3 form-floating form-group">
                                        <input
                                            v-model="form.ngay_sinh"
                                            type="date"
                                            id="ngay_sinh"
                                            class="form-control"
                                            :class="{ 'has-value': form.ngay_sinh }"
                                            required
                                        />
                                        <label for="ngay_sinh" class="form-label">Ngày sinh</label>
                                        <small v-if="form.errors.ngay_sinh" class="text-danger">
                                            {{ form.errors.ngay_sinh }}
                                        </small>
                                    </div>

                                    <!-- Giới tính -->
                                    <div class="col-md-6 col-sm-12 mb-3 form-floating form-group">
                                        <select
                                            v-model="form.gioi_tinh"
                                            id="gioi_tinh"
                                            class="form-control"
                                            :class="{ 'has-value': form.gioi_tinh }"
                                            required
                                        >
                                            <option value="">Chọn giới tính</option>
                                            <option value="true">Nam</option>
                                            <option value="false">Nữ</option>
                                        </select>
                                        <label for="gioi_tinh" class="form-label">Giới tính</label>
                                        <small v-if="form.errors.gioi_tinh" class="text-danger">
                                            {{ form.errors.gioi_tinh }}
                                        </small>
                                    </div>
                                </div>

                                <div class="row">
                                   

                                    <!-- Khoa -->
                                    <div class="col-md-6 col-sm-12 mb-3 form-floating form-group">
                                        <select
                                            v-model="selectedKhoa"
                                            class="form-control"
                                            required
                                        >
                                            <option value="">Chọn khoa</option>
                                            <option v-for="khoa in khoas" :key="khoa.id" :value="khoa.id">
                                                {{ khoa.ten }}
                                            </option>
                                        </select>
                                        <label class="form-label">Khoa</label>
                                    </div>
                                     <!-- Bộ môn -->
                                <div class="col-md-6 col-sm-12 mb-3 form-floating form-group">
                                    <select
                                        v-model="form.id_bo_mon"
                                        id="id_bo_mon"
                                        class="form-control"
                                        :class="{ 'has-value': form.id_bo_mon }"
                                        required
                                        :disabled="!selectedKhoa"
                                    >
                                        <option value="">Chọn bộ môn</option>
                                        <option
                                            v-for="bomon in filteredBomons"
                                            :key="bomon.id"
                                            :value="bomon.id"
                                        >
                                            {{ bomon.ten }}
                                        </option>
                                    </select>
                                    <label for="id_bomon" class="form-label">Bộ môn</label>
                                    <small v-if="form.errors.id_bo_mon" class="text-danger">
                                        {{ form.errors.id_bo_mon }}
                                    </small>
                                </div>
                                </div>

                               
                                 <!-- Chức vụ -->
                                 <div class="col-md-6 col-sm-12 mb-3 form-floating form-group">
                                        <select
                                            v-model="form.id_chuc_vu"
                                            id="id_chuc_vu"
                                            class="form-control"
                                            :class="{ 'has-value': form.id_chuc_vu }"
                                            required
                                        >
                                            <option value="">Chọn chức vụ</option>
                                            <option
                                                v-for="chucvu in chucvus"
                                                :key="chucvu.id"
                                                :value="chucvu.id"
                                            >
                                                {{ chucvu.ten }}
                                            </option>
                                        </select>
                                        <label for="id_chucvu" class="form-label">Chức vụ</label>
                                        <small v-if="form.errors.id_chuc_vu" class="text-danger">
                                            {{ form.errors.id_chuc_vu }}
                                        </small>
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
                                                        :checked="form.roles.includes(role.name)"
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
                                                        :checked="form.permissions.includes(permission.name)"
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


                            <!-- Nút Update -->
                            <div class="text-end mt-4">
                                <button type="submit" class="btn btn-success font-weight-bold">
                                    Lưu
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </AppLayout>
</template>

<style scoped>


/* Thêm style mới cho form-floating */
.form-floating {
    position: relative;
    margin-bottom: 20px; /* Thêm khoảng cách dưới */
}

.form-floating > .form-control,
.form-floating > .form-select {
    height: calc(3.5rem + 2px);
    padding: 1.625rem 0.75rem 0.625rem;
}

.form-floating > label {
    position: absolute;
    top: -20px; /* Điều chỉnh khoảng cách label với input */
    left: 0;
    height: 100%;
    padding: 1rem 0.75rem;
    pointer-events: none;
    border: 1px solid transparent;
    transform-origin: 0 0;
    transition: opacity .1s ease-in-out,transform .1s ease-in-out;
    color: #6c757d;
    font-size: 0.875rem;
}

/* Style cho label khi input được focus hoặc có giá trị */
.form-floating > .form-control:focus ~ label,
.form-floating > .form-control:not(:placeholder-shown) ~ label,
.form-floating > .form-select ~ label {
    opacity: .65;
    transform: scale(.85) translateY(-0.5rem) translateX(0.15rem);
    color: #5eb562;
}

/* Style cho form-control */
.form-control {
    padding-top: 1.5rem;
    padding-bottom: 0.5rem;
    border: none;
    border-bottom: 1px solid #d1d1d1;
    border-radius: 0;
    font-size: 1rem;
    width: 100%;
    height: auto;
}

/* Style cho form-select */
.form-select {
    width: 100%;
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    line-height: 1.5;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    height: auto;
}

/* Style cho disabled states */
.form-select:disabled,
.form-control:disabled {
    background-color: #e9ecef;
    cursor: not-allowed;
    opacity: 0.7;
}

/* Style cho label khi form-control có giá trị */
.form-control.has-value + .form-label {
    top: -20px;
    transform: translateY(0);
    font-size: 0.875rem;
    color: #5eb562;
}
</style>
