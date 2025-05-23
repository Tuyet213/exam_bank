<script setup>
import { onMounted, ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  id: Number,
  deIndex: Number,
  soDe: Number,
  loaiDe: String,
  loai_ky: String
});

const loading = ref(true);
const errorMessage = ref('');
const success = ref(false);

onMounted(() => {
  // Lấy dữ liệu từ localStorage
  const currentDe = localStorage.getItem('current_de');
  
  if (!currentDe) {
    errorMessage.value = 'Không tìm thấy dữ liệu đề thi trong bộ nhớ tạm';
    loading.value = false;
    return;
  }
  
  // Gửi POST request với dữ liệu lấy từ localStorage
  router.post(route('matran.export-download-full', { id: props.id }), {
    current_de: currentDe,
    de: props.deIndex + 1,
    so_de: props.soDe,
    loai_de: props.loaiDe,
    loai_ky: props.loai_ky
  }, {
    onSuccess: () => {
      success.value = true;
      loading.value = false;
      // Tự đóng trang này sau khi tải xuống hoàn tất
      setTimeout(() => {
        window.close();
      }, 2000);
    },
    onError: (errors) => {
      console.error(errors);
      errorMessage.value = 'Có lỗi xảy ra khi tải xuống đề thi: ' + JSON.stringify(errors);
      loading.value = false;
    }
  });
});
</script>

<template>
  <div class="flex h-screen w-screen items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
      <div v-if="loading">
        <h2 class="text-xl font-semibold mb-4 text-center">Đang tải xuống đề thi...</h2>
        <p class="text-gray-600 mb-6 text-center">Vui lòng đợi trong giây lát</p>
        <div class="flex justify-center">
          <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-blue-600"></div>
        </div>
      </div>
      
      <div v-else-if="errorMessage" class="text-center">
        <div class="text-red-600 text-5xl mb-4">
          <i class="bi bi-exclamation-circle"></i>
        </div>
        <h2 class="text-xl font-semibold mb-2 text-red-600">Lỗi tải xuống</h2>
        <p class="text-gray-700 mb-4">{{ errorMessage }}</p>
        <button 
          @click="window.close()" 
          class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition"
        >
          Đóng
        </button>
      </div>
      
      <div v-else-if="success" class="text-center">
        <div class="text-green-600 text-5xl mb-4">
          <i class="bi bi-check-circle"></i>
        </div>
        <h2 class="text-xl font-semibold mb-2 text-green-600">Tải xuống thành công!</h2>
        <p class="text-gray-700 mb-4">Nếu file không tự động tải xuống, vui lòng kiểm tra cài đặt trình duyệt của bạn.</p>
        <button 
          @click="window.close()" 
          class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition"
        >
          Đóng
        </button>
      </div>
    </div>
  </div>
</template>
