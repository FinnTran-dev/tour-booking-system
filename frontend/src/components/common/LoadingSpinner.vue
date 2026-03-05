<template>
  <div class="spinner-container" v-if="loading">
    <div class="spinner"></div>
  </div>
</template>

<script>
export default {
  name: 'LoadingSpinner',
  props: {
    loading: {
      type: Boolean,
      default: false,
    },
  },
};
</script>

<style scoped>
.spinner-container {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: var(--space-8);
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid var(--color-surface-hover);
  border-top-color: var(--color-primary);
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
</style>
