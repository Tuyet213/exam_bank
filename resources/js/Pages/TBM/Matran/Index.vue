<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    allHocPhans: Array,
    hocPhans: Array,
    filters: Object,
    role: String
});

const search = ref(props.filters?.search || '');

watch(search, (val) => {
    router.get(route('tbm.matran.index'), { search: val }, { preserveState: true, replace: true });
});

const goTo = (routeName, id) => {
    router.visit(route(routeName, id));
};

const deleteMatran = (id) => {
    if (confirm('Bạn có chắc chắn muốn xóa ma trận này?')) {
        router.delete(route('tbm.matran.destroy', id));
    }
};

const exportDe = (hocPhanId) => {
    
    router.get(route('tbm.matran.export', hocPhanId));
    
};
</script>
<template>
  <AppLayout :role="role">
    <template #sub-link>
      <li class="breadcrumb-item active">Ma trận đề thi</li>
    </template>
    <template #content>
      <div class="card mb-4 ">
        <div class="card-header bg-success-tb text-white">
          <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-0">DANH SÁCH MA TRẬN ĐỀ THI</h3>
            <button class="btn btn-light" @click="() => router.get(route('tbm.matran.create'))">
              <i class="fas fa-plus"></i> Tạo ma trận mới
            </button>
          </div>
        </div>
        <div class="card-body">
          <!-- Bộ tìm kiếm học phần -->
          <div class="mb-4">
            <label class="font-bold mb-1">Tìm kiếm học phần</label>
            <input v-model="search" class="form-control" placeholder="Nhập tên hoặc mã học phần..." />
          </div>

          <!-- Danh sách học phần đã có ma trận -->
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead class="table-light">
                <tr>
                  <th>STT</th>
                  <th>Mã học phần</th>
                  <th>Tên học phần</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(hp, idx) in hocPhans" :key="hp.id">
                  <td>{{ idx + 1 }}</td>
                  <td>{{ hp.id }}</td>
                  <td>{{ hp.ten }}</td>
                  <td>
                    <button class="btn btn-info btn-sm me-1" @click="goTo('tbm.matran.show', hp.id)"><i class="fas fa-eye"></i></button>
                    <button class="btn btn-warning btn-sm me-1" @click="goTo('tbm.matran.edit', hp.id)"><i class="fas fa-edit"></i></button>
                    
                    <button class="btn btn-secondary btn-sm" @click="exportDe(hp.id)"><i class="fas fa-file-export"></i></button>
                  </td>
                </tr>
                <tr v-if="hocPhans.length === 0">
                  <td colspan="5" class="text-center">Không có dữ liệu</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </template>
  </AppLayout>
</template>
