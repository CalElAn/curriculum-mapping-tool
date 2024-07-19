<template>
  <Head title="Data Entry | Courses" />

  <div
    class="base-card xmt-6 w-full px-4 py-4 text-sm md:w-11/12 md:text-base xl:px-10"
  >
    <p class="form-title mt-2 text-center">Courses</p>
    <div class="mt-6">
      <div class="mb-2 mt-8 flex items-center justify-between md:mt-8">
        <div
          class="flex w-3/5 justify-center gap-1 sm:flex-row sm:items-center xl:gap-4 xl:text-base"
        >
          <MagnifyingGlassIcon class="hidden h-6 w-6 text-gray-500 sm:block" />
          <input
            v-model="filter"
            class="input w-full shadow-sm sm:grow"
            placeholder="Search..."
            type="text"
          />
        </div>
<!--        <AddButton @click="add()" :disabled="!shouldAllowAdd" class="mr-4 font-semibold">
          Add a course
        </AddButton>-->
      </div>
      <div class="flex flex-col text-sm md:text-base">
        <TransitionGroup name="list">
          <Subform
            v-for="(course, index) in subformItems"
            :key="course"
            :course="course"
            @cancelAdd="onCancelAdd()"
            @stored="shouldAllowAdd = true"
            @destroyed="onDestroyed(index)"
          />
        </TransitionGroup>

        <p v-if="!subformItems || subformItems.length === 0" class="ml-2">
          No courses found
        </p>
      </div>
      <Pagination
        class="mt-6 flex w-11/12 justify-start"
        :links="initialCourses.links"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { MagnifyingGlassIcon } from '@heroicons/vue/24/solid';
import { Head } from '@inertiajs/vue3';
import AddButton from '@/Components/AddButton.vue';
import Subform from '@/Pages/Course/Subform.vue';
import { useFormHelpers } from '@/Helpers/formHelpers';
import { provide, ref, watch } from 'vue';
import Pagination from '@/Components/Pagination.vue';
import throttle from 'lodash/throttle';
import { getFilteredItems } from '@/Helpers/helpers';

const props = defineProps<{
  initialCourses: Object;
  allTopics: Array<object>;
  coverageLevels: Array<string>;
  filter: string | null;
}>();

provide('allTopics', props.allTopics);
provide('coverageLevels', props.coverageLevels);

const filter = ref(props.filter);

watch(
  filter,
  throttle(() => getFilteredItems(route('courses.form'), filter.value), 150),
);

const newCourse = {
  id: null,
  number: null,
  title: null,
};

const { subformItems, shouldAllowAdd, add, onCancelAdd, onDestroyed } =
  useFormHelpers(props.initialCourses.data, newCourse);
</script>

<style scoped>
@import '../../../css/subform_transition.css';
</style>