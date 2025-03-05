<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
  <GuestLayout>
    <Head title="Sign up" />
  <div class="container my-auto">
    <div class="row">
      <div class="col-lg-5 col-md-8 col-12 mx-auto mt-5">
        <div class="card z-index-0 animated-fade-in">
          <!-- Card Header -->
          <div class="card-header p-0 mx-4 z-index-2" style="position: relative; top: -30px;">
            <div
              class="shadow-lg border-radius-lg py-4 pe-1"
              style="background-color: #5EB562"
            >
              <h4 class="text-white font-weight-bolder text-center mt-2 mb-0">
                Sign in
              </h4>
              <div class="row mt-3">
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
            <form role="form" @submit.prevent="submit">
                <!-- Name Input     -->
                <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input
                  id="name"
                  type="text"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.name }"
                  v-model="form.name"
                  required
                />
                <div v-if="form.errors.name" class="invalid-feedback animated-shake">
                  {{ form.errors.name }}
                </div>
              </div>
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
                />
                <div v-if="form.errors.email" class="invalid-feedback animated-shake">
                  {{ form.errors.email }}
                </div>
              </div>

              <!-- Password Input -->
              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input
                  id="password"
                  type="password"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.password }"
                  v-model="form.password"
                  required
                />
                <div v-if="form.errors.password" class="invalid-feedback animated-shake">
                  {{ form.errors.password }}
                </div>
              </div>
              <!-- Password Confirmation Input -->
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Password Confirmation</label>
                <input
                  id="password_confirmation"
                  type="password"
                  class="form-control"
                  :class="{ 'is-invalid': form.errors.password_confirmation }"
                  v-model="form.password_confirmation"
                  required
                />
                <div v-if="form.errors.password_confirmation" class="invalid-feedback animated-shake">
                  {{ form.errors.password_confirmation }}
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
                  Sign up
                </button>
              </div>

              <!-- Links -->
              <p class="mt-4 text-sm text-center">
                Already have an account?
                <Link
                  :href="route('login')"
                  class="text-success font-weight-bold"
                >
                  Sign in
                </Link>
              </p>
              
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  </GuestLayout>
</template>


