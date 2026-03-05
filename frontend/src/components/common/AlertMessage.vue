<template>
  <div class="alert" :class="`alert-${type}`" v-if="message">
    {{ message }}
  </div>
</template>

<script>
export default {
  name: 'AlertMessage',
  props: {
    message: {
      type: String,
      default: '',
    },
    type: {
      type: String,
      default: 'error', // 'error', 'success', 'info', 'warning'
      validator: (val) => ['error', 'success', 'info', 'warning'].includes(val),
    },
  },
};
</script>

<style scoped>
.alert {
  padding: var(--space-3) var(--space-4);
  border-radius: var(--radius-md);
  margin-bottom: var(--space-4);
  font-size: var(--font-size-sm);
  font-weight: 500;
  border-left: 4px solid transparent;
}

.alert-error {
  background-color: var(--color-danger-light);
  color: var(--color-danger);
  border-left-color: var(--color-danger);
}

.alert-success {
  background-color: var(--color-success-light);
  color: var(--color-success);
  border-left-color: var(--color-success);
}

.alert-info {
  background-color: var(--color-info-light);
  color: var(--color-info);
  border-left-color: var(--color-info);
}

.alert-warning {
  background-color: var(--color-warning-light);
  color: var(--color-warning);
  border-left-color: var(--color-warning);
}
</style>
