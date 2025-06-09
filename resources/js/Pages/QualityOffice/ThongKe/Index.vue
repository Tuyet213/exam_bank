<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ref, watch, computed, onMounted, nextTick, onBeforeUnmount } from 'vue';
import { router } from '@inertiajs/vue3';
import Chart from 'chart.js/auto';

const props = defineProps({
    role: {
        type: String,
        default: () => ''
    },
    khoas: {
        type: Array,
        default: () => []
    },
    bomons: {
        type: Array,
        default: () => []
    },
    ds_hoc_ki: {
        type: Array,
        default: () => ['1', '2', 'Hè']
    },
    ds_nam_hoc: {
        type: Array,
        default: () => []
    },
    filters: {
        type: Object,
        default: () => ({
            khoa_id: '',
            bomon_id: '',
            hoc_ki: '',
            nam_hoc: ''
        })
    },
    thongke_data: {
        type: Object,
        default: () => ({})
    },
    currentFilters: Object,
    thongKeData: Object,
    chartData: Object
});

// Computed property để lọc ra các năm học (loại bỏ tong_hop)
const filteredThongKeData = computed(() => {
    const data = { ...props.thongke_data };
    delete data.tong_hop;
    return data;
});

// State cho collapse/expand của các phần
const expandedNamHoc = ref({});
const expandedHocKi = ref({});
const expandedKhoa = ref({});
const expandedBoMon = ref({});
const expandedVienChuc = ref({});

// Khởi tạo trạng thái mở rộng mặc định
onMounted(() => {
    // Mở rộng năm học đầu tiên
    const firstNamHoc = Object.keys(filteredThongKeData.value)[0];
    if (firstNamHoc) {
        expandedNamHoc.value[firstNamHoc] = true;
    }
});

// Các biến cho bộ lọc
const selectedKhoa = ref(props.filters.khoa_id || '');
const selectedBoMon = ref(props.filters.bomon_id || '');
const selectedHocKi = ref(props.filters.hoc_ki || '');
const selectedNamHoc = ref(props.filters.nam_hoc || '');
const debounceTimeout = ref(null);


// Reset bộ môn khi thay đổi khoa
watch(selectedKhoa, (newValue, oldValue) => {
    if (newValue !== oldValue) {
        selectedBoMon.value = '';
    }
});

// Hàm tìm kiếm/lọc
const performSearch = () => {
    if (debounceTimeout.value) {
        clearTimeout(debounceTimeout.value);
    }
    debounceTimeout.value = setTimeout(() => {
        router.get(
            route('thongke.index'),
            { 
                khoa_id: selectedKhoa.value,
                bomon_id: selectedBoMon.value,
                hoc_ki: selectedHocKi.value,
                nam_hoc: selectedNamHoc.value
            },
            { 
                preserveState: true,
                replace: true 
            }
        );
    }, 300);
};

// Theo dõi sự thay đổi của các bộ lọc và thực hiện tìm kiếm
watch([selectedKhoa, selectedBoMon, selectedHocKi, selectedNamHoc], () => {
    performSearch();
});

// Toggle expand/collapse cho năm học
const toggleNamHoc = (namHoc) => {
    expandedNamHoc.value[namHoc] = !expandedNamHoc.value[namHoc];
};

// Toggle expand/collapse cho học kỳ
const toggleHocKi = (namHoc, hocKi) => {
    const key = `${namHoc}_${hocKi}`;
    expandedHocKi.value[key] = !expandedHocKi.value[key];
};

// Toggle expand/collapse cho khoa
const toggleKhoa = (khoaId) => {
    expandedKhoa.value[khoaId] = !expandedKhoa.value[khoaId];
};

// Toggle expand/collapse cho bộ môn
const toggleBoMon = (khoaId, boMonId) => {
    const key = khoaId + '_' + boMonId;
    expandedBoMon.value[key] = !expandedBoMon.value[key];
};

// Toggle expand/collapse cho viên chức
const toggleVienChuc = (vienChucId) => {
    expandedVienChuc.value[vienChucId] = !expandedVienChuc.value[vienChucId];
};

// Hàm xuất Excel
const exportExcel = () => {
    const queryParams = new URLSearchParams({
        khoa_id: filterForm.value.khoa_id,
        bomon_id: filterForm.value.bomon_id,
        hoc_ki: filterForm.value.hoc_ki,
        nam_hoc: filterForm.value.nam_hoc
    }).toString();
    
    window.location.href = route('thongke.export') + '?' + queryParams;
};

// Hàm xuất Excel danh sách giảng viên
const exportGiangVienExcel = () => {
    const queryParams = new URLSearchParams({
        khoa_id: filterForm.value.khoa_id,
        bomon_id: filterForm.value.bomon_id,
        hoc_ki: filterForm.value.hoc_ki,
        nam_hoc: filterForm.value.nam_hoc
    }).toString();
    
    window.location.href = route('thongke.export_giang_vien') + '?' + queryParams;
};

// Hàm xuất Excel biểu đồ cột
const exportBarChartExcel = () => {
    const queryParams = new URLSearchParams({
        khoa_id: filterForm.value.khoa_id,
        bomon_id: filterForm.value.bomon_id,
        hoc_ki: filterForm.value.hoc_ki,
        nam_hoc: filterForm.value.nam_hoc
    }).toString();
    
    window.location.href = route('thongke.export_bar_chart') + '?' + queryParams;
};

// Hàm xuất Excel biểu đồ tròn
const exportPieChartExcel = () => {
    const queryParams = new URLSearchParams({
        khoa_id: filterForm.value.khoa_id,
        bomon_id: filterForm.value.bomon_id,
        hoc_ki: filterForm.value.hoc_ki,
        nam_hoc: filterForm.value.nam_hoc
    }).toString();
    
    window.location.href = route('thongke.export_pie_chart') + '?' + queryParams;
};

// Thêm các phương thức xử lý trạng thái
const getStatusBadge = (status) => {
    switch(status) {
        case 'Approved': return 'badge bg-success';
        case 'Pending': return 'badge bg-warning';
        case 'Rejected': return 'badge bg-danger';
        case 'Draft': return 'badge bg-secondary';
        case 'Completed': return 'badge bg-primary';
        default: return 'badge bg-light';
    }
};

const getStatusText = (status) => {
    switch(status) {
        case 'Approved': return 'Đã duyệt';
        case 'Pending': return 'Chờ duyệt';
        case 'Rejected': return 'Từ chối';
        case 'Draft': return 'Nháp';
        case 'Completed': return 'Hoàn thành';
        default: return 'Không xác định';
    }
};

const exportExcelGioThamGia = () => {
    const queryParams = new URLSearchParams({
        khoa_id: selectedKhoa.value,
        bomon_id: selectedBoMon.value,
        hoc_ki: selectedHocKi.value,
        nam_hoc: selectedNamHoc.value
    }).toString();
    window.location.href = route('thongke.excel_gio_tham_gia') + '?' + queryParams;
};

// Reactive data
const filterForm = ref({
    khoa_id: props.currentFilters.khoa_id || '',
    bomon_id: props.currentFilters.bomon_id || '',
    nam_hoc: props.currentFilters.nam_hoc || '',
    hoc_ki: props.currentFilters.hoc_ki || ''
});

const activeChart = ref('pie');
const activeView = ref('khoa');
const pieChartInstance = ref(null);
const pieChartCanvas = ref(null);
const barChartInstance = ref(null);
const barChartCanvas = ref(null);

// Computed
const filteredBoMons = computed(() => {
    // Ưu tiên filterForm.khoa_id trước, sau đó mới đến selectedKhoa
    const khoaId = filterForm.value.khoa_id || selectedKhoa.value;
    if (!khoaId) return props.filters.bomons || [];
    return (props.filters.bomons || []).filter(bm => bm.id_khoa == khoaId);
});

// Computed cho màu legend
const legendColors = computed(() => {
    const colors = [
        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', 
        '#9966FF', '#FF9F40', '#FF6B6B', '#4ECDC4',
        '#45B7D1', '#96CEB4', '#FFEAA7', '#DDA0DD',
        '#98D8C8', '#F7DC6F', '#BB8FCE', '#85C1E9'
    ];
    
    return props.chartData.pie_chart.map((item, index) => ({
        ...item,
        color: colors[index % colors.length]
    }));
});

// Methods
const applyFilters = () => {
    router.get(route('thongke.index'), filterForm.value, {
        preserveState: true,
        preserveScroll: true
    });
};

const resetFilters = () => {
    filterForm.value = {
        khoa_id: '',
        bomon_id: '',
        nam_hoc: '',
        hoc_ki: ''
    };
    applyFilters();
};

const onKhoaChange = () => {
    filterForm.value.bomon_id = '';
};

const createPieChart = () => {
    if (pieChartInstance.value) {
        pieChartInstance.value.destroy();
        pieChartInstance.value = null;
    }
    
    if (!pieChartCanvas.value) return;
    
    // Kiểm tra dữ liệu
    if (!props.chartData || !props.chartData.pie_chart || props.chartData.pie_chart.length === 0) {
        return;
    }
    
    const ctx = pieChartCanvas.value.getContext('2d');
    pieChartInstance.value = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: props.chartData.pie_chart.map(item => item.label),
            datasets: [{
                data: props.chartData.pie_chart.map(item => item.value),
                backgroundColor: legendColors.value.map(item => item.color),
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return `${context.label}: ${context.parsed} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
};

const createBarChart = () => {
    if (barChartInstance.value) {
        barChartInstance.value.destroy();
        barChartInstance.value = null;
    }
    
    if (!barChartCanvas.value) return;
    
    // Kiểm tra dữ liệu
    if (!props.chartData || !props.chartData.bar_chart || props.chartData.bar_chart.length === 0) {
        return;
    }
    
    const ctx = barChartCanvas.value.getContext('2d');
    barChartInstance.value = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: props.chartData.bar_chart.map(item => item.name),
            datasets: [
                {
                    label: 'Số học phần',
                    data: props.chartData.bar_chart.map(item => item.hoc_phan),
                    backgroundColor: 'rgba(54, 162, 235, 0.8)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Số viên chức',
                    data: props.chartData.bar_chart.map(item => item.vien_chuc),
                    backgroundColor: 'rgba(75, 192, 192, 0.8)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Tổng giờ',
                    data: props.chartData.bar_chart.map(item => item.gio),
                    backgroundColor: 'rgba(255, 206, 86, 0.8)',
                    borderColor: 'rgba(255, 206, 86, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        font: {
                            size: 12
                        },
                        padding: 20
                    }
                },
                tooltip: {
                    callbacks: {
                        title: function(context) {
                            return context[0].label;
                        },
                        label: function(context) {
                            return `${context.dataset.label}: ${context.parsed.y}`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Số lượng'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: filterForm.value.khoa_id ? 'Bộ môn' : 'Khoa'
                    }
                }
            }
        }
    });
};

// Watchers
watch(activeChart, async (newValue) => {
    await nextTick();
    if (newValue === 'pie') {
        createPieChart();
    } else if (newValue === 'bar') {
        createBarChart();
    }
});

// Watcher để cập nhật biểu đồ khi dữ liệu thay đổi
watch(() => props.chartData, async (newData) => {
    if (newData) {
        await nextTick();
        if (activeChart.value === 'pie') {
            createPieChart();
        } else if (activeChart.value === 'bar') {
            createBarChart();
        }
    }
}, { deep: true });

// Watcher để cập nhật biểu đồ khi thongKeData thay đổi
watch(() => props.thongKeData, async (newData) => {
    if (newData) {
        await nextTick();
        if (activeChart.value === 'pie') {
            createPieChart();
        } else if (activeChart.value === 'bar') {
            createBarChart();
        }
    }
}, { deep: true });

// Lifecycle
onMounted(async () => {
    await nextTick();
    createPieChart();
    createBarChart();
});

// Cleanup on unmount
onBeforeUnmount(() => {
    if (pieChartInstance.value) {
        pieChartInstance.value.destroy();
    }
    if (barChartInstance.value) {
        barChartInstance.value.destroy();
    }
});
</script>

<template>
    <AppLayout :role="role">
        <template #sub-link>
            <li class="breadcrumb-item active">Thống kê tổng hợp</li>
        </template>

        <template #content>
            <div class="container-fluid py-4">
                <!-- Header -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="fas fa-chart-bar fa-2x text-primary"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-1 fw-bold">Thống kê biên soạn ngân hàng câu hỏi</h4>
                                        <p class="text-muted mb-0">Tổng quan hoạt động biên soạn và phản biện</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                  <!-- Nút xuất Excel -->
                  <div class="row mt-4 mb-4">
                    <div class="col-12 text-end">
                        <div class="btn-group">
                            <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-file-excel me-2"></i>Xuất Excel
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="#" @click.prevent="exportGiangVienExcel">
                                        <i class="fas fa-users me-2"></i>Danh sách giảng viên
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" @click.prevent="exportBarChartExcel">
                                        <i class="fas fa-chart-bar me-2"></i>Biểu đồ cột
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" @click.prevent="exportPieChartExcel">
                                        <i class="fas fa-chart-pie me-2"></i>Biểu đồ tròn
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="#" @click.prevent="exportExcel">
                                        <i class="fas fa-table me-2"></i>Báo cáo tổng hợp
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Bộ lọc -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <form @submit.prevent="applyFilters" class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold">
                                            <i class="fas fa-university me-1"></i>Khoa
                                        </label>
                                        <select v-model="filterForm.khoa_id" class="form-select" @change="onKhoaChange">
                                            <option value="">Toàn trường</option>
                                            <option v-for="khoa in filters.khoas" :key="khoa.id" :value="khoa.id">
                                                {{ khoa.ten }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold">
                                            <i class="fas fa-sitemap me-1"></i>Bộ môn
                                        </label>
                                        <select v-model="filterForm.bomon_id" class="form-select" :disabled="!filterForm.khoa_id">
                                            <option value="">Tất cả bộ môn</option>
                                            <option v-for="bomon in filteredBoMons" :key="bomon.id" :value="bomon.id">
                                                {{ bomon.ten }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fw-semibold">
                                            <i class="fas fa-calendar-alt me-1"></i>Năm học
                                        </label>
                                        <select v-model="filterForm.nam_hoc" class="form-select">
                                            <option value="">Tất cả</option>
                                            <option v-for="nam in filters.ds_nam_hoc" :key="nam" :value="nam">
                                                {{ nam }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fw-semibold">
                                            <i class="fas fa-clock me-1"></i>Học kỳ
                                        </label>
                                        <select v-model="filterForm.hoc_ki" class="form-select">
                                            <option value="">Tất cả</option>
                                            <option v-for="hk in filters.ds_hoc_ki" :key="hk" :value="hk">
                                                Học kỳ {{ hk }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary me-2">
                                            <i class="fas fa-filter me-1"></i>Lọc
                                        </button>
                                        <button type="button" @click="resetFilters" class="btn btn-outline-secondary">
                                            <i class="fas fa-undo me-1"></i>Reset
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tổng quan -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center">
                                <div class="d-flex align-items-center justify-content-center mb-3">
                                    <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                        <i class="fas fa-book fa-2x text-primary"></i>
                                    </div>
                                </div>
                                <h3 class="fw-bold text-primary mb-1">{{ thongKeData.tong_quan.tong_hoc_phan }}</h3>
                                <p class="text-muted mb-0">Tổng học phần</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center">
                                <div class="d-flex align-items-center justify-content-center mb-3">
                                    <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                        <i class="fas fa-users fa-2x text-success"></i>
                                    </div>
                                </div>
                                <h3 class="fw-bold text-success mb-1">{{ thongKeData.tong_quan.tong_vien_chuc }}</h3>
                                <p class="text-muted mb-0">Tổng viên chức tham gia</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center">
                                <div class="d-flex align-items-center justify-content-center mb-3">
                                    <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                        <i class="fas fa-clock fa-2x text-warning"></i>
                                    </div>
                                </div>
                                <h3 class="fw-bold text-warning mb-1">{{ thongKeData.tong_quan.tong_gio }}</h3>
                                <p class="text-muted mb-0">Tổng số giờ</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Biểu đồ -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent border-0 pb-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="fw-bold mb-0">
                                        <i class="fas fa-chart-pie me-2"></i>Biểu đồ thống kê
                                    </h5>
                                    <div class="btn-group" role="group">
                                        <input type="radio" class="btn-check" name="chartType" id="pieChart" v-model="activeChart" value="pie" autocomplete="off" checked>
                                        <label class="btn btn-outline-primary btn-sm" for="pieChart">
                                            <i class="fas fa-chart-pie me-1"></i>Tròn
                                        </label>
                                        
                                        <input type="radio" class="btn-check" name="chartType" id="barChart" v-model="activeChart" value="bar" autocomplete="off">
                                        <label class="btn btn-outline-primary btn-sm" for="barChart">
                                            <i class="fas fa-chart-bar me-1"></i>Cột
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Pie Chart -->
                                    <div v-if="activeChart === 'pie'" class="col-md-8">
                                        <div class="chart-container">
                                            <canvas ref="pieChartCanvas" width="400" height="200"></canvas>
                                        </div>
                                    </div>
                                    
                                    <!-- Bar Chart - Full width -->
                                    <div v-if="activeChart === 'bar'" class="col-12">
                                        <div class="chart-container">
                                            <canvas ref="barChartCanvas" width="400" height="200"></canvas>
                                        </div>
                                    </div>
                                    
                                    <!-- Legend chỉ hiển thị cho Pie Chart -->
                                    <div v-if="activeChart === 'pie'" class="col-md-4">
                                        <div class="chart-legend">
                                            <h6 class="fw-semibold mb-3">Chú thích</h6>
                                            <div v-for="(item, index) in legendColors" :key="index" class="d-flex align-items-center mb-2">
                                                <div class="legend-color me-2" :style="{ backgroundColor: item.color }"></div>
                                                <small class="text-muted">{{ item.label }}: {{ item.value }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chi tiết dữ liệu -->
                <div class="row">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent border-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="fw-bold mb-0">
                                        <i class="fas fa-table me-2"></i>Chi tiết dữ liệu
                                    </h5>
                                    <div class="btn-group" role="group">
                                        <input type="radio" class="btn-check" name="viewType" id="khoaView" v-model="activeView" value="khoa" autocomplete="off" checked>
                                        <label class="btn btn-outline-secondary btn-sm" for="khoaView">
                                            <i class="fas fa-university me-1"></i>Theo khoa
                                        </label>
                                        
                                        <input type="radio" class="btn-check" name="viewType" id="vienChucView" v-model="activeView" value="vien_chuc" autocomplete="off">
                                        <label class="btn btn-outline-secondary btn-sm" for="vienChucView">
                                            <i class="fas fa-user me-1"></i>Theo viên chức
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- View theo khoa -->
                                <div v-if="activeView === 'khoa'">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Khoa/Bộ môn</th>
                                                    <th class="text-center">Học phần</th>
                                                    <th class="text-center">Viên chức</th>
                                                    <th class="text-center">Tổng giờ</th>
                                                    <th class="text-center">Thao tác</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <template v-for="(khoa, khoaId) in thongKeData.chi_tiet_khoa" :key="khoaId">
                                                    <!-- Hàng khoa -->
                                                    <tr class="table-primary">
                                                        <td class="fw-bold">
                                                            <i class="fas fa-university me-2"></i>{{ khoa.ten }}
                                                        </td>
                                                        <td class="text-center fw-bold">{{ khoa.tong_hoc_phan }}</td>
                                                        <td class="text-center fw-bold">{{ khoa.tong_vien_chuc }}</td>
                                                        <td class="text-center fw-bold">{{ khoa.tong_gio }}</td>
                                                        <td class="text-center">
                                                            <button @click="toggleKhoa(khoaId)" class="btn btn-sm btn-outline-primary">
                                                                <i :class="expandedKhoa[khoaId] ? 'fas fa-minus' : 'fas fa-plus'"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    
                                                    <!-- Các bộ môn -->
                                                    <template v-if="expandedKhoa[khoaId]">
                                                        <tr v-for="(boMon, boMonId) in khoa.bo_mon" :key="boMonId" class="table-light">
                                                            <td class="ps-4">
                                                                <i class="fas fa-sitemap me-2"></i>{{ boMon.ten }}
                                                            </td>
                                                            <td class="text-center">{{ boMon.tong_hoc_phan }}</td>
                                                            <td class="text-center">{{ boMon.tong_vien_chuc }}</td>
                                                            <td class="text-center">{{ boMon.tong_gio }}</td>
                                                            <td class="text-center">
                                                                <button @click="toggleBoMon(khoaId, boMonId)" class="btn btn-sm btn-outline-secondary">
                                                                    <i :class="expandedBoMon[khoaId + '_' + boMonId] ? 'fas fa-minus' : 'fas fa-plus'"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                        
                                                        <!-- Các học phần -->
                                                        <template v-if="expandedBoMon[khoaId + '_' + boMonId]">
                                                            <tr v-for="(hocPhan, hocPhanId) in boMon.hoc_phan" :key="hocPhanId">
                                                                <td class="ps-5">
                                                                    <i class="fas fa-book me-2"></i>{{ hocPhan.ten }}
                                                                    <small class="text-muted d-block">{{ hocPhan.ma }}</small>
                                                                </td>
                                                                <td class="text-center">1</td>
                                                                <td class="text-center">{{ hocPhan.vien_chuc.length }}</td>
                                                                <td class="text-center">
                                                                    {{ hocPhan.vien_chuc.reduce((sum, vc) => sum + vc.so_gio, 0) }}
                                                                </td>
                                                                <td class="text-center">
                                                                    <span :class="getStatusBadge(hocPhan.trang_thai)">
                                                                        {{ getStatusText(hocPhan.trang_thai) }}
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        </template>
                                                    </template>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- View theo viên chức -->
                                <div v-if="activeView === 'vien_chuc'">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Mã viên chức</th>
                                                    <th>Tên viên chức</th>
                                                    <th class="text-center">Tổng giờ</th>
                                                    <th class="text-center">Chi tiết</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <template v-for="vienChuc in thongKeData.chi_tiet_vien_chuc" :key="vienChuc.id">
                                                    <tr>
                                                        <td class="fw-semibold">{{ vienChuc.ma }}</td>
                                                        <td>{{ vienChuc.ten }}</td>
                                                        <td class="text-center fw-bold">{{ vienChuc.tong_gio }}</td>
                                                        <td class="text-center">
                                                            <button @click="toggleVienChuc(vienChuc.id)" class="btn btn-sm btn-outline-info">
                                                                <i :class="expandedVienChuc[vienChuc.id] ? 'fas fa-minus' : 'fas fa-plus'"></i>
                                                                Chi tiết
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    
                                                    <!-- Chi tiết học phần của viên chức -->
                                                    <tr v-if="expandedVienChuc[vienChuc.id]">
                                                        <td colspan="4" class="p-0">
                                                            <div class="bg-light p-3">
                                                                <div class="table-responsive">
                                                                    <table class="table table-sm mb-0">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Học phần</th>
                                                                                <th>Khoa</th>
                                                                                <th>Bộ môn</th>
                                                                                <th>Loại</th>
                                                                                <th class="text-center">Số giờ</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <tr v-for="(hp, key) in vienChuc.chi_tiet_hoc_phan" :key="key">
                                                                                <td>
                                                                                    <strong>{{ hp.ten_hoc_phan }}</strong>
                                                                                    <small class="text-muted d-block">{{ hp.ma_hoc_phan }}</small>
                                                                                </td>
                                                                                <td>{{ hp.khoa }}</td>
                                                                                <td>{{ hp.bo_mon }}</td>
                                                                                <td>
                                                                                    <span :class="hp.loai === 'Biên soạn' ? 'badge bg-primary' : 'badge bg-success'">
                                                                                        {{ hp.loai }}
                                                                                    </span>
                                                                                </td>
                                                                                <td class="text-center">{{ hp.so_gio }}</td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

              
            </div>
        </template>
    </AppLayout>
</template>

<style scoped>
.chart-container {
    position: relative;
    height: 300px;
}

.legend-color {
    width: 16px;
    height: 16px;
    border-radius: 2px;
    flex-shrink: 0;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 25px rgba(0,0,0,0.1) !important;
}

.btn-group .btn-check:checked + .btn {
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
    color: white;
}

.table-responsive {
    border-radius: 0.375rem;
}

.bg-opacity-10 {
    --bs-bg-opacity: 0.1;
}
</style> 