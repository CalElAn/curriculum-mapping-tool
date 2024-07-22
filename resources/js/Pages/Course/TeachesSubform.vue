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
    title="Topic"
    :pillDivDisplay="`${topicName} | ${form.level}`"
    v-model:level="form.level"
    v-model:tools="form.tools"
    v-model:comments="form.comments"
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
  </GenericRelationshipSubform>
</template>

<script setup lang="ts">
import GenericRelationshipSubform from '@/Components/GenericRelationshipSubform.vue';
import { emittedEvents, useSubformHelpers } from '@/Helpers/subformHelpers.js';
import { onMounted, watch, computed, inject } from 'vue';

const props = defineProps<{
  teachesData: Object;
  courseId: String;
}>();

const allTopics: Array<string> = inject('allTopics');

const emit = defineEmits(emittedEvents);

const useFormData = {
  id: props.teachesData.TEACHES.id,
  level: props.teachesData.TEACHES.level ?? '',
  tools: props.teachesData.TEACHES.tools ?? '',
  comments: props.teachesData.TEACHES.comments ?? '',
  topic_id: props.teachesData.Topic.id,
  course_id: props.courseId,
};

const { form, adding, editing, store, update, destroy, id } = useSubformHelpers(
  props.teachesData,
  useFormData,
  emit,
  route('teaches.store'),
  'teaches.update',
  'teaches.destroy',
);

const topicName = computed(
  () => allTopics.find((topic) => topic.id === form.topic_id)?.name,
);
</script>
