<script setup>
import QualityLayout from "@/Layouts/QualityLayout.vue";
import { useForm } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";

const { thongbao } = defineProps({
    thongbao: {
        type: Object,
        required: true,
    },
});

</script>

<template>
    <QualityLayout>
        <template #sub-link>
            <li class="breadcrumb-item">
                <a :href="route('qlo.notice.index')">Thông báo quy định</a>
                
            </li>
            <li class="breadcrumb-item active">{{ thongbao.title }}</li>
        </template>
        <template #content>
            <div class="content">
                <div class="card border-radius-lg shadow-lg animated-fade-in">
                    <!-- Card Header -->
                    <div class="card-header bg-success-tb text-white p-4">
                        <h3 class="mb-0 font-weight-bolder">
                            THÔNG BÁO QUY ĐỊNH MỚI
                        </h3>
                    </div>

                    <div class="card-body">
                        <h3 class="font-weight-bold mb-3">Tiêu đề: {{ thongbao.title }}</h3>
                        <p class="mb-4" style="font-size: 1.1em;">{{ thongbao.content }}</p>
                        <h4 class="font-weight-bold">File đính kèm:</h4>
                        <div class="row">
                            <div class="col-12" v-for="file in thongbao.files" :key="file.name" style="margin-bottom: 15px;">
                                <div class="d-flex align-items-center">
                                    <img :src="file.url" alt="Ảnh" class="img-thumbnail me-2" style="width: 100px; height: 100px;" referrerpolicy="no-referrer">
                                    <a :href="file.url" target="_blank" download class="btn btn-primary btn-sm">{{ file.name }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </QualityLayout>
</template>
