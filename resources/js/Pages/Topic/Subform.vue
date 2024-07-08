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
    <Combobox v-model="form.name">
      <div class="relative mt-1 w-full">
        <!--        <div
          class="relative w-full overflow-hidden "
        >-->
        <ComboboxInput
          class="input w-full"
          :displayValue="(topicName) => topicName"
          @change="query = $event.target.value"
          as="textarea"
          rows="2"
          required
        />
        <ComboboxButton
          class="absolute inset-y-3 right-0 flex items-center pr-2"
        >
          <ChevronUpDownIcon
            class="mb-0.5 mr-0.5 h-5 w-5 text-gray-800"
            aria-hidden="true"
          />
        </ComboboxButton>
        <!--        </div>-->
        <TransitionRoot
          leave="transition ease-in duration-100"
          leaveFrom="opacity-100"
          leaveTo="opacity-0"
          @after-leave="query = ''"
        >
          <ComboboxOptions
            class="absolute mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black/5 focus:outline-none sm:text-sm"
          >
            <ComboboxOption
              v-if="filteredTopicNames.length === 0 && query !== ''"
              as="template"
              :key="query"
              :value="query"
            >
              <li
                class="relative cursor-default select-none py-2 pl-10 pr-4"
                :class="{
                  'bg-blue-500 text-white': active,
                  'text-gray-900': !active,
                }"
              >
                <span
                  class="block truncate"
                  :class="{ 'font-medium': selected, 'font-normal': !selected }"
                >
                  {{ query }}
                </span>
                <span
                  v-if="selected"
                  class="absolute inset-y-0 left-0 flex items-center pl-3"
                  :class="{ 'text-white': active, 'text-blue-500': !active }"
                >
                  <CheckIcon class="h-5 w-5" aria-hidden="true" />
                </span>
              </li>
            </ComboboxOption>

            <ComboboxOption
              v-for="topicName in filteredTopicNames"
              as="template"
              :key="topicName"
              :value="topicName"
              v-slot="{ selected, active }"
            >
              <li
                class="relative cursor-default select-none py-2 pl-10 pr-4"
                :class="{
                  'bg-blue-500 text-white': active,
                  'text-gray-900': !active,
                }"
              >
                <span
                  class="block truncate"
                  :class="{ 'font-medium': selected, 'font-normal': !selected }"
                >
                  {{ topicName }}
                </span>
                <span
                  v-if="selected"
                  class="absolute inset-y-0 left-0 flex items-center pl-3"
                  :class="{ 'text-white': active, 'text-blue-500': !active }"
                >
                  <CheckIcon class="h-5 w-5" aria-hidden="true" />
                </span>
              </li>
            </ComboboxOption>
          </ComboboxOptions>
        </TransitionRoot>
      </div>
    </Combobox>
    <!--    <textarea
      required
      v-model="form.name"
      rows="2"
      class="input mt-2 w-full"
    ></textarea>-->
    <label class="label mt-4 block">Coverage level</label>
    <select required v-model="form.coverage_level" class="select mt-1 w-full">
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
  <PillDiv class="my-auto" v-else>
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
import { onMounted, watch, ref, computed } from 'vue';
import {
  Combobox,
  ComboboxInput,
  ComboboxButton,
  ComboboxOptions,
  ComboboxOption,
  TransitionRoot,
} from '@headlessui/vue';
import { CheckIcon, ChevronUpDownIcon } from '@heroicons/vue/20/solid';

const props = defineProps<{
  courseId: string;
  topic: Object;
  allTopicNames: Array<string>;
  courseTopicEdgeWeights: Array<string>;
}>();

let query = ref('');

let filteredTopicNames = computed(() =>
  query.value === ''
    ? props.allTopicNames
    : props.allTopicNames.filter((topicName) =>
        String(topicName)
          .toLowerCase()
          .replace(/\s+/g, '')
          .includes(query.value.toLowerCase().replace(/\s+/g, '')),
      ),
);

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
