<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { onMounted, ref, watch } from 'vue';
import { DataSet, Network } from 'vis-network/standalone';
import map from 'lodash/map';
import PillDiv from '@/Components/PillDiv.vue';

const props = defineProps<{
  courses: Array<object>;
  topics: Array<object>;
  coursesWithTopics: Array<object>;
  courseTopicEdgeWeights: Array<string>;
}>();

const allCourses: Array<Number> = props.courses.map((course) => course.number);
const selectedCourses = ref(allCourses);
const selectAll = ref(true);

watch(selectedCourses, (newSelectedCourses) => {
  if (newSelectedCourses.length === 0) selectAll.value = false;
});

watch(selectAll, (newVal) => {
  if (newVal) {
    selectedCourses.value.push(...props.courses.map((course) => course.number));

    props.courses.forEach((course) => {
      toggleCourseVisibility(course.id, true);
    });

    return;
  }

  selectedCourses.value = [];

  props.courses.forEach((course) => {
    toggleCourseVisibility(course.id, false);
  });
});

let nodes = [];

nodes.push(
  ...props.courses.map((course) => ({
    id: course.id,
    label: `<b>${course.number}</b>`,
    color: '#fca5a5',
    mass: 4,
    font: { multi: true, bold: 20, size: 20 },
    widthConstraint: { minimum: 70 },
    heightConstraint: { minimum: 70 },
  })),
);

nodes.push(
  ...props.topics.map((topic) => ({
    id: topic.id,
    label: `${topic.name.substring(0, 20)}${topic.name.length > 20 ? '...' : ''}`,
    title: topic.name,
    font: { size: 16 },
    widthConstraint: { minimum: 60, maximum: 70 },
    heightConstraint: { minimum: 60, maximum: 70 },
  })),
);

nodes = new DataSet(nodes);

let edges = [];

edges.push(
  ...props.coursesWithTopics.map((courseData) => ({
    from: courseData.course.id,
    to: courseData.topic.id,
    label: `${courseData.covers.coverage_level}`,
    value:
      props.courseTopicEdgeWeights.indexOf(courseData.covers.coverage_level) +
      1,
  })),
);

edges = new DataSet(edges);

function toggleCourseVisibility(courseId: string, setVisibilityTo = null) {
  let isCourseVisible = nodes.get(courseId).hidden;

  if (typeof setVisibilityTo === 'boolean') {
    isCourseVisible = setVisibilityTo;
  } else {
    if (typeof isCourseVisible === 'undefined' || isCourseVisible === false) {
      isCourseVisible = false;
    }
  }

  let nodeUpdates = [{ id: courseId, hidden: !isCourseVisible }];

  nodeUpdates.push(
    ...props.coursesWithTopics
      .filter((item) => {
        if (
          selectedCourses.value.length === 0 ||
          selectedCourses.value.length === props.courses.length
        ) {
          return item.course.id === courseId;
        }

        return (
          item.course.id === courseId &&
          !isTopicAttachedToMultipleVisibleCourses(item.topic.id)
        );
      })
      .map((item) => ({ id: item.topic.id, hidden: !isCourseVisible })),
  );

  nodes.update(nodeUpdates);
}

function isTopicAttachedToMultipleVisibleCourses(topicId): boolean {
  let courseIdsTopicIsAttachedTo: string[] = props.coursesWithTopics
    .filter((item) => item.topic.id === topicId)
    .map((item) => item.course.id);

  if (courseIdsTopicIsAttachedTo.length < 2) return false;

  return (
    nodes.get({
      filter: function (node) {
        return (
          courseIdsTopicIsAttachedTo.includes(node.id) &&
          (node.hidden === false || typeof node.hidden === 'undefined')
        );
      },
    }).length > 1
  );
}

const data = {
  nodes: nodes,
  edges: edges,
};
const options = {
  edges: {
    arrows: 'to',
    color: 'gray',
    length: 300,
    arrowStrikethrough: false,
    smooth: false,
    scaling: {
      min: 1,
      max: 6,
    },
    font: { align: 'top', vadjust: -1 },
  },
  nodes: {
    shape: 'circle',
  },
};

onMounted(() => {
  const container = document.getElementById('graph-container');

  const network = new Network(container, data, options);
});
</script>

<template>
  <Head title="Data Entry | Topics" />

  <div
    class="base-card w-full space-y-2 px-4 py-4 text-sm md:w-11/12 md:text-base xl:px-10"
  >
    <p class="form-title text-center">Topics</p>
    <div class="flex flex-wrap gap-x-5 gap-y-3 rounded-lg border p-3">
      <PillDiv
        ><input
          v-model="selectAll"
          class="input-checkbox mr-2"
          type="checkbox"
          :value="true"
        />
        select all
      </PillDiv>
      <PillDiv v-for="course in courses">
        <input
          v-model="selectedCourses"
          @change="toggleCourseVisibility(course.id)"
          class="input-checkbox mr-2"
          type="checkbox"
          :value="course.number"
        />
        {{ course.number }}
      </PillDiv>
    </div>
    <div
      id="graph-container"
      class="xbg-gray-50 h-[40rem] rounded-lg border"
    ></div>
  </div>
</template>
