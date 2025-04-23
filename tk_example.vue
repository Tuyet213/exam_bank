<script setup>
import { reactive, ref } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Table from '@/Components/Table.vue';

// Lấy thông tin người dùng hiện tại
const user = usePage().props.auth.user;
const userName = user ? user.name : 'Người dùng';

// Code tiếp theo...

const form = useForm({
  // Dữ liệu form
});

const handleSubmit = () => {
  // Xử lý submit
};
</script>

<template>
  <Head title="TK Example" />

  <AppLayout role="tk">
    <template #header>
      <div class="flex justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Trưởng Khoa - Ví Dụ
        </h2>
        <div class="flex items-center">
          <span class="mr-4">Xin chào, {{ userName }}</span>
          <PrimaryButton @click="handleSubmit">Tạo Mới</PrimaryButton>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <!-- Nội dung chính -->
            <Table>
              <template #header>
                <tr>
                  <th>ID</th>
                  <th>Khoa</th>
                  <th>Thao tác</th>
                </tr>
              </template>
              <template #body>
                <tr>
                  <td>1</td>
                  <td>Khoa ví dụ</td>
                  <td>
                    <div class="flex space-x-2">
                      <SecondaryButton>Sửa</SecondaryButton>
                      <DangerButton>Xóa</DangerButton>
                    </div>
                  </td>
                </tr>
              </template>
            </Table>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template> 