<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { NVL } from '@neo4j-nvl/base';
import { onMounted } from 'vue';
import { ClickInteraction, DragNodeInteraction } from '@neo4j-nvl/interaction-handlers';

const props = defineProps<{
  courses: Array<object>;
  topics: Array<object>;
  coursesWithTopics: Array<object>;
}>();

onMounted(() => {
  let nodes = [];

for (const course of props.courses) {
  nodes.push({
    id: course.id,
    captions:String(course.number),
      // captions: {
      //   // value: String(course.number),
      //   key: String(course.number),
      //   // styles: 'bold,italic',
      //   // key: Math.random().toString(36).substring(3, 9),
      // },
      // captionSize: 100,
      // html: document.getElementById("graph-label"),
      // captionAlign: 'center',
      // caption: 'DFSD111',
      // label: 'DFSD',
  });
}
for (const topic of props.topics) {
  nodes.push({
    id: topic.id,
    label: topic.name,
    shape: 'circle'
  });
}

  let relationships = [];

for (const courseData of props.coursesWithTopics) {
  relationships.push({
    id: Math.random().toString(36).substring(3, 9),
    from: courseData.course.id,
    to: courseData.topic.id,
  });
}

  const nvl = new NVL(
    document.getElementById('graph-container'),
    nodes,
    relationships,
  );

  const clickInteraction = new ClickInteraction(nvl)

  const dragNodeInteraction = new DragNodeInteraction(nvl)

});
</script>

<template>
  <Head title="Data Entry | Topics" />

  <div
    class="base-card xmt-6 w-full px-4 py-4 text-sm md:w-11/12 md:text-base xl:px-10"
  >
    <p class="form-title mt-2 text-center">Topics</p>
    <div id="graph-container" style="height: 30rem"></div>
  </div>
  <span id="graph-label">GRAPH</span>
</template>
