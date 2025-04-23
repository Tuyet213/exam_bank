<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";

const { thongbaos } = defineProps({
    thongbaos: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <AppLayout role="dbcl">
        <template #sub-link>
            <li class="breadcrumb-item active">
                <a :href="route('qlo.notice.index')">Thông báo quy định</a>
            </li>
        </template>
        <template #content>
            <div class="content">
                <div class="card border-radius-lg shadow-lg animated-fade-in">
                    <!-- Card Header -->
                    <div class="card-header bg-success-tb text-white p-4 d-flex justify-content-between">
                        <h3 class="mb-0 font-weight-bolder">
                            THÔNG BÁO QUY ĐỊNH MỚI
                        </h3>
                        <Link
                            :href="route('qlo.notice.create')"
                            class="btn btn-success-add"
                        >
                            <i class="fas fa-user-plus"></i> Thêm thông báo
                        </Link>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tiêu đề</th>
                                        <th>Nội dung</th>
                                        <th>Ngày gửi</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="thongbaos.data.length === 0">
                                        <td colspan="3" class="text-center">
                                            Không có dữ liệu
                                        </td>
                                    </tr>
                                    <tr
                                        v-for="thongbao in thongbaos.data"
                                        :key="thongbao.id"
                                    >
                                        <td>{{ thongbao.id }}</td>
                                        <td>{{ thongbao.title }}</td>
                                        <td>{{ thongbao.content }}</td>
                                        <td>{{ thongbao.formatted_date }}</td>
                                        <td>
                                            <Link
                                                :href="
                                                    route(
                                                        'qlo.notice.show',
                                                        thongbao.id
                                                    )
                                                "
                                                class="btn btn-sm btn-success-edit me-2"
                                            >
                                                <i class="fas fa-eye"></i>
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Phân trang -->
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center mt-3">
                                <li
                                    class="page-item"
                                    :class="{
                                        disabled: thongbaos.current_page === 1,
                                    }"
                                >
                                    <Link
                                        :href="thongbaos.links[0]?.url || '#'"
                                        class="page-link rounded-circle"
                                        :class="{
                                            'disabled-link':
                                                !thongbaos.links[0]?.url,
                                        }"
                                    >
                                        <i class="fas fa-chevron-left"></i>
                                    </Link>
                                </li>
                                <li
                                    v-for="link in thongbaos.links.slice(1, -1)"
                                    :key="link.label"
                                    class="page-item"
                                    :class="{ active: link.active }"
                                >
                                    <Link
                                        :href="link.url || '#'"
                                        class="page-link rounded-circle"
                                        :class="{ 'active-page': link.active }"
                                    >
                                        {{ link.label }}
                                    </Link>
                                </li>
                                <li
                                    class="page-item"
                                    :class="{
                                        disabled:
                                            thongbaos.current_page ===
                                            thongbaos.last_page,
                                    }"
                                >
                                    <Link
                                        :href="
                                            thongbaos.links[
                                                thongbaos.links.length - 1
                                            ]?.url || '#'
                                        "
                                        class="page-link rounded-circle"
                                        :class="{
                                            'disabled-link':
                                                !thongbaos.links[
                                                    thongbaos.links.length - 1
                                                ]?.url,
                                        }"
                                    >
                                        <i class="fas fa-chevron-right"></i>
                                    </Link>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </template>
    </AppLayout>
</template>
