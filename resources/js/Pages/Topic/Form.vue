<template>
  <Head title="Data Entry | Topics" />

  <div
    class="base-card xmt-6 w-full px-4 py-4 text-sm md:w-11/12 md:text-base xl:px-10"
  >
    <p class="form-title mt-2 text-center">Topics</p>
    <div class="mt-6">
      <div class="mb-2 mt-8 flex justify-end md:mt-8">
        <AddButton @click="add()" :disabled="!shouldAllowAdd" class="mr-4">
          Add a topic
        </AddButton>
      </div>
      <div class="flex flex-col text-sm md:text-base">
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
    </div>
  </div>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AddButton from '@/Components/AddButton.vue';
import Subform from '@/Pages/Topic/Subform.vue';
import { useFormHelpers } from '@/Helpers/formHelpers';
import { provide } from 'vue';

const props = defineProps<{
  initialTopics: Array<object>;
  allCourses: Array<object>;
  coverageLevels: Array<string>;
}>();

provide('allCourses', props.allCourses);
provide('coverageLevels', props.coverageLevels);

const newTopic = {
  id: null,
  name: null,
};

const { subformItems, shouldAllowAdd, add, onCancelAdd, onDestroyed } =
  useFormHelpers(props.initialTopics, newTopic);
</script>

<style scoped>
@import '../../../css/subform_transition.css';
</style>
