<template>
  <div class="tour-bookings-view">
    <div class="page-header">
      <div>
        <router-link to="/tours" class="back-link">← Back to Tours</router-link>
        <h1 class="page-title" v-if="tour">{{ tour.name }}</h1>
        <h1 class="page-title" v-else>Tour Bookings</h1>
        <p class="text-muted">All bookings for this tour.</p>
      </div>
      <router-link
        v-if="tour"
        :to="{ path: '/bookings/new', query: { tour_id: $route.params.id }}"
        class="btn btn-success"
      >
        + New Booking
      </router-link>
    </div>

    <!-- Filters -->
    <div class="toolbar card">
      <div class="form-group" style="margin-bottom: 0; min-width: 160px;">
        <select class="form-control" v-model="statusFilter" @change="loadBookings">
          <option value="">All Statuses</option>
          <option value="Submitted">Submitted</option>
          <option value="Confirmed">Confirmed</option>
          <option value="Cancelled">Cancelled</option>
        </select>
      </div>
      <div style="margin-left: auto; color: var(--color-text-secondary); font-size: var(--font-size-sm); display: flex; align-items: center;">
        {{ bookings.length }} booking(s) found
      </div>
    </div>

    <LoadingSpinner :loading="loading" />

    <div v-if="!loading">
      <div class="table-container" v-if="bookings.length > 0">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Customer</th>
              <th>Tour Date</th>
              <th>Passengers</th>
              <th>Invoice</th>
              <th>Status</th>
              <th>Created</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="booking in bookings" :key="booking.id">
              <td><strong>#{{ booking.id }}</strong></td>
              <td>
                <div>{{ booking.customer_name }}</div>
                <div class="text-muted" style="font-size: 0.8rem;">{{ booking.customer_email }}</div>
              </td>
              <td>{{ booking.tour_date ? booking.tour_date.date : '—' }}</td>
              <td>{{ booking.passengers ? booking.passengers.length : 0 }} pax</td>
              <td>
                <span v-if="booking.invoice">
                  ${{ booking.invoice.amount }}
                  <span :class="`badge badge-${booking.invoice.status.toLowerCase()}`" style="margin-left: 4px;">
                    {{ booking.invoice.status }}
                  </span>
                </span>
                <span v-else class="text-muted">—</span>
              </td>
              <td>
                <span :class="`badge badge-${booking.status.toLowerCase()}`">
                  {{ booking.status }}
                </span>
              </td>
              <td class="text-muted">{{ formatDate(booking.created_at) }}</td>
              <td>
                <router-link :to="`/bookings/${booking.id}/edit`" class="btn btn-outline btn-sm">
                  View / Edit
                </router-link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-else class="empty-state card">
        <div class="empty-icon"><i class="fa-regular fa-calendar-xmark"></i></div>
        <p>No bookings found for this tour.</p>
        <router-link
          v-if="tour"
          :to="{ path: '/bookings/new', query: { tour_id: $route.params.id }}"
          class="btn btn-primary"
        >
          Create First Booking
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import LoadingSpinner from '@/components/common/LoadingSpinner.vue';
import apiClient from '@/services/apiClient';

export default {
  name: 'TourBookingsView',
  components: { LoadingSpinner },
  data() {
    return {
      tour: null,
      bookings: [],
      loading: false,
      statusFilter: '',
    };
  },
  methods: {
    formatDate(dateStr) {
      if (!dateStr) return '—';
      return new Date(dateStr).toLocaleDateString('vi-VN');
    },
    async loadTour() {
      try {
        const res = await apiClient.get(`/tours/${this.$route.params.id}`);
        this.tour = res.data.data;
      } catch (e) {
        console.error('Failed to load tour:', e);
      }
    },
    async loadBookings() {
      this.loading = true;
      try {
        const params = {};
        if (this.statusFilter) params.status = this.statusFilter;
        const res = await apiClient.get(`/tours/${this.$route.params.id}/bookings`, { params });
        this.bookings = res.data.data;
      } catch (e) {
        console.error('Failed to load bookings:', e);
      } finally {
        this.loading = false;
      }
    },
  },
  async created() {
    await Promise.all([this.loadTour(), this.loadBookings()]);
  },
};
</script>

<style scoped>
.back-link {
  color: var(--color-text-secondary);
  font-size: var(--font-size-sm);
  text-decoration: none;
  display: inline-block;
  margin-bottom: var(--space-2);
}
.back-link:hover {
  color: var(--color-primary);
}

.text-muted {
  color: var(--color-text-secondary);
}

.toolbar {
  margin-bottom: var(--space-6);
  padding: var(--space-4) var(--space-6);
  display: flex;
  gap: var(--space-4);
  align-items: center;
}

.empty-state {
  text-align: center;
  padding: var(--space-12) var(--space-6);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: var(--space-4);
  color: var(--color-text-secondary);
}
</style>
