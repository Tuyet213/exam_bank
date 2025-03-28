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
            <li class="breadcrumb-item active">
                <a :href="route('qlo.notice.index')">Thông báo quy định</a>
            </li>
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
                        <div class="table-responsive">
                            <h3>{{ thongbao.title }}</h3>
                            <p>{{ thongbao.content }}</p>
                            <div v-for="(fileUrl, fileName) in thongbao.files" :key="fileName">
                                <a :href="fileUrl" target="_blank" download>{{ fileName }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </QualityLayout>
</template>
