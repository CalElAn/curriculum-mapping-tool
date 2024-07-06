<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import NeoVis from 'neovis.js/dist/neovis.js';

const props = defineProps<{
  courses: Array<object>;
  coursesWithTopics: Array<object>;
}>();

onMounted(() => {
  let neoViz;

  function draw() {
    const config = {
      containerId: 'graph-container',
      neo4j: {
        serverUrl: import.meta.env.VITE_AURA_DB_URL,
        serverUser: import.meta.env.VITE_AURA_DB_USERNAME,
        serverPassword: import.meta.env.VITE_AURA_DB_PASSWORD,
        driverConfig: {
          encrypted: 'ENCRYPTION_ON',
          trust: 'TRUST_SYSTEM_CA_SIGNED_CERTIFICATES',
        },
      },
      // nodes: {
      //   shape: 'circle',
      //   size: 50
      // },
      labels: {
        Course: {
          label: 'number',
          shape: 'box',
          // value: 'pagerank',
          // group: 'community',
          // [NeoVis.NEOVIS_ADVANCED_CONFIG]: {
          //   function: {
          //     title: (node) => NeoVis.nodeToHtml(node, ['number']),
          //   },
          // },
        },
        Topic: {
          label: 'name',
          shape: 'box',
        },
      },
      // relationships: {
      //     value: 'coverage_level',
      //   COVERS: {
      //     arrows: 'to;from',
      //   },
      // },
      // edges: {
      //   arrows: 'to, from',
      //   color: 'red',
      // },
      initialCypher: `MATCH (c:Course)-[r_c:COVERS]->(t:Topic) RETURN c, r_c, t
      `,
    };

    neoViz = new NeoVis(config);
    neoViz.render();
  }

  draw();
});
</script>

<template>
  <Head title="Data Entry | Topics" />

  <div
    class="base-card xmt-6 w-full px-4 py-4 text-sm md:w-11/12 md:text-base xl:px-10"
  >
    <p class="form-title mt-2 text-center">Topics</p>
    <div id="graph-container" class="h-[30rem] border border-gray-200"></div>
  </div>
</template>
