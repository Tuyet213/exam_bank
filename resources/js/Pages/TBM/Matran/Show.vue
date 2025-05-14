<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { computed } from 'vue';

const props = defineProps({
  hocPhan: Object,
  chuongs: Array,
  cdrs: Array,
  giao: Array,
  bang: Object,
  id: [String, Number],
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
</script>
<template>
  <AppLayout :role="role">
    <template #sub-link>
      <li class="breadcrumb-item"><a :href="route('tbm.matran.index')">Danh sách ma trận</a></li>
      <li class="breadcrumb-item active">Chi tiết ma trận</li>
    </template>
    <template #content>
      <div class="card mb-4">
        <div class="card-header">
          <h3 class="mb-0">CHI TIẾT MA TRẬN ĐỀ THI</h3>
        </div>
        <div class="card-body">
          <div class="mb-4">
            <b>Mã học phần:</b> {{ hocPhan.id }}<br>
            <b>Tên học phần:</b> {{ hocPhan.ten }}
          </div>
          <div v-if="chuongs.length && cdrs.length" class="mb-4 overflow-x-auto">
            <table class="table-auto border w-full">
              <thead>
                <tr>
                  <th class="border px-2 py-1 align-bottom" rowspan="2" style="width: 120px;">Chương/Chủ đề</th>
                  <th class="border px-2 py-1 text-center" :colspan="cdrs.length">CDR</th>
                </tr>
                <tr>
                  <th v-for="cdr in cdrs" :key="cdr.id" class="border px-2 py-1 text-center">{{ cdr.ten }}</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="chuong in chuongs" :key="chuong.id">
                  <td class="border px-2 py-1 text-center">{{ chuong.ten }}</td>
                  <td v-for="cdr in cdrs" :key="cdr.id" class="border px-2 py-1 text-center">
                    <template v-if="isGiao(chuong.id, cdr.id)">
                      <div class="flex flex-col items-center gap-1">
                        <input :value="bang?.[chuong.id]?.[cdr.id]?.[1] || 0" type="number" readonly class="w-25 text-center border rounded mb-1 bg-light" />
                        <input :value="bang?.[chuong.id]?.[cdr.id]?.[2] || 0" type="number" readonly class="w-25 text-center border rounded mb-1 bg-light" />
                        <input :value="bang?.[chuong.id]?.[cdr.id]?.[3] || 0" type="number" readonly class="w-25 text-center border rounded bg-light" />
                      </div>
                    </template>
                    <template v-else>
                      <div class="bg-gray-300 h-16 w-full flex items-center justify-center"></div>
                    </template>
                  </td>
                </tr>
                <tr>
                  <td class="border px-2 py-1 font-bold text-center">Tổng số câu hỏi</td>
                  <td v-for="cdr in cdrs" :key="cdr.id" class="border px-2 py-1 text-center font-bold">
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
        </div>
      </div>
    </template>
  </AppLayout>
</template>
