<template>
  <CoversSubform
    @formSubmit="store()"
    @save="update()"
    @delete="destroy()"
    @cancelAdd="$emit('cancelAdd')"
    @cancelEditing="editing = false"
    @edit="editing = true"
    @toggleViewing="viewing = !viewing"
    :editing="editing"
    :adding="adding"
    :form="form"
    title="Course"
    :pillDivDisplay="`${courseNumber} | ${form.coverage_level}`"
    v-model:coverageLevel="form.coverage_level"
    v-model:tools="form.tools"
    v-model:comments="form.comments"
  >
    <select
      :disabled="editing"
      required
      v-model="form.course_id"
      class="select mt-1 w-full"
    >
      <option value="" selected disabled>- select course -</option>
      <option v-for="course in allCourses" :value="course.id">
        {{ course.number }}
      </option>
    </select>
  </CoversSubform>
</template>

<script setup lang="ts">
import { emittedEvents, useSubformHelpers } from '@/Helpers/subformHelpers.js';
import { onMounted, watch, computed, inject } from 'vue';
import CoversSubform from '@/Components/Covers/Subform.vue';

const props = defineProps<{
  coversData: Object;
  topicId: String;
}>();

const allCourses: Array<string> = inject('allCourses');

const emit = defineEmits(emittedEvents);

const useFormData = {
  id: props.coversData.id,
  coverage_level: props.coversData.coverage_level ?? '',
  tools: props.coversData.tools ?? '',
  comments: props.coversData.comments ?? '',
  course_id: props.coversData.course.id,
  topic_id: props.topicId,
};

const { form, adding, editing, store, update, destroy, id } = useSubformHelpers(
  props.coversData,
  useFormData,
  emit,
  route('covers.store'),
  'covers.update',
  'covers.destroy',
);

const courseNumber = computed(
  () => allCourses.find((course) => course.id === form.course_id)?.number,
);
</script>
