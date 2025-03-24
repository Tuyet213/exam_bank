<script setup>
import QualityLayout from "@/Layouts/QualityLayout.vue";
import { useForm } from "@inertiajs/vue3";

const form = useForm({
    title: "",
    content: "",
    files: [],
});

const submit = () => {
    console.log(form.files);
    form.post(route("qlo.notice.store"), {
        onSuccess: () => {
            alert("Thông báo quy định đã được gửi thành công");
            form.reset();
        },
    });
};

const handleFileChange = (event) => {
    form.files = event.target.files;
};
</script>

<template>
    <QualityLayout>
        <template #sub-link>
            <li class="breadcrumb-item active">
                <a :href="route('qlo.notice.create')">Thông báo quy định</a>
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

                    <!-- Card Body -->
                    <div class="card-body p-4">
                        <form @submit.prevent="submit">
                            <div class="mb-3">
                                <label for="title" class="form-label"
                                    >Tiêu đề</label
                                >
                                <input
                                    type="text"
                                    class="form-control"
                                    id="title"
                                    v-model="form.title"
                                    required
                                    placeholder=""
                                />
                                <small
                                    v-if="form.errors.title"
                                    class="text-danger"
                                >
                                    {{ form.errors.title }}
                                </small>
                            </div>
                            <div class="mb-3">
                                <label for="content" class="form-label"
                                    >Nội dung</label
                                >
                                <textarea
                                    class="form-control"
                                    id="content"
                                    v-model="form.content"
                                    required
                                    placeholder=""
                                ></textarea>
                                <small
                                    v-if="form.errors.content"
                                    class="text-danger"
                                >
                                    {{ form.errors.content }}
                                </small>
                            </div>
                            <div class="mb-3">
                                <label for="file" class="form-label"
                                    >File</label
                                >

                                <input
                                    type="file"
                                    class="form-control"
                                    id="file"
                                    name="files[]"
                                    @change="handleFileChange"
                                    multiple
                                />
                                <small
                                    v-if="form.errors.files"
                                    class="text-danger"
                                >
                                    {{ form.errors.files }}
                                </small>
                            </div>
                            <div v-if="form.files.length > 0">
                                <p>Các file đã chọn:</p>
                                <ul>
                                    <li
                                        v-for="file in form.files"
                                        :key="file.name"
                                    >
                                        {{ file.name }}
                                    </li>
                                </ul>
                            </div>
                            <!-- Nút Create -->
                            <div class="text-end">
                                <button
                                    type="submit"
                                    class="btn btn-success font-weight-bold"
                                >
                                    Gửi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </QualityLayout>
</template>
