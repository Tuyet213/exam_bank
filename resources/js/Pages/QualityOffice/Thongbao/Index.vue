<template>
    <AppLayout role="dbcl">
        <template v-slot:sub-link>
            <li class="breadcrumb-item active">
                <a :href="route('quality.thongbao.index')">Danh sách thông báo</a>
            </li>
        </template>

        <template v-slot:content>
            <div class="content">
                <div class="card border-radius-lg shadow-lg animated-fade-in">
                    <div class="card-header d-flex justify-content-between align-items-center bg-info-qo text-white p-4">
                        <h3 class="mb-0">DANH SÁCH THÔNG BÁO</h3>
                        <Link
                            :href="route('quality.thongbao.create')"
                            class="btn btn-success"
                        >
                            <i class="fas fa-plus me-2"></i>Thêm mới
                        </Link>
                    </div>

                    <div class="card-body">
                        <!-- Thông báo thành công -->
                        <div v-if="$page.props.flash.success" class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ $page.props.flash.success }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>

                        <!-- Bảng dữ liệu -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">STT</th>
                                        <th width="25%">Tiêu đề</th>
                                        <th width="40%">Nội dung</th>
                                        <th width="15%">Ngày gửi</th>
                                        <th width="15%">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(thongbao, index) in thongbaos.data" :key="thongbao.id">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ thongbao.title }}</td>
                                        <td>
                                            {{ truncateContent(thongbao.content) }}
                                        </td>
                                        <td>{{ thongbao.formatted_date }}</td>
                                        <td>
                                            <Link
                                                :href="route('quality.thongbao.show', thongbao.id)"
                                                class="btn btn-sm btn-primary me-2"
                                                title="Xem chi tiết"
                                            >
                                                <i class="fas fa-eye"></i>
                                            </Link>
                                        </td>
                                    </tr>
                                    <tr v-if="thongbaos.data.length === 0">
                                        <td colspan="5" class="text-center">Không có dữ liệu</td>
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

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    thongbaos: Object
});

// Hàm cắt nội dung hiển thị
const truncateContent = (content) => {
    if (!content) return '';
    return content.length > 100 ? content.substring(0, 100) + '...' : content;
};
</script>

<style scoped>
.bg-info-qo {
    background-color: #5cb85c;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.btn-sm i {
    font-size: 0.875rem;
}

.animated-fade-in {
    animation: fadeIn 0.5s;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.pagination .page-link {
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 3px;
}

.disabled-link {
    pointer-events: none;
    color: #ccc;
}

.active-page {
    background-color: #5cb85c;
    border-color: #5cb85c;
    color: white;
}
</style> 