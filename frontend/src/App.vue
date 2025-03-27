<template>
  <NavMenu />
  <router-view :key="$route.fullPath"></router-view>

</template>
<script>
import NavMenu from '@/components/NavMenu.vue';

export default {
  name: 'App',
  components: {
    NavMenu,  
  },
};
</script>
<style>

</style>
