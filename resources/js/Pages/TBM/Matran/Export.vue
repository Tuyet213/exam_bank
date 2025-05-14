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
  role: String
});

const soDe = ref(props.soDe || '');
const loaiDe = ref('');
const loaiDeOptions = [
  { value: 'trac_nghiem', label: 'Trắc nghiệm' },
  { value: 'tu_luan_van_dap', label: 'Tự luận và vấn đáp' }
];

const submit = () => {
  if (!soDe.value || isNaN(soDe.value) || soDe.value <= 0) {
    alert('Vui lòng nhập số lượng đề hợp lệ!');
    return;
  }
  router.get(route('tbm.matran.export', props.id), { so_de: soDe.value, loai_de: loaiDe.value }, { preserveState: true });
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

const downloadDe = (idx) => {
  // Gửi request tải file bộ đề idx (có thể dùng window.open hoặc router.get tuỳ backend)
  window.open(route('tbm.matran.export-download', { id: props.id, de: idx + 1 }), '_blank');
};

const downloadDeFull = (idx) => {
  window.open(route('tbm.matran.export-download', { id: props.id, de: idx + 1 }), '_blank');
};

const downloadDeSimple = (idx) => {
  window.open(route('tbm.matran.export-download-simple', { id: props.id, de: idx + 1 }), '_blank');
};
</script>
<template>
  <AppLayout :role="role">
    <template #sub-link>
      <li class="breadcrumb-item"><a :href="route('tbm.matran.index')">Danh sách ma trận</a></li>
      <li class="breadcrumb-item active">Trích xuất đề thi</li>
    </template>
    <template #content>
      <div class="card mb-4">
        <div class="card-header">
          <h3 class="mb-0">TRÍCH XUẤT ĐỀ THI</h3>
        </div>
        <div class="card-body">
          <div class="mb-4">
            <b>Mã học phần:</b> {{ hocPhan.id }}<br>
            <b>Tên học phần:</b> {{ hocPhan.ten }}
          </div>
          <form @submit.prevent="submit" class="mb-4">
            <label class="font-bold mb-1">Nhập số lượng đề cần sinh</label>
            <input v-model="soDe" type="number" min="1" class="form-control d-inline-block w-auto me-2" @keyup.enter="submit" />
            <select v-model="loaiDe" class="form-select d-inline-block w-auto me-2">
              <option v-for="opt in loaiDeOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
            </select>
            <button type="submit" class="btn btn-primary">Tạo đề</button>
          </form>
          <div v-if="dsDe && dsDe.length">
            <h5>Kết quả trích xuất {{ dsDe.length }} bộ đề:</h5>
            <div v-for="(de, idx) in dsDe" :key="idx" class="mb-4 border p-2 rounded">
              <div class="d-flex align-items-center mb-2">
                <b class="me-2">Đề số {{ idx + 1 }}</b>
                <div class="ms-auto">
                  <button class="btn btn-outline-primary btn-sm me-2" @click="downloadDeFull(idx)">
                    <i class="bi bi-download"></i> Download đầy đủ
                  </button>
                  <button class="btn btn-outline-secondary btn-sm" @click="downloadDeSimple(idx)">
                    <i class="bi bi-download"></i> Download đơn giản
                  </button>
                </div>
              </div>
              <ul>
                <li v-for="(cau, i) in de" :key="i" class="mb-2">
                  <div>
                    <b>Câu {{ i + 1 }}:</b> {{ cau.noi_dung || 'Nội dung câu hỏi...' }}
                    <span class="text-muted">
                      [Chương {{ getTenChuong(cau.id_chuong) }} - CĐR {{ getTenCDR(cau.id_chuan_dau_ra) }} - Mức {{ mucDoText(cau.muc_do) }}]
                    </span>
                  </div>
                  <ul v-if="cau.dap_ans && cau.dap_ans.length" class="ms-4">
                    <template v-if="cau.phan_loai == 0">
                      <li v-for="(da, j) in cau.dap_ans" :key="j">
                        <span :style="{ fontWeight: da.is_dap_an ? 'bold' : 'normal' }">
                          {{ String.fromCharCode(65 + j) }}. {{ da.noi_dung }}
                          <span v-if="da.is_dap_an" class="text-success"> (Đáp án đúng)</span>
                        </span>
                      </li>
                    </template>
                    <template v-else-if="cau.phan_loai == 1 || cau.phan_loai == 2">
                      <li v-for="(da, j) in cau.dap_ans" :key="j">
                        <span :style="{ fontWeight: da.is_dap_an ? 'bold' : 'normal' }">
                          Ý {{ j + 1 }}. {{ da.noi_dung }}
                          
                        </span>
                      </li>
                    </template>
                    <template v-else>
                      <li v-for="(da, j) in cau.dap_ans" :key="j">
                        <span :style="{ fontWeight: da.is_dap_an ? 'bold' : 'normal' }">
                          {{ da.noi_dung }}
                          <span v-if="da.is_dap_an" class="text-success"> (Đáp án đúng)</span>
                        </span>
                      </li>
                    </template>
                  </ul>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </template>
  </AppLayout>
</template>
