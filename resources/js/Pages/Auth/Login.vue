<template>
  <div class="container my-auto">
    <div class="row">
      <div class="col-lg-4 col-md-8 col-12 mx-auto mt-5">
        <div class="card z-index-0 animated-fade-in">
          <!-- Card Header -->
          <div class="card-header p-0 mt-n6 mx-4 z-index-2" style="position: relative; top: -20px;">
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

              <!-- Links -->
              <p class="mt-4 text-sm text-center">
                Don't have an account?
                <Link
                  :href="route('register')"
                  class="text-success font-weight-bold"
                >
                  Sign up
                </Link>
              </p>
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

<script>
import { useForm } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';

export default {
  components: {
    Link,
  },
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

<!-- <style scoped>
/* Animation for card fade-in */
.animated-fade-in {
  animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Animation for error shake */
.animated-shake {
  animation: shake 0.3s ease-in-out;
}

@keyframes shake {
  0%, 100% {
    transform: translateX(0);
  }
  25%, 75% {
    transform: translateX(-5px);
  }
  50% {
    transform: translateX(5px);
  }
}

/* Custom styles for Card Header */
.shadow-lg {
  box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.2) !important;
}

.border-radius-lg {
  border-radius: 0.75rem !important;
}

/* Form styles */
.form-label {
  font-size: 0.875rem;
  color: #6c757d;
  margin-bottom: 0.25rem;
}

.form-control {
  border: none;
  border-bottom: 1px solid #d1d1d1;
  border-radius: 0;
  padding: 0.5rem 0;
  font-size: 1rem;
}

.form-control:focus {
  box-shadow: none;
  border-bottom: 1px solid #5EB562;
}

.form-check-input:checked {
  background-color: #5EB562;
  border-color: #5EB562;
}

.btn-success {
  background-color: #5EB562;
  border: none;
}

.btn-success:hover {
  background-color: #4a9b51;
}

.text-success {
  color: #5EB562 !important;
}

.font-weight-bolder {
  font-weight: 700;
}

.font-weight-bold {
  font-weight: 600;
}
</style> -->