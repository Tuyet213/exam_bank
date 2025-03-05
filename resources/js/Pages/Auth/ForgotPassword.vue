<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
      <Head title="Forgot Password" />
    <div class="container my-auto">
      <div class="row mt-5">
        <div class="col-lg-5 col-md-8 col-12 mx-auto mt-1">
          <div class="card z-index-0 animated-fade-in">
            <!-- Card Header -->
            <div class="card-header p-0 mt-n1 mx-3 z-index-2" style="position: relative; top: -30px;">
              <div
                class="shadow-md border-radius-lg py-1 pe-1"
                style="background-color: #5EB562"
              >
                <h4 class="text-white font-weight-bolder text-center mt-2 mb-0">
                  Forgot Password
                </h4>
                <div class="row mt-1">
                  <div class="col-2 text-center ms-auto">
                    <a class="btn btn-link px-3" href="javascript:;">
                      <i class="fab fa-facebook-f text-white text-lg"></i>
                    </a>
                  </div>
                  <div class="col-2 text-center px-1">
                    <a class="btn btn-link px-3" href="javascript:;">
                      <i class="fab fa-github text-white text-lg"></i>
                    </a>
                  </div>
                  <div class="col-2 text-center me-auto">
                    <a class="btn btn-link px-3" href="javascript:;">
                      <i class="fab fa-google text-white text-lg"></i>
                    </a>
                  </div>
                </div>
              </div>
            </div>
  
            <!-- Card Body -->
            <div class="card-body">
              <form role="form" class="text-start" @submit.prevent="submit">
                <!-- Email Input -->
                <div class="mb-3">
                  <label for="email" class="form-label">Email</label>
                  <input
                    id="email"
                    type="email"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.email }"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="email"
                  />
                  <div v-if="form.errors.email" class="invalid-feedback animated-shake">
                    {{ form.errors.email }}
                  </div>
                </div>
  
                <!-- Sign In Button -->
                <div class="text-center">
                  <button
                    type="submit"
                    class="btn btn-success w-100 my-4 mb-2"
                    :class="{ 'opacity-50': form.processing }"
                    :disabled="form.processing"
                  >
                    Email Password Reset Link
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    </GuestLayout>
  </template>