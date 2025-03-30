<script>
import { useForm } from '@inertiajs/vue3';
import { Head, Link } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

export default {
  components: {
    Link, Head
  },
  layout: GuestLayout,
  props: {
    canResetPassword: Boolean,
  },
  setup() {
    const form = useForm({
      email: '',
      password: '',
      remember: false,
    });

    const submit = () => {
      form.post(route('login'), {
        onFinish: () => form.reset('password'),
      });
    };

    return { form, submit };
  },
};
</script>
<!-- Giữ v-model="model" để binding dữ liệu.
Giữ ref="input" để tham chiếu DOM. -->


<template>

    <Head title="Signin" />
  <div class="container my-auto">
    <div class="row mt-5">
      <div class="col-lg-5 col-md-8 col-12 mx-auto mt-5">
        <div class="card z-index-0 animated-fade-in">
          <!-- Card Header -->
          <div class="card-header p-0 mt-n6 mx-3 z-index-2" style="position: relative; top: -10px;">
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
            <form role="form" class="text-start mt-3" @submit.prevent="submit">
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

              <!-- Remember Me Switch -->
              <div class="form-check form-switch mb-3">
                <input
                  id="rememberMe"
                  type="checkbox"
                  class="form-check-input"
                  v-model="form.remember"
                />
                <label for="rememberMe" class="form-check-label">Remember me</label>
              </div>

              <!-- Sign In Button -->
              <div class="text-center">
                <button
                  type="submit"
                  class="btn btn-success w-100 my-4 mb-2"
                  :class="{ 'opacity-50': form.processing }"
                  :disabled="form.processing"
                >
                  Sign in
                </button>
              </div>

              <!-- Links
              <p class="mt-4 text-sm text-center">
                Don't have an account?
                <Link
                  :href="route('register')"
                  class="text-success font-weight-bold"
                >
                  Sign up
                </Link>
              </p> -->
              <div class="mb-4 flex items-center justify-center">
                <Link
                  v-if="canResetPassword"
                  :href="route('password.request')"
                  class="text-success font-weight-bold"
                >
                  Recover Password
                </Link>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

</template>

