<template>
  <Head title="Data Entry | Topics" />

  <div
    class="base-card xmt-6 w-full px-4 py-4 text-sm md:w-11/12 md:text-base xl:px-10"
  >
    <p class="form-title mt-2 text-center">Topics</p>
    <div class="mt-6">
      <div
        class="mb-2 mt-8 flex flex-col items-center justify-between gap-y-3 lg:flex-row md:mt-8"
      >
        <div
          class="flex w-full justify-center gap-1 lg:w-3/5 sm:flex-row sm:items-center xl:gap-4 xl:text-base"
        >
          <MagnifyingGlassIcon class="hidden h-5 w-5 text-gray-500 sm:block" />
          <input
            v-model="filter"
            class="input w-full shadow-sm sm:grow"
            placeholder="Search..."
            type="text"
          />
        </div>
        <AddButton
          @click="add()"
          :disabled="!shouldAllowAdd"
          class="w-full font-semibold sm:mr-4 sm:w-fit"
        >
          Add a topic
        </AddButton>
      </div>
      <div class="mt-3 flex flex-col text-sm md:text-base">
        <TransitionGroup name="list">
          <Subform
            v-for="(topic, index) in subformItems"
            :key="topic"
            :topic="topic"
            @cancelAdd="onCancelAdd()"
            @stored="shouldAllowAdd = true"
            @destroyed="onDestroyed(index)"
          />
        </TransitionGroup>

        <p v-if="!subformItems || subformItems.length === 0" class="ml-2">
          No topics found
        </p>
      </div>
      <Pagination
        class="mt-6 flex w-11/12 justify-start"
        :links="initialTopics.links"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { MagnifyingGlassIcon } from '@heroicons/vue/24/solid';
import { Head } from '@inertiajs/vue3';
import AddButton from '@/Components/AddButton.vue';
import Subform from '@/Pages/Topic/Subform.vue';
import { useFormHelpers } from '@/Helpers/formHelpers';
import { provide, ref, watch } from 'vue';
import Pagination from '@/Components/Pagination.vue';
import throttle from 'lodash/throttle';
import { getFilteredItems } from '@/Helpers/helpers';

const props = defineProps<{
  initialTopics: Object;
  allCourses: Array<object>;
  allKnowledgeAreas: Array<object>;
  levels: Array<string>;
  filter: string | null;
}>();

provide('allCourses', props.allCourses);
provide('allKnowledgeAreas', props.allKnowledgeAreas);
provide('levels', props.levels);

const filter = ref(props.filter);

const newTopic = {
  id: null,
  name: null,
};

const { subformItems, shouldAllowAdd, add, onCancelAdd, onDestroyed } =
  useFormHelpers(props.initialTopics.data, newTopic);

watch(
  filter,
  throttle(
    () =>
      getFilteredItems(
        route('topics.form'),
        filter.value,
        subformItems,
        'initialTopics',
      ),
    150,
  ),
);
</script>

<style scoped>
@import '../../../css/subform_transition.css';
</style>
