<template>
  <div v-if="links.length > 3">
    <div class="flex flex-wrap justify-center gap-2">
      <template v-for="(link, key) in links">
        <div
          v-if="link.url === null"
          :key="key"
          class="mb-1 rounded-lg bg-white px-4 py-3 leading-4 text-gray-400 border shadow-sm"
          v-html="link.label"
        />
        <template v-else>
          <component
            :is="shouldPaginateWithAxios ? 'button' : Link"
            @click="shouldPaginateWithAxios ? visitLink(link.url) : ''"
            :href="link.url"
            :key="`link-${key}`"
            class="mb-1 rounded-lg border px-4 py-3 leading-4 shadow-sm hover:border-red-600 hover:text-red-600 focus:border-red-600 focus:text-red-600"
            :class="[
              link.active
                ? 'border-red-600 font-semibold text-red-600'
                : 'border-gray-400 text-gray-500',
            ]"
            v-html="link.label"
          ></component>
        </template>
      </template>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
const props = defineProps({
  links: Array,
  shouldPaginateWithAxios: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['visitedLink']);

function visitLink(url) {
  axios.get(url).then((response) => {
    emit('visitedLink', response.data);
  });
}
</script>
