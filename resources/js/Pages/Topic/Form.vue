<template>
  <Head title="Data Entry | Topics" />

  <div
    class="base-card xmt-6 w-full px-4 py-4 text-sm md:w-11/12 md:text-base xl:px-10"
  >
    <p class="form-title mt-2 text-center">Topics</p>
    <div class="mt-6">
      <div>
        <label class="label block"> Select a course </label
        ><select v-model="courseId" class="select mt-2 w-full">
          <option value="" selected disabled>- select a course -</option>
          <option v-for="course in courses" :value="course.id">
            {{ course.number }} | {{ course.title }}
          </option>
        </select>
      </div>
      <div class="form-title-div mt-8">
        <span class="subform-title">Topics</span>
        <AddButton
          @click="add()"
          class="self-center sm:mr-4"
          :disabled="!shouldAllowAdd || !courseId"
          >Add a topic</AddButton
        >
      </div>
      <div class="mt-4 flex flex-wrap gap-x-5 gap-y-3 rounded-lg border p-4">
        <Subform
          v-for="topic in subformItems"
          :courseId="courseId"
          :topic="topic"
          :courseTopicEdgeWeights="courseTopicEdgeWeights"
          @cancelAdd="onCancelAdd()"
          @stored="shouldAllowAdd = true"
          @destroyed="onDestroyed(index)"
        />
        <p
          v-if="courseId && (!subformItems || subformItems.length === 0)"
          class="ml-2"
        >
          No topics found
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head } from '@inertiajs/vue3';
import AddButton from '@/Components/AddButton.vue';
import Subform from '@/Pages/Topic/Subform.vue';
import { useFormHelpers } from '@/Helpers/formHelpers';

const props = defineProps<{
  courses: Array<object>;
  courseTopicEdgeWeights: Array<string>;
}>();

const courseId = ref('');

const newTopic = {
  id: null,
  name: '',
};

const { subformItems, shouldAllowAdd, add, onCancelAdd, onDestroyed } =
  useFormHelpers([], newTopic);

watch(courseId, (newCourseId) => {
  if (!courseId) {
    subformItems.value = [];
    return;
  }

  axios.get(route('topics.get_topics', newCourseId)).then((response) => {
    subformItems.value = response.data;
  });

  shouldAllowAdd.value = true;
});
</script>
