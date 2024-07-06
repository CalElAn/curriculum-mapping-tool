<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { SquaresPlusIcon } from '@heroicons/vue/24/outline';
import TabLink from '@/Components/TabLink.vue';
</script>

<template>
  <button
    data-drawer-target="logo-sidebar"
    data-drawer-toggle="logo-sidebar"
    aria-controls="logo-sidebar"
    type="button"
    class="ms-3 mt-2 inline-flex items-center rounded-2xl p-2 text-sm text-gray-500 shadow hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600 sm:hidden"
  >
    <span class="sr-only">Open sidebar</span>
    <svg
      class="h-6 w-6"
      aria-hidden="true"
      fill="currentColor"
      viewBox="0 0 20 20"
      xmlns="http://www.w3.org/2000/svg"
    >
      <path
        clip-rule="evenodd"
        fill-rule="evenodd"
        d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"
      ></path>
    </svg>
  </button>

  <!-- Sidebar -->
  <aside
    id="logo-sidebar"
    class="fixed left-0 top-0 z-40 h-screen w-64 -translate-x-full bg-gray-50 transition-transform sm:translate-x-0 sm:p-2"
    aria-label="Sidebar"
  >
    <div
      class="h-full overflow-y-auto rounded-2xl border bg-white px-3 py-4 dark:bg-gray-800"
    >
      <Link
        href="/"
        class="mb-5 flex items-center ps-2.5 text-lg font-semibold tracking-wide"
      >
        Curriculum Mapping Tool
        <!--        <span
                    class="self-center text-xl font-semibold whitespace-nowrap dark:text-white"
                    >Flowbite</span
                  >-->
      </Link>
      <ul class="mt-8 space-y-4 font-medium">
        <li>
          <Link
            :class="[
              $page.url.startsWith('/data-entry')
                ? 'bg-red-200 font-semibold text-red-600'
                : 'font-medium hover:text-red-600 hover:underline',
            ]"
            :href="route('topics.form')"
            class="flex w-full items-center justify-start gap-2 rounded-lg py-2 pl-3 tracking-wide"
          >
            <SquaresPlusIcon class="h-5 w-5 text-red-600" />
            Data Entry
          </Link>
        </li>
        <li>
          <Link
            :class="[
              $page.url.startsWith('/visualization')
                ? 'bg-red-200 font-semibold text-red-600'
                : 'font-medium hover:text-red-600 hover:underline',
            ]"
            :href="route('topics.visualization')"
            class="flex w-full items-center justify-start gap-2 rounded-lg py-2 pl-3 tracking-wide"
          >
            <svg
              class="h-5 w-5 text-red-600"
              aria-hidden="true"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
            >
              <path
                stroke="currentColor"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 4v15a1 1 0 0 0 1 1h15M8 16l2.5-5.5 3 3L17.273 7 20 9.667"
              />
            </svg>

            Visualization
          </Link>
        </li>
      </ul>
    </div>
  </aside>

  <div class="min-h-screen space-y-6 bg-gray-50 px-4 py-2 sm:ml-64">
    <header
      class="w-full rounded-2xl bg-white px-4 py-2 text-sm shadow md:w-11/12 xl:px-10"
    >
      <nav class="flex w-full items-center justify-end" aria-label="Global">
        <!--        <div class="h-9 w-9 rounded-full bg-gray-200" aria-hidden="true"></div>-->

        <button
          id="dropdownAvatarNameButton"
          data-dropdown-toggle="dropdownAvatarName"
          class="flex items-center rounded-full pe-1 text-sm font-medium text-gray-900 hover:text-blue-600 focus:ring-4 focus:ring-gray-100 dark:text-white dark:hover:text-blue-500 dark:focus:ring-gray-700 md:me-0"
          type="button"
        >
          <span class="sr-only">Open user menu</span>
          <div
            class="me-2 h-8 w-8 rounded-full bg-gray-200"
            alt="user photo"
          ></div>
          Bonnie Green
          <svg
            class="ms-3 h-2.5 w-2.5"
            aria-hidden="true"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 10 6"
          >
            <path
              stroke="currentColor"
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="m1 1 4 4 4-4"
            />
          </svg>
        </button>

        <!-- Dropdown menu -->
        <div
          id="dropdownAvatarName"
          class="z-10 hidden w-44 divide-y divide-gray-100 rounded-lg bg-white shadow dark:divide-gray-600 dark:bg-gray-700"
        >
          <!--          <div class="px-4 py-3 text-sm text-gray-900 dark:text-white">
            <div class="font-medium">Pro User</div>
            <div class="truncate">name@flowbite.com</div>
          </div>-->
          <ul
            class="py-2 text-sm text-gray-700 dark:text-gray-200"
            aria-labelledby="dropdownInformdropdownAvatarNameButtonationButton"
          >
            <li>
              <a
                href="#"
                class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                >Profile</a
              >
              <!-- TODO Profile link and page -->
            </li>
          </ul>
          <div class="py-2">
            <Link
              @click="router.post(route('logout'))"
              as="button"
              type="button"
              href="#"
              class="block w-full px-4 py-2 text-start text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600 dark:hover:text-white"
              >Sign out</Link
            >
          </div>
        </div>
      </nav>
    </header>

    <div
      class="border-b border-gray-200 text-center text-base font-medium text-gray-500 dark:border-gray-700 dark:text-gray-400 md:w-11/12"
    >
      <ul class="-mb-px flex flex-wrap">
        <li class="me-2">
          <TabLink title="Topics" activeIfIncludes="topics" />
        </li>
        <li class="me-2">
          <TabLink title="Course Outcomes" activeIfIncludes="course-outcomes" />
        </li>
        <li class="me-2">
          <TabLink
            title="Program Outcomes"
            activeIfIncludes="program-outcomes"
          />
        </li>
      </ul>
    </div>
    <slot />
  </div>
</template>
