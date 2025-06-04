<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  hocPhan: Object,
  chuongs: Array,
  cdrs: Array,
  giao: Array,
  bang: Object,
  id: [String, Number],
  soDe: [String, Number, null],
  dsDe: Array,
  role: String,
  loai_ky: String,
  loaiDe: String
});

const soDe = ref(props.soDe || '');
const loaiDe = ref(props.loaiDe || 'trac_nghiem');
const loaiDeOptions = [
  { value: 'trac_nghiem', label: 'Trắc nghiệm' },
  { value: 'tu_luan_van_dap', label: 'Tự luận và vấn đáp' }
];

const submit = () => {
  if (!soDe.value || isNaN(soDe.value) || soDe.value <= 0) {
    alert('Vui lòng nhập số lượng đề hợp lệ!');
    return;
  }
  if (!loaiDe.value) {
    alert('Vui lòng chọn loại đề thi!');
    return;
  }
  
  router.get(route('trich-xuat-de-thi.show', props.id), { 
    so_de: soDe.value, 
    loai_de: loaiDe.value,
    loai_ky: props.loai_ky
  }, { preserveState: true });
};

const getTenChuong = (id) => {
  const ch = props.chuongs.find(c => c.id == id);
  return ch ? (ch.ten || ch.ten_chuong || ch.id) : id;
};

const getTenCDR = (id) => {
  const cdr = props.cdrs.find(c => c.id == id);
  return cdr ? (cdr.mo_ta || cdr.ten || cdr.id) : id;
};

const mucDoText = (muc) => {
  if (muc == 1) return 'Dễ';
  if (muc == 2) return 'Trung bình';
  if (muc == 3) return 'Khó';
  return muc;
};

const mucDoColor = (muc) => {
  if (muc == 1) return 'success';
  if (muc == 2) return 'warning';
  if (muc == 3) return 'danger';
  return 'secondary';
};

const downloadDeFull = (idx) => {
  // Lưu dữ liệu đề thi vào localStorage
  localStorage.setItem('current_de', JSON.stringify(props.dsDe[idx]));
  localStorage.setItem('current_de_index', idx.toString());
  
  // Mở URL tải xuống trong tab mới với tham số để báo hiệu sử dụng localStorage
  window.open(route('matran.export-download-full', { 
    id: props.id, 
    de: idx + 1,
    so_de: soDe.value,
    loai_de: loaiDe.value,
    loai_ky: props.loai_ky,
    dsCauHoi: props.dsDe[idx],
    use_localstorage: 'true'
  }), '_blank');
};

const downloadDeSimple = (idx) => {
  // Lưu dữ liệu đề thi vào localStorage
  localStorage.setItem('current_de', JSON.stringify(props.dsDe[idx]));
  localStorage.setItem('current_de_index', idx.toString());
  
  // Mở URL tải xuống trong tab mới với tham số để báo hiệu sử dụng localStorage
  window.open(route('matran.export-download-simple', { 
    id: props.id, 
    de: idx + 1,
    so_de: soDe.value,
    loai_de: loaiDe.value,
    loai_ky: props.loai_ky,
    dsCauHoi: props.dsDe[idx],
    use_localstorage: 'true'
  }), '_blank');
};
</script>

<template>
  <AppLayout :role="role">
    <template #sub-link>
      <li class="breadcrumb-item">
        <a href="#" @click.prevent="goBack">Trích xuất đề thi</a>
      </li>
      <li class="breadcrumb-item active">{{ hocPhan.ten }}</li>
    </template>
    <template #content>
      <div class="card mb-4">
        <div class="card-header bg-success-tb text-white">
          <h3 class="mb-0">
            <!-- <i class="fas fa-file-export me-2"></i> -->
            TRÍCH XUẤT ĐỀ THI {{ loai_ky === 'giua_ky' ? 'GIỮA KỲ' : 'CUỐI KỲ' }}
          </h3>
        </div>
        <div class="card-body">
          <!-- Thông tin học phần -->
          <div class="row mb-4">
            <div class="col-md-12">
              <div class="card bg-light">
                <div class="card-body">
                  <h6 class="card-title">
                    <i class="fas fa-book me-2"></i>Thông tin học phần
                  </h6>
                  <p class="mb-1"><strong>Mã học phần:</strong> {{ hocPhan.id }}</p>
                  <p class="mb-1"><strong>Tên học phần:</strong> {{ hocPhan.ten }}</p>
                  <p class="mb-0"><strong>Loại kỳ thi:</strong> 
                    <span>
                      {{ loai_ky === 'giua_ky' ? 'Giữa kỳ' : 'Cuối kỳ' }}
                    </span>
                  </p>
                </div>
              </div>
            </div>
            
          </div>

          <!-- Form trích xuất -->
          <div class="card mb-4">
            <div class="card-header">
              <h5 class="mb-0">
                <i class="fas fa-cogs me-2"></i>Cấu hình trích xuất đề thi
              </h5>
            </div>
            <div class="card-body">
              <form @submit.prevent="submit" class="row g-3">
                <div class="col-md-4">
                  <label for="soDe" class="form-label fw-bold">
                    <i class="fas fa-list-ol me-1"></i>Số lượng đề cần sinh
                  </label>
                  <input 
                    id="soDe"
                    name="soDe"
                    v-model="soDe" 
                    type="number" 
                    min="1" 
                    max="50"
                    class="form-control" 
                    placeholder="Nhập số đề"
                    required
                  />
                </div>
                <div class="col-md-4">
                  <label for="loaiDe" class="form-label fw-bold">
                    <i class="fas fa-file-alt me-1"></i>Loại đề thi
                  </label>
                  <select id="loaiDe" name="loaiDe" v-model="loaiDe" class="form-select" required>
                    <option value="">-- Chọn loại đề --</option>
                    <option v-for="opt in loaiDeOptions" :key="opt.value" :value="opt.value">
                      {{ opt.label }}
                    </option>
                  </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                  <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-magic me-2"></i>Tạo đề thi
                  </button>
                </div>
              </form>
            </div>
          </div>

          <!-- Kết quả trích xuất -->
          <div v-if="dsDe && dsDe.length" class="card">
            <div class="card-header">
              <h5 class="mb-0">
                <i class="fas fa-list-check me-2"></i>
                Kết quả trích xuất {{ dsDe.length }} bộ đề
              </h5>
            </div>
            <div class="card-body">
              <div v-for="(de, idx) in dsDe" :key="idx" class="mb-4">
                <div class="card border-primary">
                  <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                      <h6 class="mb-0">
                        <i class="fas fa-file-text me-2"></i>
                        Đề số {{ idx + 1 }} ({{ de.length }} câu hỏi)
                      </h6>
                      <div>
                        <button 
                          class="btn btn-light btn-sm me-2" 
                          @click="downloadDeFull(idx)"
                          title="Tải xuống kèm đáp án"
                        >
                          <i class="fas fa-download me-1"></i>Kèm đáp án
                        </button>
                        <button 
                          class="btn btn-outline-light btn-sm" 
                          @click="downloadDeSimple(idx)"
                          title="Tải xuống đề thi đơn giản"
                        >
                          <i class="fas fa-download me-1"></i>Đề thi
                        </button>
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="row">
                      <div v-for="(cau, i) in de" :key="i" class="col-12 mb-3">
                        <div class="card border-light">
                          <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                              <h6 class="mb-0">
                                <span class="badge bg-secondary me-2">{{ i + 1 }}</span>
                                {{ cau.noi_dung || 'Nội dung câu hỏi...' }}
                              </h6>
                              <span class="badge bg-info">{{ cau.diem }}đ</span>
                            </div>
                            
                            <div class="mb-2">
                              <small class="text-muted">
                                <i class="fas fa-book me-1"></i>Chương: {{ getTenChuong(cau.id_chuong) }} |
                                <i class="fas fa-target ms-2 me-1"></i>CĐR: {{ getTenCDR(cau.id_chuan_dau_ra) }} |
                                <span class="ms-2">Mức độ: 
                                  <span class="badge" :class="`bg-${mucDoColor(cau.muc_do)}`">
                                    {{ mucDoText(cau.muc_do) }}
                                  </span>
                                </span>
                              </small>
                            </div>

                            <!-- Đáp án -->
                            <div v-if="cau.dap_ans && cau.dap_ans.length" class="mt-2">
                              <div v-if="cau.phan_loai == 0" class="row">
                                <div v-for="(da, j) in cau.dap_ans" :key="j" class="col-md-6 mb-1">
                                  <small 
                                    :class="da.is_dap_an ? 'text-success fw-bold' : 'text-muted'"
                                  >
                                    {{ String.fromCharCode(65 + j) }}. {{ da.noi_dung }}
                                    <i v-if="da.is_dap_an" class="fas fa-check-circle ms-1"></i>
                                  </small>
                                </div>
                              </div>
                              <div v-else>
                                <div v-for="(da, j) in cau.dap_ans" :key="j" class="mb-1">
                                  <small class="text-success">
                                    <i class="fas fa-arrow-right me-1"></i>
                                    Ý {{ j + 1 }}: {{ da.noi_dung }}
                                  </small>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
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
.card.border-primary {
  border-width: 2px;
}

.card.border-light {
  border-color: #e9ecef;
}

.btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.badge {
  font-size: 0.75em;
}

.alert-info {
  border-left: 4px solid #17a2b8;
}

.text-success.fw-bold {
  font-weight: 600 !important;
}

.card-body p {
  margin-bottom: 0.5rem;
}

.form-control:focus,
.form-select:focus {
  border-color: #28a745;
  box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}
</style> 