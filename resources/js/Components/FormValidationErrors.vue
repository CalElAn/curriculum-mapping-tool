<template>
  <div
    v-if="Object.keys(errors).length"
    class="my-2 rounded-md border border-red-500 p-2 text-sm text-red-500"
  >
    <ul class="list-inside list-disc">
      <template v-for="item in errors" :key="item">
        <template v-if="typeof item === 'object'">
          <li v-for="secondItem in item">
            {{ secondItem }}
          </li>
        </template>
        <li v-else>
          {{ item }}
        </li>
      </template>
    </ul>
  </div>
</template>

<script setup>
defineProps({ errors: Object });
</script>
