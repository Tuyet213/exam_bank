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
  loai_ky: String,
  role: String
});

const isGiao = (chuongId, cdrId) => {
  return props.giao.some(([c, d]) => c === chuongId && d === cdrId);
};

const tongSoCau = (cdrId, muc) => {
  let sum = 0;
  props.chuongs.forEach(ch => {
    if (props.bang?.[ch.id]?.[cdrId]) {
      sum += Number(props.bang[ch.id][cdrId][muc] || 0);
    }
  });
  return sum;
};

const onLoaiKyChange = (e) => {
  router.visit(route('matran.show', { 
    id: props.id,
    loai_ky: e.target.value 
  }), { preserveState: false });
};

const getLoaiKyText = (loai_ky) => {
  return loai_ky === 'giua_ky' ? 'Giữa kỳ' : 'Cuối kỳ';
};
</script>

<template>
  <AppLayout :role="role">
    <template #sub-link>
      <li class="breadcrumb-item">
        <a :href="route('matran.index', { loai_ky: props.loai_ky })">Danh sách ma trận</a>
      </li>
      <li class="breadcrumb-item active">Chi tiết ma trận</li>
    </template>
    <template #content>
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between bg-success-tb text-white align-items-center">
          <h3 class="mb-0">CHI TIẾT MA TRẬN ĐỀ THI</h3>
          <div>
            <a :href="route('matran.edit', { id, loai_ky: props.loai_ky })" class="btn btn-light">Chỉnh sửa</a>
          </div>
        </div>
        <div class="card-body">
          <div class="mb-4">
            <div class="mb-2"><b>Mã học phần:</b> {{ hocPhan.id }}</div>
            <div class="mb-2"><b>Tên học phần:</b> {{ hocPhan.ten }}</div>
            <div class="mb-2"><b>Loại kỳ:</b> {{ getLoaiKyText(props.loai_ky) }}</div>
          </div>
          
          <div class="mb-3">
            <label class="form-label fw-bold">Xem theo loại kỳ</label>
            <div class="d-flex">
              <div class="form-check me-4">
                <input class="form-check-input" type="radio" :checked="props.loai_ky === 'giua_ky'" id="loai_ky_giua" value="giua_ky" @change="onLoaiKyChange">
                <label class="form-check-label" for="loai_ky_giua">
                  Giữa kỳ
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" :checked="props.loai_ky === 'cuoi_ky'" id="loai_ky_cuoi" value="cuoi_ky" @change="onLoaiKyChange">
                <label class="form-check-label" for="loai_ky_cuoi">
                  Cuối kỳ
                </label>
              </div>
            </div>
          </div>
          
          <div v-if="chuongs.length && cdrs.length" class="mb-4 overflow-x-auto">
            <table class="table-auto border w-full">
              <thead>
                <tr>
                  <th class="border px-2 py-1 align-bottom" rowspan="2">Chương/Chủ đề</th>
                  <th class="border px-2 py-1 text-center" :colspan="cdrs.length">CDR</th>
                </tr>
                <tr>
                  <th v-for="cdr in cdrs" :key="cdr.id" class="border px-2 py-1 text-center">{{ cdr.ten }}</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="chuong in chuongs" :key="chuong.id">
                  <td class="border px-2 py-1">{{ chuong.ten }}</td>
                  <td v-for="cdr in cdrs" :key="cdr.id" class="border px-2 py-1 text-center">
                    <template v-if="isGiao(chuong.id, cdr.id) && bang[chuong.id]?.[cdr.id]">
                      <div>
                        <div>Dễ: {{ bang[chuong.id][cdr.id][1] }}</div>
                        <div>TB: {{ bang[chuong.id][cdr.id][2] }}</div>
                        <div>Khó: {{ bang[chuong.id][cdr.id][3] }}</div>
                      </div>
                    </template>
                    <template v-else>
                      <div class="bg-gray-300 h-16 w-full flex items-center justify-center"></div>
                    </template>
                  </td>
                </tr>
                <tr>
                  <td class="border px-2 py-1 font-bold">Tổng số câu hỏi</td>
                  <td v-for="cdr in cdrs" :key="cdr.id" class="border px-2 py-1 text-center font-bold">
                    <span v-for="muc in [1,2,3]" :key="muc" class="mr-1">
                      {{ tongSoCau(cdr.id, muc) }}<span v-if="muc<3">/</span>
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
            <div class="mt-2 italic text-sm text-gray-600">
              <b>Ghi chú:</b> Số lượng câu hỏi theo thứ tự: Dễ / Trung bình / Khó.
            </div>
          </div>
          
          <div class="mt-4">
            <a :href="route('matran.export', { id, loai_ky: props.loai_ky })" class="btn btn-success">Xuất đề thi</a>
          </div>
        </div>
      </div>
    </template>
  </AppLayout>
</template>
