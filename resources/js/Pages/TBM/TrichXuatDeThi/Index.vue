<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    hocPhans: Array,
    filters: Object,
    loai_ky: String,
    role: String
});

const search = ref(props.filters.search || '');
const loai_ky = ref(props.loai_ky || 'cuoi_ky');

const handleSearch = () => {
    router.get(route('trich-xuat-de-thi.index'), {
        search: search.value,
        loai_ky: loai_ky.value
    }, { preserveState: true, replace: true });
};

const handleLoaiKyChange = () => {
    router.get(route('trich-xuat-de-thi.index'), {
        search: search.value,
        loai_ky: loai_ky.value
    }, { preserveState: true, replace: true });
};

watch(loai_ky, () => {
    handleLoaiKyChange();
});

const goToTrichXuat = (hocPhanId) => {
    router.visit(route('trich-xuat-de-thi.show', { 
        id: hocPhanId,
        loai_ky: loai_ky.value
    }));
};
</script>

<template>
  <AppLayout :role="role">
    <template #sub-link>
      <li class="breadcrumb-item active">Trích xuất đề thi</li>
    </template>
    <template #content>
      <div class="card mb-4">
        <div class="card-header bg-success-tb text-white">
          <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-0">TRÍCH XUẤT ĐỀ THI</h3>
            <div class="text-white">
              <i class="fas fa-file-export me-2"></i>
              Dành cho Trưởng Bộ Môn
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="mb-4 d-flex justify-content-between align-items-center row">
            <div class="d-flex col-md-6">
              <input 
                type="text" 
                v-model="search" 
                placeholder="Tìm kiếm theo tên hoặc mã học phần..." 
                class="form-control me-2" 
                @keyup.enter="handleSearch"
              >
              <button @click="handleSearch" class="btn btn-primary">
                <i class="fas fa-search"></i> Tìm
              </button>
            </div>
            
            <div class="d-flex align-items-center col-md-6 justify-content-end">
              <label class="me-2 mb-0 fw-bold">Loại kỳ:</label>
              <div class="form-check form-check-inline">
                <input 
                  class="form-check-input" 
                  type="radio" 
                  id="loai_ky_giua" 
                  v-model="loai_ky" 
                  value="giua_ky"
                >
                <label class="form-check-label" for="loai_ky_giua">Giữa kỳ</label>
              </div>
              <div class="form-check form-check-inline">
                <input 
                  class="form-check-input" 
                  type="radio" 
                  id="loai_ky_cuoi" 
                  v-model="loai_ky" 
                  value="cuoi_ky"
                >
                <label class="form-check-label" for="loai_ky_cuoi">Cuối kỳ</label>
              </div>
            </div>
          </div>

         

          <!-- Danh sách học phần có ma trận -->
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead class="table-light">
                <tr>
                  <th class="text-center">STT</th>
                  <th>Mã học phần</th>
                  <th>Tên học phần</th>
                  <th class="text-center">Loại kỳ</th>
                  <th class="text-center">Thao tác</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(hp, idx) in hocPhans" :key="hp.id">
                  <td class="text-center">{{ idx + 1 }}</td>
                  <td>
                    <span>{{ hp.id }}</span>
                  </td>
                  <td>
                    <strong>{{ hp.ten }}</strong>
                  </td>
            
                  <td class="text-center">
                    <span class="badge" :class="loai_ky === 'giua_ky' ? 'bg-warning' : 'bg-success'">
                      {{ loai_ky === 'giua_ky' ? 'Giữa kỳ' : 'Cuối kỳ' }}
                    </span>
                  </td>
                  <td class="text-center">
                    <button 
                      @click="goToTrichXuat(hp.id)" 
                      class="btn btn-success btn-sm"
                      title="Trích xuất đề thi"
                    >
                      <i class="fas fa-file-export"></i> Trích xuất đề thi
                    </button>
                  </td>
                </tr>
                <tr v-if="hocPhans.length === 0">
                  <td colspan="6" class="text-center py-4">
                    <div class="text-muted">
                      <i class="fas fa-inbox fa-2x mb-2"></i>
                      <p class="mb-0">
                        Không có học phần nào có ma trận {{ loai_ky === 'giua_ky' ? 'giữa kỳ' : 'cuối kỳ' }} 
                        thuộc bộ môn của bạn
                      </p>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          
        </div>
      </div>
    </template>
  </AppLayout>
</template>

<style scoped>
.table th {
  background-color: #f8f9fa;
  font-weight: 600;
}

.table tbody tr:hover {
  background-color: #f5f5f5;
}

.badge {
  font-size: 0.875em;
}

.btn-success:hover {
  transform: translateY(-1px);
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.alert-info {
  border-left: 4px solid #17a2b8;
}


</style>
