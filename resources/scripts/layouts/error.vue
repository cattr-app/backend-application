<template>
  <div>Error</div>
  <slot/>
</template>

<script lang="ts">
import {defineComponent} from 'vue';

export default defineComponent({
  name: 'error-layout',
});
</script>
