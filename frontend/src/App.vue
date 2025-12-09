<template>
  <router-view />

  <Chatbot v-if="$route.meta.showChatbot" />
</template>

<script>
import Chatbot from './components/Web/Chatbot.vue'

export default {
  components: { Chatbot }
}
</script>

<style scoped>
.footer {
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100%;
}
</style>