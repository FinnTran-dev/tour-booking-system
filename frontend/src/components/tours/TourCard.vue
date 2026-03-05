<template>
  <div class="card tour-card">
    <div class="tour-header">
      <h3 class="tour-title">{{ tour.name }}</h3>
      <span class="badge" :class="`badge-${tour.status.toLowerCase()}`">{{ tour.status }}</span>
    </div>
    
    <p class="tour-desc">
      {{ truncateDesc(tour.description) }}
    </p>

    <div class="tour-meta">
      <strong>Available Dates:</strong>
      <div v-if="tour.tour_dates && tour.tour_dates.length > 0" class="date-tags">
        <span class="date-tag" v-for="date in tour.tour_dates" :key="date.id">
          {{ date.date }}
        </span>
      </div>
      <p class="text-muted" v-else>No dates available.</p>
    </div>

    <div class="tour-actions">
      <!-- Edit Tour Link -->
      <router-link :to="`/tours/${tour.id}/edit`" class="btn btn-outline btn-sm">Edit Tour</router-link>
      <!-- Book Tour Link: Pass context through query string -->
      <router-link :to="{ path: '/bookings/new', query: { tour_id: tour.id }}" class="btn btn-primary btn-sm" :class="{ 'disabled': !tour.tour_dates || tour.tour_dates.length === 0 }">Book Now</router-link>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TourCard',
  props: {
    tour: {
      type: Object,
      required: true,
    },
  },
  methods: {
    truncateDesc(text, length = 100) {
      if (!text) return 'No description provided.';
      return text.length > length ? text.substring(0, length) + '...' : text;
    },
  },
};
</script>

<style scoped>
.tour-card {
  display: flex;
  flex-direction: column;
  height: 100%;
}

.tour-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: var(--space-4);
  gap: var(--space-3);
}

.tour-title {
  font-size: var(--font-size-lg);
  font-weight: 600;
  line-height: 1.3;
}

.tour-desc {
  color: var(--color-text-secondary);
  font-size: var(--font-size-sm);
  margin-bottom: var(--space-5);
  flex-grow: 1;
}

.tour-meta {
  margin-bottom: var(--space-5);
  font-size: var(--font-size-sm);
  color: var(--color-text-secondary);
}

.date-tags {
  display: flex;
  flex-wrap: wrap;
  gap: var(--space-2);
  margin-top: var(--space-2);
}

.date-tag {
  background: var(--color-bg-tertiary);
  padding: 2px var(--space-2);
  border-radius: var(--radius-sm);
  font-size: var(--font-size-xs);
  color: var(--color-text);
}

.tour-actions {
  display: flex;
  gap: var(--space-3);
  margin-top: auto;
}

.disabled {
  pointer-events: none;
  opacity: 0.5;
}
</style>
