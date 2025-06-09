<script setup>
import { Head, Link, usePage } from "@inertiajs/vue3";
import { ref, onMounted, onUnmounted, computed } from "vue";

const props = defineProps({
    role: {
        type: String,
        default: "admin", // Mặc định là admin
        validator: (value) =>
            ["admin", "gv", "dbcl", "tbm", "tk"].includes(value),
    },
    title: {
        type: String,
        default: "",
    },
});

const roleTitle = computed(() => {
    const titles = {
        admin: "Admin",
        gv: "Giảng viên",
        dbcl: "Nhân viên P.ĐBCL",
        tbm: "Trưởng Bộ Môn",
        tk: "Trưởng Khoa",
    };
    return props.title || titles[props.role] || "Dashboard";
});

const isSidebarVisible = ref(false);
const isMobile = ref(false);

const checkMobile = () => {
    isMobile.value = window.innerWidth <= 768;
    if (isMobile.value) {
        isSidebarVisible.value = false;
    } else {
        isSidebarVisible.value = true;
    }
};

const toggleSidebar = () => {
    isSidebarVisible.value = !isSidebarVisible.value;
};

onMounted(() => {
    checkMobile();
    window.addEventListener("resize", checkMobile);
});

onUnmounted(() => {
    window.removeEventListener("resize", checkMobile);
});

const page = usePage();
const userPermissions = computed(() => page.props.auth.permissions || []);
</script>

<template>
    <div class="admin-dashboard">
        <Head :title="roleTitle" />

        <!-- Sidebar -->
        <div class="sidebar" :class="{ 'sidebar-hidden': !isSidebarVisible }">
            <div class="sidebar-header">
                <h4>{{ roleTitle }}</h4>
                <button
                    class="btn btn-close-sidebar"
                    @click="toggleSidebar"
                    v-show="isMobile"
                >
                    <i class="fas fa-arrow-left"></i>
                </button>
            </div>

            <!-- Admin Menu -->

            <ul v-if="role === 'admin'" class="sidebar-menu">
                <li>
                    <i class="fas fa-building"></i>
                    <Link :href="route('admin.khoa.index')">Khoa</Link>
                </li>
                <li>
                    <i class="fas fa-building"></i>
                    <Link :href="route('admin.bomon.index')">Bộ môn</Link>
                </li>
                <li>
                    <i class="fas fa-building"></i>
                    <Link :href="route('admin.chucvu.index')">Chức vụ</Link>
                </li>
                <li>
                    <i class="fas fa-building"></i>
                    <Link :href="route('admin.bacdaotao.index')"
                        >Bậc đào tạo</Link
                    >
                </li>
                <li>
                    <i class="fas fa-building"></i>
                    <Link :href="route('admin.hocphan.index')">Học phần</Link>
                </li>
                <li>
                    <i class="fas fa-building"></i>
                    <Link :href="route('admin.chuandaura.index')"
                        >Chuẩn đầu ra</Link
                    >
                </li>
                <li>
                    <i class="fas fa-building"></i>
                    <Link :href="route('admin.lophocphan.index')"
                        >Lớp học phần</Link
                    >
                </li>
                <li>
                    <i class="fas fa-building"></i>
                    <Link :href="route('admin.nhiemvu.index')">Nhiệm vụ</Link>
                </li>
                <li>
                    <i class="fas fa-building"></i>
                    <Link :href="route('admin.gioquydoi.index')"
                        >Giờ quy đổi</Link
                    >
                </li>
                <li>
                    <i class="fas fa-users"></i>
                    <Link :href="route('admin.user.index')">Users</Link>
                </li>
                <!-- <li>
                    <i class="fas fa-question-circle"></i>
                    <Link :href="route('cauhoi.hocphan')"
                        >Danh sách câu hỏi</Link
                    >
                </li> -->
                <li>
                    <i class="fas fa-chart-bar"></i>
                    <Link :href="route('thongke.index')"
                        >Thống kê</Link
                    >
                </li>
                <!-- <li>
                    <i class="fas fa-chart-bar"></i>
                    <Link :href="route('thongke_giang_vien.index')"
                        >Thống kê giảng viên biên soạn</Link
                    >
                </li>
                <li>
                    <i class="fas fa-chart-bar"></i>
                    <Link :href="route('thongkehocphan.index')"
                        >Thống kê học phần biên soạn</Link
                    >
                </li> -->
               
            </ul>

            <!-- Giảng viên Menu -->
            <ul v-else-if="role === 'gv'" class="sidebar-menu">
                <li>
                    <i class="fas fa-question-circle"></i>
                    <Link :href="route('cauhoi.hocphan')"
                        >Danh sách câu hỏi</Link
                    >
                </li>
                <li>
                    <i class="fas fa-file-export"></i>
                    <Link :href="route('dethi.hocphan')"
                        >Danh sách đề thi</Link
                    >
                </li>
                <li>
                    <i class="fas fa-chart-bar"></i>
                    <Link :href="route('matran.index')"
                        >Ma trận đề thi</Link
                    >
                </li>
            </ul>

            <!-- Nhân viên P.ĐBCL Menu -->
            <ul v-else-if="role === 'dbcl'" class="sidebar-menu">
                <!-- <li v-if="userPermissions.includes('Duyệt danh sách đăng ký')">
                    <Link :href="route('quality.dsdangky.index')"
                        >Duyệt danh sách đăng ký</Link
                    >
                </li> -->
                <li v-if="userPermissions.includes('Duyệt danh sách đăng ký')">
                    <i class="fas fa-users"></i>
                    <Link :href="route('quality.thongbao.index')"
                        >Thông báo quy định</Link
                    >
                </li>
                <!-- <li  v-if="userPermissions.includes('Duyệt danh sách đăng ký')">
                    <i class="fas fa-users"></i>
                    <Link :href="route('qlo.notice.create')"
                        >Thông báo quy định</Link
                    >
                </li> -->
                <li  v-if="userPermissions.includes('Duyệt danh sách đăng ký')">
                    <i class="fas fa-users"></i>
                    <Link :href="route('quality.dsdangky.index')"
                        >Danh sách đăng ký</Link
                    >
                </li>
                <li  v-if="userPermissions.includes('Duyệt danh sách đăng ký')">
                    <i class="fas fa-users"></i>
                    <Link :href="route('quality.dsbienban.index')"
                        >Danh sách biên nghiệm thu</Link
                    >
                </li>
                <li  v-if="userPermissions.includes('Duyệt danh sách đăng ký')">
                    <i class="fas fa-chart-bar"></i>
                    <Link :href="route('thongke.index')"
                        >Thống kê</Link
                    >
                </li>
                <!-- <li  v-if="userPermissions.includes('Duyệt danh sách đăng ký')">
                    <i class="fas fa-chart-bar"></i>
                    <Link :href="route('thongke_giang_vien.index')"
                        >Thống kê giảng viên biên soạn</Link
                    >
                </li>
                <li  v-if="userPermissions.includes('Duyệt danh sách đăng ký')">
                    <i class="fas fa-chart-bar"></i>
                    <Link :href="route('thongkehocphan.index')"
                        >Thống kê học phần biên soạn</Link
                    >
                </li> -->
            </ul>

            <!-- Trưởng Khoa Menu -->
            <ul v-else-if="role === 'tk'" class="sidebar-menu">
                <li>
                    <i class="fas fa-users"></i>
                    <Link :href="route('tk.dsdangky.index')"
                        >Danh sách đăng ký biên soạn</Link
                    >
                </li>
                <li>
                    <i class="fas fa-users"></i>
                    <Link :href="route('tk.dsbienban.index')"
                        >Danh sách biên bản họp cấp khoa</Link
                    >
                </li>
            </ul>

            <!-- Trưởng Bộ Môn Menu -->
            <ul v-else-if="role === 'tbm'" class="sidebar-menu">
                <li>
                    <i class="fas fa-users"></i>
                    <Link :href="route('tbm.dsdangky.index')"
                        >Danh sách đăng ký biên soạn</Link
                    >
                </li>

                <li>
                    <i class="fas fa-users"></i>
                    <Link :href="route('tbm.dsbienban.index')"
                        >Danh sách biên bản nghiệm thu</Link
                    >
                </li>
                <li>
                    <i class="fas fa-question-circle"></i>
                    <Link :href="route('cauhoi.hocphan')"
                        >Danh sách câu hỏi</Link
                    >
                </li>
                <li>
                    <i class="fas fa-file-export"></i>
                    <Link :href="route('dethi.hocphan')"
                        >Danh sách đề thi</Link
                    >
                </li>
                <li>
                    <i class="fas fa-chart-bar"></i>
                    <Link :href="route('matran.index')"
                        >Ma trận đề thi</Link
                    >
                </li>
                <li>
                    <i class="fas fa-file-export"></i>
                    <Link :href="route('trich-xuat-de-thi.index')"
                        >Trích xuất đề thi</Link
                    >
                </li>
            </ul>

            <!-- Chung cho tất cả các menu -->
            <div class="sidebar-footer">
                <!-- <template v-if="role === 'admin'">
                    <Link
                        class="btn btn-success w-100 mt-3"
                        :href="route('dashboard')"
                        >Client Page</Link
                    >
                </template> -->
                <Link
                    class="btn btn-success w-100 mt-3"
                    :href="route('logout')"
                    method="post"
                    as="button"
                    >Logout</Link
                >
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <button
                    class="btn btn-toggle-sidebar"
                    @click="toggleSidebar"
                    v-show="isMobile"
                >
                    <i class="fas fa-bars"></i>
                </button>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="#">{{ roleTitle }}</a>
                        </li>
                        <slot name="sub-link" />
                    </ol>
                </nav>
                <slot name="message" />
            </div>

            <main>
                <slot name="content" />
            </main>

            <!-- Footer -->
            <footer class="footer">
                <!-- <p>© 2025, made with ❤️ by HTNGOCTUYET.</p> 
                <ul>
                    <li><a href="#">Creative Tim</a></li>
                    <li><a href="#">UPDIVISION</a></li>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">License</a></li>
                </ul>-->
            </footer>
        </div>
    </div>
</template>

<style scoped>
/* layout */
.admin-dashboard {
    display: flex;
    min-height: 100vh;
}

.sidebar {
    width: 250px;
    background-color: #2a2f45;
    color: white;
    padding: 20px;
    position: fixed;
    height: 100%;
    overflow-y: auto;
    transition: transform 0.3s ease;
    z-index: 1000;
}

.sidebar-hidden {
    transform: translateX(-100%);
}

.btn-close-sidebar {
    background: none;
    border: none;
    color: white;
    font-size: 1.2rem;
    cursor: pointer;
    position: absolute;
    right: 10px;
    top: 10px;
    padding: 0;
}

.btn-close-sidebar:hover {
    color: #ddd;
}

.main-content {
    flex: 1;
    padding: 20px;
    background-color: #f4f7fe;
    margin-left: 0;
}

.btn-toggle-sidebar {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: #333;
    cursor: pointer;
    padding: 0;
    margin-right: 10px;
}

@media (min-width: 769px) {
    .main-content {
        margin-left: 250px;
    }
    .sidebar {
        transform: translateX(0);
    }
    .btn-toggle-sidebar {
        display: none;
    }
}

@media (max-width: 768px) {
    .sidebar {
        width: 250px;
    }
    .main-content {
        margin-left: 0;
    }
}

.sidebar-header h4 {
    font-size: 1.25rem;
    margin-bottom: 30px;
}

.sidebar-menu {
    list-style: none;
    padding: 0;
}

.sidebar-menu li {
    padding: 10px 0;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1rem;
    cursor: pointer;
}

.sidebar-menu li.active {
    background-color: rgba(255, 255, 255, 0.1);
    border-radius: 5px;
}

.sidebar-footer {
    margin-top: 50px;
}

.sidebar-footer h5 {
    font-size: 0.9rem;
    margin-bottom: 10px;
}

.sidebar-footer ul {
    list-style: none;
    padding: 0;
}

.sidebar-footer ul li {
    padding: 10px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.btn-success {
    background-color: #00c853;
    border: none;
}

.btn-success:hover {
    background-color: #00b140;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.footer {
    margin-top: 50px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #999;
    font-size: 0.875rem;
}
</style>
