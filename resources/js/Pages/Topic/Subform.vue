<template>
  <form
    @submit.prevent="store()"
    class="rounded-xl border p-4 shadow-sm"
    v-if="editing || adding"
    :class="{
      'subform-ring': adding,
    }"
  >
    <label class="label">Name</label>
    <textarea
      required
      v-model="form.name"
      rows="2"
      class="input mt-2 w-full"
    ></textarea>
    <label class="label">Coverage level</label>
    <select required v-model="form.coverage_level" class="select w-full">
      <option v-for="weight in courseTopicEdgeWeights">
        {{ weight }}
      </option>
    </select>
    <FormValidationErrors class="mt-2 sm:col-span-full" :errors="form.errors" />
    <AllSubformButtons
      class="mt-4"
      @cancelAdd="$emit('cancelAdd')"
      @save="update()"
      @delete="destroy()"
      @cancelEditing="editing = false"
      @edit="editing = true"
      @toggleViewing="viewing = !viewing"
      :adding="adding"
      :editing="editing"
      :form="form"
    />
  </form>
  <PillDiv v-else>
    {{ form.name }} | {{ form.coverage_level }}

    <button
      id="dropdownMenuIconButton"
      :data-dropdown-toggle="`dropdownDots${random}`"
      data-dropdown-placement="right"
      class="xrounded-lg xbg-white xtext-center xtext-sm xfont-medium xhover:bg-gray-100 xfocus:outline-none xfocus:ring-4 xfocus:ring-gray-50 inline-flex items-center p-1 text-gray-900"
      type="button"
    >
      <EllipsisVerticalIcon class="h-5 w-5" />
    </button>

    <!-- Dropdown menu -->
    <div
      :id="`dropdownDots${random}`"
      class="z-10 hidden w-44 space-y-2 divide-y divide-gray-100 rounded-2xl bg-white py-2 text-sm shadow dark:divide-gray-600 dark:bg-gray-700"
    >
      <ul
        class="px-2 text-gray-700 dark:text-gray-200"
        aria-labelledby="dropdownMenuIconButton"
      >
        <li>
          <button
            @click="editing = true"
            class="block w-full px-2 py-1 text-start hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
          >
            Edit
          </button>
        </li>
      </ul>
      <div class="px-2">
        <a
          href="#"
          class="block px-2 py-1 text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600 dark:hover:text-white"
          >Link to Course Outcome</a
        >
      </div>
    </div>
  </PillDiv>
</template>

<script setup lang="ts">
import { EllipsisVerticalIcon } from '@heroicons/vue/20/solid';
import AllSubformButtons from '@/Components/AllSubformButtons.vue';
import FormValidationErrors from '@/Components/FormValidationErrors.vue';
import PillDiv from '@/Components/PillDiv.vue';
import { emittedEvents, useSubformHelpers } from '@/Helpers/subformHelpers.js';
import { initFlowbite } from 'flowbite';
import { onMounted, watch } from 'vue';

const props = defineProps<{
  courseId: string;
  topic: Object;
  courseTopicEdgeWeights: Array<string>;
}>();

const emit = defineEmits(emittedEvents);

const useFormData = {
  name: props.topic.name,
  coverage_level: props.topic.coverage_level ?? props.courseTopicEdgeWeights[0],
};

const { form, adding, editing, store, update, destroy, id } = useSubformHelpers(
  props.topic,
  useFormData,
  emit,
  route('topics.store', props.courseId),
  'topics.update',
  [props.courseId],
  'topics.destroy',
);

const random = Math.round(Math.random() * 10000);

// Flowbite tooltip doesn't work if element was dynamically rendered after initial page initialization.
// below are necessary so the tooltip can function.
watch(adding, () => initFlowbite(), { flush: 'post' });

onMounted(() => initFlowbite());
</script>
