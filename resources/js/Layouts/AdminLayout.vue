<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';

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
  window.addEventListener('resize', checkMobile);
});

onUnmounted(() => {
  window.removeEventListener('resize', checkMobile);
});
</script>

<template>
  <div class="admin-dashboard">
    <Head title="Admin Dashboard" />

    <!-- Sidebar -->
    <div class="sidebar" :class="{ 'sidebar-hidden': !isSidebarVisible }">
      <div class="sidebar-header">
        <h4>MATERIAL DASHBOARD 2</h4>
        <button class="btn btn-close-sidebar" @click="toggleSidebar" v-show="isMobile">
          <i class="fas fa-arrow-left"></i>
        </button>
      </div>
      <ul class="sidebar-menu">
        <li><i class="fas fa-tachometer-alt"></i> <Link :href="route('admin.dashboard')">Dashboard</Link></li>
        <li><i class="fas fa-building"></i> <Link :href="route('admin.khoa.index')">Khoa</Link></li>
        <li><i class="fas fa-building"></i> <Link :href="route('admin.bomon.index')">Bộ môn</Link></li>
        <li><i class="fas fa-building"></i> <Link :href="route('admin.chucvu.index')">Chức vụ</Link></li>
        <li><i class="fas fa-building"></i> <Link :href="route('admin.bacdaotao.index')">Bậc đào tạo</Link></li>
        <li><i class="fas fa-building"></i> <Link :href="route('admin.hocphan.index')">Học phần</Link></li>
        <li><i class="fas fa-building"></i> <Link :href="route('admin.chuandaura.index')">Chuẩn đầu ra</Link></li>
        <li><i class="fas fa-building"></i> <Link :href="route('admin.lophocphan.index')">Lớp học phần</Link></li>
        <li><i class="fas fa-building"></i> <Link :href="route('admin.nhiemvu.index')">Nhiệm vụ</Link></li>
        <li><i class="fas fa-building"></i> <Link :href="route('admin.gioquydoi.index')">Giờ quy đổi</Link></li>
        <li><i class="fas fa-users"></i> <Link :href="route('admin.user.index')">Users</Link></li>
        
      </ul>
      <div class="sidebar-footer">
        <Link class="btn btn-success w-100 mt-3" :href="route('dashboard')">Client Page</Link>
        
        <Link class="btn btn-success w-100 mt-3" :href="route('logout')" method="post" as="button">Logout</Link>
      </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <!-- Header -->
      <div class="header">
        <button class="btn btn-toggle-sidebar" @click="toggleSidebar" v-show="isMobile">
          <i class="fas fa-bars"></i>
        </button>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Admin</a></li>
            <slot name="sub-link" />
          </ol>
        </nav>
        <slot name="message" />
      
      </div>

      
      <main>
        <slot name="content"/>
      </main>

      <!-- Footer -->
      <footer class="footer">
        <p>© 2025, made with ❤️ by HTNGOCTUYET.</p>
        <ul>
          <li><a href="#">Creative Tim</a></li>
          <li><a href="#">UPDIVISION</a></li>
          <li><a href="#">About Us</a></li>
          <li><a href="#">Blog</a></li>
          <li><a href="#">License</a></li>
        </ul>
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