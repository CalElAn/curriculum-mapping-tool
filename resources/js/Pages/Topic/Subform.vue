<template>
  <SubformWrapper @submit.prevent="store()" :adding="adding" class="">
    <textarea
      :readonly="!editing && !adding"
      rows="2"
      placeholder="name"
      required
      class="input col-span-full w-full"
      type="text"
      v-model="form.name"
    ></textarea>
    <FormValidationErrors class="sm:col-span-full" :errors="form.errors" />
    <AllSubformButtons
      class=""
      @cancelAdd="$emit('cancelAdd')"
      @save="update()"
      @delete="destroy()"
      @cancelEditing="editing = false"
      @edit="editing = true"
      @toggleViewing="viewing = !viewing"
      :adding="adding"
      :editing="editing"
      :viewing="viewing"
      :viewingText="`Relationships`"
      :form="form"
    />
    <template v-if="viewing && id" #viewingContainer>
      <!-- TEACHES relationship -->
      <div class="viewing-subform-container">
        <div>
          <span class="subform-title">Courses teaching this topic</span>
        </div>
        <div class="col-span-1 mt-4 flex flex-wrap gap-x-5 gap-y-3">
          <VueElementLoading :showLoadingSpinner="showTeachesLoadingSpinner" />
          <div class="w-full">
            <AddButton
              @click="addTeaches()"
              :disabled="!shouldAllowAddTeaches"
              class="my-auto text-sm"
            >
              Add a relationship
            </AddButton>
          </div>
          <TeachesSubform
            v-for="(item, index) in teachesSubformItems"
            :key="item"
            :teachesData="item"
            :topicId="id"
            @cancelAdd="onCancelAddTeaches()"
            @stored="shouldAllowAddTeaches = true"
            @destroyed="onDestroyedTeaches(index)"
          />
        </div>
      </div>
      <div class="viewing-subform-container mt-4">
        <div>
          <span class="subform-title"
            >Knowledge areas covered by this topic</span
          >
        </div>
        <div class="col-span-1 mt-4 flex flex-wrap gap-x-5 gap-y-3">
          <VueElementLoading :showLoadingSpinner="showCoversLoadingSpinner" />
          <div class="w-full">
            <AddButton
              @click="addCovers()"
              :disabled="!shouldAllowAddCovers"
              class="my-auto text-sm"
            >
              Add a relationship
            </AddButton>
          </div>
          <CoversSubform
            v-for="(item, index) in coversSubformItems"
            :key="item"
            :coversData="item"
            :topicId="id"
            @cancelAdd="onCancelAddCovers()"
            @stored="shouldAllowAddCovers = true"
            @destroyed="onDestroyedCovers(index)"
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
import TeachesSubform from '@/Pages/Topic/TeachesSubform.vue';
import CoversSubform from '@/Pages/Topic/CoversSubform.vue';
import { handleViewing, useFormHelpers } from '@/Helpers/formHelpers';
import AllSubformButtons from '@/Components/AllSubformButtons.vue';
import AddButton from '@/Components/AddButton.vue';
import VueElementLoading from '@/Components/VueElementLoading.vue';

const props = defineProps<{
  topic: Object;
}>();

const viewing = ref(false);
const showTeachesLoadingSpinner = ref(false);
const showCoversLoadingSpinner = ref(false);

const formData = {
  id: props.topic.id,
  name: props.topic.name,
};

const emit = defineEmits(emittedEvents);

const { id, form, adding, editing, store, update, destroy } = useSubformHelpers(
  props.topic,
  formData,
  emit,
  route('topics.store'),
  'topics.update',
  'topics.destroy',
);

// TEACHES relationship
const newTeachesRelationship = {
  TEACHES: {
    id: null,
    level: '',
    adding: true,
  },
  Course: {
    id: '',
  },
};

const {
  subformItems: teachesSubformItems,
  shouldAllowAdd: shouldAllowAddTeaches,
  add: addTeaches,
  onCancelAdd: onCancelAddTeaches,
  onDestroyed: onDestroyedTeaches,
} = useFormHelpers([], newTeachesRelationship);

watch(viewing, (shouldView) => {
  handleViewing(
    shouldView,
    teachesSubformItems,
    showTeachesLoadingSpinner,
    route('topics.get_courses', id.value),
  );
});

// COVERS relationship
const newCoversRelationship = {
  COVERS: {
    id: null,
    level: '',
    adding: true,
  },
  KnowledgeArea: {
    id: '',
  },
};

const {
  subformItems: coversSubformItems,
  shouldAllowAdd: shouldAllowAddCovers,
  add: addCovers,
  onCancelAdd: onCancelAddCovers,
  onDestroyed: onDestroyedCovers,
} = useFormHelpers([], newCoversRelationship);

watch(viewing, (shouldView) => {
  handleViewing(
    shouldView,
    coversSubformItems,
    showCoversLoadingSpinner,
    route('topics.get_knowledge_areas', id.value),
  );
});
</script>
