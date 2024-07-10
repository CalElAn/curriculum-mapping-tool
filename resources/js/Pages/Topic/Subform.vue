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
      :viewingText="`Courses covering this topic`"
      :form="form"
    />
    <template #viewingContainer>
      <div
        v-if="viewing && topic.id"
        class="col-span-1 mt-4 flex flex-wrap gap-x-5 gap-y-3 rounded-lg border p-4"
      >
        <div v-for="item in subformItems" class="flex items-center gap-2">
          <CourseAllocationSubform :courseData="item" />
        </div>
      </div>
    </template>
  </SubformWrapper>
</template>

<script setup lang="ts">
import SubformWrapper from '@/Components/SubformWrapper.vue';
import FormValidationErrors from '@/Components/FormValidationErrors.vue';
import SubformButton from '@/Components/SubformButton.vue';
import { emittedEvents, useSubformHelpers } from '@/Helpers/subformHelpers.js';
import { SquaresPlusIcon, Squares2X2Icon } from '@heroicons/vue/24/outline';
import { computed, ref, watch } from 'vue';
import CourseAllocationSubform from '@/Pages/Topic/CourseAllocationSubform.vue';
import { useFormHelpers } from '@/Helpers/formHelpers';
import AllSubformButtons from '@/Components/AllSubformButtons.vue';

const props = defineProps<{
  topic: Object;
}>();

const viewing = ref(false);

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

const newCoversRelationship = computed(() => ({
  topic_id: id.value,
  course: null,
  coverage_level: null,
}));

const { subformItems, shouldAllowAdd, add, onCancelAdd, onDestroyed } =
  useFormHelpers([], newCoversRelationship);

watch(viewing, (shouldAllocate) => {
  if (!shouldAllocate) {
    subformItems.value = [];
    return;
  }

  axios.get(route('topics.get_courses', id.value)).then((response) => {
    subformItems.value = response.data;
  });

  shouldAllowAdd.value = true;
});
</script>
