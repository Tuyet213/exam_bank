<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-gray-100 pt-6 sm:justify-center sm:pt-0"
    >
        <div>
            <Link href="/">
                <img src="../assets/img/logontu.png" alt="logo" :style="{ width: '100px', height: '100px' }" style="border-radius: 50%;">
            </Link>
        </div>

        <div
            class="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:rounded-lg"
            style="max-width: 95%; width: 800px;"
        >
            <slot />
        </div>
    </div>
</template>
