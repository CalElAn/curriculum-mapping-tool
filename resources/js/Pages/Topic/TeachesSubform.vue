<template>
  <GenericRelationshipSubform
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
    :pillDivDisplay="`${courseNumber} | ${form.level}`"
    v-model:level="form.level"
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
  </GenericRelationshipSubform>
</template>

<script setup lang="ts">
import { emittedEvents, useSubformHelpers } from '@/Helpers/subformHelpers.js';
import { onMounted, watch, computed, inject } from 'vue';
import GenericRelationshipSubform from '@/Components/GenericRelationshipSubform.vue';

const props = defineProps<{
  teachesData: Object;
  topicId: String;
}>();

const allCourses: Array<string> = inject('allCourses');

const emit = defineEmits(emittedEvents);

const useFormData = {
  id: props.teachesData.TEACHES.id,
  level: props.teachesData.TEACHES.level ?? '',
  tools: props.teachesData.TEACHES.tools ?? '',
  comments: props.teachesData.TEACHES.comments ?? '',
  course_id: props.teachesData.Course.id,
  topic_id: props.topicId,
};

const { form, adding, editing, store, update, destroy, id } = useSubformHelpers(
  props.teachesData,
  useFormData,
  emit,
  route('teaches.store'),
  'teaches.update',
  'teaches.destroy',
);

const courseNumber = computed(
  () => allCourses.find((course) => course.id === form.course_id)?.number,
);
</script>
