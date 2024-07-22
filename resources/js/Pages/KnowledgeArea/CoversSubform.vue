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
    :showTools="false"
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
  coversData: Object;
  knowledgeAreaId: String;
}>();

const allTopics: Array<string> = inject('allTopics');

const emit = defineEmits(emittedEvents);

const useFormData = {
  id: props.coversData.COVERS.id,
  level: props.coversData.COVERS.level ?? '',
  tools: props.coversData.COVERS.tools ?? '',
  comments: props.coversData.COVERS.comments ?? '',
  topic_id: props.coversData.Topic.id,
  knowledge_area_id: props.knowledgeAreaId,
};

const { form, adding, editing, store, update, destroy, id } = useSubformHelpers(
  props.coversData,
  useFormData,
  emit,
  route('covers.store'),
  'covers.update',
  'covers.destroy',
);

const topicName = computed(
  () => allTopics.find((topic) => topic.id === form.topic_id)?.name,
);
</script>
