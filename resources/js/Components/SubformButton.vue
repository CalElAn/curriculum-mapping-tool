<template>
  <button
    :type="type"
    :class="[
      iconType === 'delete'
        ? 'border-red-600 text-red-600 hover:bg-red-400'
        : 'border-amber-600 text-amber-600 hover:bg-amber-400',
    ]"
    class="my-auto flex flex-wrap items-center justify-between gap-1 rounded-lg border px-2 py-1 text-sm font-medium tracking-wide shadow-sm hover:border-transparent hover:text-white disabled:opacity-50 disabled:hover:cursor-text xl:font-semibold xl:tracking-wider"
  >
    <component
      :is="icon"
      class="h-5 w-5 transition-transform"
      :class="{ 'rotate-180': shouldRotateIcon }"
    />
    <slot />
  </button>
</template>

<script setup>
import {
  ArrowDownCircleIcon,
  CheckIcon,
  PencilSquareIcon,
  TrashIcon,
  XMarkIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
  type: {
    type: String,
    default: 'button',
  },
  iconType: String,
  shouldRotateIcon: { type: Boolean, default: false },
});

const icon = {
  view: ArrowDownCircleIcon,
  edit: PencilSquareIcon,
  save: CheckIcon,
  delete: TrashIcon,
  cancel: XMarkIcon,
}[props.iconType];
</script>
