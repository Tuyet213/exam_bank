<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { reactive, computed, watch } from 'vue';

const props = defineProps({
  hocPhans: Array,
  chuongs: Array,
  cdrs: Array,
  giao: Array,
  selectedHocPhan: [String, Number, null],
  loai_ky: String,
  role: String
});

const form = reactive({
  hoc_phan: props.selectedHocPhan || '',
  loai_ky: props.loai_ky || 'cuoi_ky',
  bang: {}, // {chuongId: {cdrId: {1: số, 2: số, 3: số}}}
});

const isGiao = (chuongId, cdrId) => {
  if (!props.giao || !Array.isArray(props.giao)) return false;
  return props.giao.some(([c, d]) => c === chuongId && d === cdrId);
};

const tongSoCau = (cdrId, muc) => {
  let sum = 0;
  props.chuongs.forEach(ch => {
    if (form.bang[ch.id] && form.bang[ch.id][cdrId]) {
      sum += Number(form.bang[ch.id][cdrId][muc] || 0);
    }
  });
  return sum;
};

const onHocPhanChange = (e) => {
  form.hoc_phan = e.target.value;
  router.visit(route('matran.create', { 
    hoc_phan_id: form.hoc_phan,
    loai_ky: form.loai_ky
  }), { 
    preserveState: true,
    onSuccess: () => {
      setTimeout(() => {
        initBang();
      }, 100); // Đợi một chút để đảm bảo dữ liệu đã được cập nhật
    }
  });
};

const onLoaiKyChange = (e) => {
  form.loai_ky = e.target.value;
  router.visit(route('matran.create', { 
    hoc_phan_id: form.hoc_phan,
    loai_ky: form.loai_ky
  }), { preserveState: true });
};

// Khởi tạo bảng nhập liệu khi props.chuongs/cdrs/giao thay đổi
const initBang = () => {
  form.bang = {};
  if (!props.chuongs || !props.cdrs || !Array.isArray(props.chuongs) || !Array.isArray(props.cdrs)) return;
  
  props.chuongs.forEach(ch => {
    if (!ch || !ch.id) return;
    form.bang[ch.id] = {};
    props.cdrs.forEach(cdr => {
      if (!cdr || !cdr.id) return;
      if (isGiao(ch.id, cdr.id)) {
        form.bang[ch.id][cdr.id] = { 1: 0, 2: 0, 3: 0 };
      }
    });
  });
};

initBang();

const submit = () => {
  router.post(route('matran.store'), form);
};

watch(() => form.hoc_phan, (newVal, oldVal) => {
  if (newVal && newVal !== oldVal) {
    router.get(route('matran.create', { 
      hoc_phan_id: newVal,
      loai_ky: form.loai_ky
    }), { preserveState: true });
  }
});

watch(() => [props.chuongs, props.cdrs, props.giao], () => {
  initBang();
}, { deep: true, immediate: false });
</script>
<template>
  <AppLayout :role="role">
    <template #sub-link>
      <li class="breadcrumb-item active">
        <a :href="route('matran.index', { loai_ky: form.loai_ky })">Danh sách ma trận</a>
      </li>
    </template>
    <template #content>
      <div class="card mb-4">
        <div class="card-header bg-success-tb text-white">
          <h3 class="mb-0">TẠO MA TRẬN ĐỀ THI</h3>
        </div>
        <div class="card-body">
          <form @submit.prevent="submit">
            <div class="mb-3">
              <label class="form-label fw-bold">Loại kỳ</label>
              <div class="d-flex">
                <div class="form-check me-4">
                  <input class="form-check-input" type="radio" v-model="form.loai_ky" id="loai_ky_giua" value="giua_ky" @change="onLoaiKyChange">
                  <label class="form-check-label" for="loai_ky_giua">
                    Giữa kỳ
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" v-model="form.loai_ky" id="loai_ky_cuoi" value="cuoi_ky" @change="onLoaiKyChange">
                  <label class="form-check-label" for="loai_ky_cuoi">
                    Cuối kỳ
                  </label>
                </div>
              </div>
            </div>
            
            <div class="mb-4">
              <label class="block font-bold mb-1">Học phần</label>
              <select v-model="form.hoc_phan" class="form-control" required @change="onHocPhanChange">
                <option value="">Chọn học phần</option>
                <option v-for="hp in props.hocPhans" :key="hp.id" :value="hp.id">{{ hp.ten }}</option>
              </select>
            </div>
            <div v-if="props.chuongs.length && props.cdrs.length" class="mb-4 overflow-x-auto">
              <table class="table-auto border w-full">
                <thead>
                  <tr>
                    <th class="border px-2 py-1 align-bottom" rowspan="2" style="width: 120px;">Chương/Chủ đề</th>
                    <th class="border px-2 py-1 text-center" :colspan="props.cdrs.length">CDR</th>
                  </tr>
                  <tr>
                    <th v-for="cdr in props.cdrs" :key="cdr.id" class="border px-2 py-1 text-center">{{ cdr.ten }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="chuong in props.chuongs" :key="chuong.id">
                    <td class="border px-2 py-1 text-center">{{ chuong.ten }}</td>
                    <td v-for="cdr in props.cdrs" :key="cdr.id" class="border px-2 py-1 text-center">
                      <template v-if="isGiao(chuong.id, cdr.id)">
                        <div class="flex flex-col items-center gap-1">
                          <input v-model.number="form.bang[chuong.id][cdr.id][1]" type="number" min="0" placeholder="Dễ" class="w-25 text-center border rounded mb-1" />
                          <input v-model.number="form.bang[chuong.id][cdr.id][2]" type="number" min="0" placeholder="TB" class="w-25 text-center border rounded mb-1" />
                          <input v-model.number="form.bang[chuong.id][cdr.id][3]" type="number" min="0" placeholder="Khó" class="w-25 text-center border rounded" />
                        </div>
                      </template>
                      <template v-else>
                        <div class="bg-gray-300 h-16 w-full flex items-center justify-center"></div>
                      </template>
                    </td>
                  </tr>
                  <tr>
                    <td class="border px-2 py-1 font-bold text-center">Tổng số câu hỏi</td>
                    <td v-for="cdr in props.cdrs" :key="cdr.id" class="border px-2 py-1 text-center font-bold">
                      <span v-for="muc in [1,2,3]" :key="muc" class="mr-1">
                        {{ tongSoCau(cdr.id, muc) }}<span v-if="muc<3">/</span>
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
              <div class="mt-2 italic text-sm text-gray-600">
                <b>Ghi chú:</b> (1) Số lượng câu hỏi Dễ, mức 1; (2) Số lượng câu hỏi Trung bình, mức 2; (3) Số lượng câu hỏi Khó, mức 3.
              </div>
            </div>
            <button type="submit" class="btn btn-success mt-4">Lưu ma trận</button>
          </form>
        </div>
      </div>
    </template>
  </AppLayout>
</template>
