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
    :pillDivDisplay="`${knowledgeAreaName} | ${form.level}`"
    :showTools="false"
    v-model:level="form.level"
    v-model:comments="form.comments"
  >
    <select
      :disabled="editing"
      required
      v-model="form.knowledge_area_id"
      class="select mt-1 w-full"
    >
      <option value="" selected disabled>- select knowledge area -</option>
      <option
        v-for="knowledgeArea in allKnowledgeAreas"
        :value="knowledgeArea.id"
      >
        {{ knowledgeArea.title }}
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
  topicId: String;
}>();

const allKnowledgeAreas: Array<string> = inject('allKnowledgeAreas');

const emit = defineEmits(emittedEvents);

const useFormData = {
  id: props.coversData.COVERS.id,
  level: props.coversData.COVERS.level ?? '',
  comments: props.coversData.COVERS.comments ?? '',
  topic_id: props.topicId,
  knowledge_area_id: props.coversData.KnowledgeArea.id,
};

const { form, adding, editing, store, update, destroy, id } = useSubformHelpers(
  props.coversData.COVERS,
  useFormData,
  emit,
  route('covers.store'),
  'covers.update',
  'covers.destroy',
);

const knowledgeAreaName = computed(
  () =>
    allKnowledgeAreas.find(
      (knowledgeArea) => knowledgeArea.id === form.knowledge_area_id,
    )?.title,
);
</script>
