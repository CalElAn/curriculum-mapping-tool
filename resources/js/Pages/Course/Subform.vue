<template>
  <SubformWrapper @submit.prevent="store()" :adding="adding" class="">
    <input
      readonly
      type="text"
      class="input col-span-full w-full"
      v-model="form.number"
    />
    <textarea
      readonly
      rows="2"
      placeholder="name"
      required
      class="input col-span-full w-full"
      type="text"
      v-model="form.title"
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
          <span class="subform-title">Topics covered by this course</span>
        </div>
        <div class="col-span-1 mt-4 flex flex-wrap gap-x-5 gap-y-3">
          <AddButton
            @click="add()"
            :disabled="!shouldAllowAdd"
            class="my-auto text-sm"
          >
            Add a relationship
          </AddButton>
          <CourseAllocationSubform
            v-for="(item, index) in subformItems"
            :key="item"
            :coversData="item"
            :courseId="id"
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
import { computed, ref, watch } from 'vue';
import CourseAllocationSubform from '@/Pages/Course/CoversSubform.vue';
import { useFormHelpers } from '@/Helpers/formHelpers';
import AllSubformButtons from '@/Components/AllSubformButtons.vue';
import AddButton from '@/Components/AddButton.vue';
import SubformButton from '@/Components/SubformButton.vue';

const props = defineProps<{
  course: Object;
}>();

const viewing = ref(false);

const formData = {
  id: props.course.id,
  number: props.course.number,
  title: props.course.title,
};

const emit = defineEmits(emittedEvents);

const { id, form, adding, editing, store, update, destroy } = useSubformHelpers(
  props.course,
  formData,
  emit,
  route('courses.store'),
  'courses.update',
  [],
  'courses.destroy',
);

const newCoversRelationship = {
  id: null,
  coverage_level: '',
  topic: {
    id: '',
  },
};

const { subformItems, shouldAllowAdd, add, onCancelAdd, onDestroyed } =
  useFormHelpers([], newCoversRelationship);

watch(viewing, (shouldView) => {
  if (!shouldView) {
    subformItems.value = [];
    return;
  }

  axios.get(route('courses.get_topics', id.value)).then((response) => {
    subformItems.value = response.data;
  });
});
</script>
