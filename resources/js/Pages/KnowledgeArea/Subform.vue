<template>
  <SubformWrapper @submit.prevent="store()" :adding="adding" class="">
    <input
      readonly
      type="text"
      class="input col-span-full w-full"
      v-model="form.title"
    />
    <textarea
      readonly
      rows="2"
      placeholder="name"
      required
      class="input col-span-full w-full"
      type="text"
      v-model="form.description"
    ></textarea>
    <FormValidationErrors class="sm:col-span-full" :errors="form.errors" />
    <div class="flex justify-end sm:col-span-full sm:mr-4">
      <SubformButton
        iconType="view"
        :shouldRotateIcon="viewing"
        @click="viewing = !viewing"
        class=""
      >
        {{ viewing ? '' : `Relationships` }}
      </SubformButton>
    </div>
    <template v-if="viewing && id" #viewingContainer>
      <div class="viewing-subform-container">
        <div>
          <span class="subform-title"
            >Topics that teach this knowledge area</span
          >
        </div>
        <div class="col-span-1 mt-4 flex flex-wrap gap-x-5 gap-y-3">
          <VueElementLoading :showLoadingSpinner="showLoadingSpinner" />
          <AddButton
            @click="add()"
            :disabled="!shouldAllowAdd"
            class="my-auto text-sm"
          >
            Add a relationship
          </AddButton>
          <CoversSubform
            v-for="(item, index) in subformItems"
            :key="item"
            :coversData="item"
            :knowledgeAreaId="id"
            @cancelAdd="onCancelAdd()"
            @stored="shouldAllowAdd = true"
            @destroyed="onDestroyed(index)"
          />
        </div>
      </div>
    </template>
  </SubformWrapper>
</template>

<script setup lang="ts">
import SubformWrapper from '@/Components/SubformWrapper.vue';
import FormValidationErrors from '@/Components/FormValidationErrors.vue';
import { emittedEvents, useSubformHelpers } from '@/Helpers/subformHelpers.js';
import { ref, watch } from 'vue';
import CoversSubform from '@/Pages/KnowledgeArea/CoversSubform.vue';
import { handleViewing, useFormHelpers } from '@/Helpers/formHelpers';
import AddButton from '@/Components/AddButton.vue';
import SubformButton from '@/Components/SubformButton.vue';
import VueElementLoading from '@/Components/VueElementLoading.vue';

const props = defineProps<{
  knowledgeArea: Object;
}>();

const viewing = ref(false);
const showLoadingSpinner = ref(false);

const formData = {
  id: props.knowledgeArea.id,
  title: props.knowledgeArea.title,
  description: props.knowledgeArea.description,
};

const emit = defineEmits(emittedEvents);

const { id, form, adding, editing, store, update, destroy } = useSubformHelpers(
  props.knowledgeArea,
  formData,
  emit,
  route('knowledge_areas.store'),
  'knowledge_areas.update',
  'knowledge_areas.destroy',
);

const newTeachesRelationship = {
  COVERS: {
    id: null,
    level: '',
  },
  Topic: {
    id: '',
  },
};

const { subformItems, shouldAllowAdd, add, onCancelAdd, onDestroyed } =
  useFormHelpers([], newTeachesRelationship);

watch(viewing, (shouldView) => {
  handleViewing(
    shouldView,
    subformItems,
    showLoadingSpinner,
    route('knowledge_areas.get_topics', id.value),
  );
});
</script>
