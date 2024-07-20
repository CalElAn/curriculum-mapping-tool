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
    title="Topic"
    :pillDivDisplay="`${topicName} | ${form.coverage_level}`"
    v-model:coverageLevel="form.coverage_level"
  >
    <select
      :disabled="editing"
      required
      v-model="form.topic_id"
      class="select mt-1 w-full"
    >
      <option value="" selected disabled>- select topic -</option>
      <option v-for="topic in allTopics" :value="topic.id">
        {{ topic.name }}
      </option>
    </select>
  </CoversSubform>
</template>

<script setup lang="ts">
import CoversSubform from '@/Components/Covers/Subform.vue';
import { emittedEvents, useSubformHelpers } from '@/Helpers/subformHelpers.js';
import { onMounted, watch, computed, inject } from 'vue';

const props = defineProps<{
  coversData: Object;
  courseId: String;
}>();

const allTopics: Array<string> = inject('allTopics');

const emit = defineEmits(emittedEvents);

const useFormData = {
  id: props.coversData.id,
  coverage_level: props.coversData.coverage_level ?? '',
  course_id: props.courseId,
  topic_id: props.coversData.topic.id,
};

const { form, adding, editing, store, update, destroy, id } = useSubformHelpers(
  props.coversData,
  useFormData,
  emit,
  route('covers.store'),
  'covers.update',
  [],
  'covers.destroy',
);

const topicName = computed(
  () => allTopics.find((topic) => topic.id === form.topic_id)?.name,
);
</script>
