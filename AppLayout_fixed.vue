<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { Disclosure, DisclosureButton, DisclosurePanel, Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';
import { Bars3Icon, XMarkIcon, UserIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  role: {
    type: String,
    required: true
  }
});

// Lấy thông tin người dùng hiện tại từ Inertia shared props
const user = usePage().props.auth.user;

// Tạo navigation dựa trên role
const navigation = ref([]);
if (props.role === 'admin') {
  navigation.value = [
    { name: 'Dashboard', href: route('admin.dashboard'), current: route().current('admin.dashboard') },
    { name: 'Người dùng', href: route('admin.users.index'), current: route().current('admin.users.*') },
    // Các menu khác cho admin
  ];
} else if (props.role === 'tbm') {
  // Lấy id bộ môn của người dùng nếu có
  const boMonId = user && user.bo_mon_id ? user.bo_mon_id : null;
  
  navigation.value = [
    { name: 'Dashboard', href: route('tbm.dashboard'), current: route().current('tbm.dashboard') },
    // Đảm bảo bạn có id bộ môn trước khi tạo route có tham số id
    ...boMonId ? [
      { name: 'Đăng ký', href: route('tbm.dsdangky.index'), current: route().current('tbm.dsdangky.*') },
      // Đưa id vào route yêu cầu tham số
      { name: 'Chi tiết đăng ký', href: route('tbm.ctdsdangky.index', { id: boMonId }), current: route().current('tbm.ctdsdangky.*') }
    ] : [
      // Nếu không có id, có thể hiển thị link rỗng hoặc ẩn đi
      { name: 'Đăng ký', href: '#', current: false }
    ],
    // Các menu khác cho tbm
  ];
} else if (props.role === 'tk') {
  navigation.value = [
    { name: 'Dashboard', href: route('tk.dashboard'), current: route().current('tk.dashboard') },
    { name: 'Đăng ký khoa', href: route('tk.dsdangkykhoa.index'), current: route().current('tk.dsdangkykhoa.*') },
    // Các menu khác cho tk
  ];
} else if (props.role === 'dbcl') {
  navigation.value = [
    { name: 'Dashboard', href: route('dbcl.dashboard'), current: route().current('dbcl.dashboard') },
    { name: 'Quản lý đăng ký', href: route('dbcl.ctdsdangky.index'), current: route().current('dbcl.ctdsdangky.*') },
    // Các menu khác cho dbcl
  ];
}

// Tạo user menu
const userNavigation = [
  { name: 'Thông tin cá nhân', href: route('profile.edit') },
  { name: 'Đổi mật khẩu', href: route('password.edit') },
  { name: 'Đăng xuất', href: route('logout'), method: 'post' }
];

// Debug
console.log('Role:', props.role);
console.log('User:', user);
console.log('Navigation:', navigation.value);
</script>

<template>
  <div class="min-h-full">
    <Disclosure as="nav" class="bg-gray-800" v-slot="{ open }">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <img class="h-8 w-auto" src="/images/logo.png" alt="Hệ thống ngân hàng câu hỏi" />
            </div>
            <div class="hidden md:block">
              <div class="ml-10 flex items-baseline space-x-4">
                <Link 
                  v-for="item in navigation" 
                  :key="item.name" 
                  :href="item.href" 
                  :class="[item.current ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white', 
                    'rounded-md px-3 py-2 text-sm font-medium']"
                >
                  {{ item.name }}
                </Link>
              </div>
            </div>
          </div>
          <div class="hidden md:block">
            <div class="ml-4 flex items-center md:ml-6">
              <!-- Thông tin người dùng -->
              <Menu as="div" class="relative ml-3">
                <div>
                  <MenuButton class="flex max-w-xs items-center rounded-full bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800">
                    <span class="sr-only">Open user menu</span>
                    <span class="text-white px-2">{{ user.name }}</span>
                    <UserIcon class="h-8 w-8 rounded-full bg-gray-600 p-1 text-white" />
                  </MenuButton>
                </div>
                <transition enter-active-class="transition ease-out duration-100" 
                  enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" 
                  leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" 
                  leave-to-class="transform opacity-0 scale-95">
                  <MenuItems class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                    <MenuItem v-for="item in userNavigation" :key="item.name" v-slot="{ active }">
                      <Link :href="item.href" :method="item.method || 'get'" 
                        :class="[active ? 'bg-gray-100' : '', 'block px-4 py-2 text-sm text-gray-700']">
                        {{ item.name }}
                      </Link>
                    </MenuItem>
                  </MenuItems>
                </transition>
              </Menu>
            </div>
          </div>
          <div class="-mr-2 flex md:hidden">
            <!-- Mobile menu button -->
            <DisclosureButton class="inline-flex items-center justify-center rounded-md bg-gray-800 p-2 text-gray-400 hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800">
              <span class="sr-only">Open main menu</span>
              <Bars3Icon v-if="!open" class="block h-6 w-6" aria-hidden="true" />
              <XMarkIcon v-else class="block h-6 w-6" aria-hidden="true" />
            </DisclosureButton>
          </div>
        </div>
      </div>

      <DisclosurePanel class="md:hidden">
        <div class="space-y-1 px-2 pt-2 pb-3 sm:px-3">
          <Link 
            v-for="item in navigation" 
            :key="item.name" 
            :href="item.href" 
            :class="[item.current ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white', 
              'block rounded-md px-3 py-2 text-base font-medium']"
          >
            {{ item.name }}
          </Link>
        </div>
        <div class="border-t border-gray-700 pt-4 pb-3">
          <div class="flex items-center px-5">
            <div class="flex-shrink-0">
              <UserIcon class="h-10 w-10 rounded-full bg-gray-600 p-1 text-white" />
            </div>
            <div class="ml-3">
              <div class="text-base font-medium leading-none text-white">{{ user.name }}</div>
              <div class="text-sm font-medium leading-none text-gray-400 mt-1">{{ user.email }}</div>
            </div>
          </div>
          <div class="mt-3 space-y-1 px-2">
            <Link 
              v-for="item in userNavigation" 
              :key="item.name" 
              :href="item.href" 
              :method="item.method || 'get'"
              class="block rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-gray-700 hover:text-white"
            >
              {{ item.name }}
            </Link>
          </div>
        </div>
      </DisclosurePanel>
    </Disclosure>

    <header class="bg-white shadow">
      <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <slot name="header"></slot>
      </div>
    </header>
    <main>
      <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
        <slot></slot>
      </div>
    </main>
  </div>
</template> 