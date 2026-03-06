<template>
  <div class="bookings-view">
    <div class="page-header">
      <div>
        <h1 class="page-title">Bookings</h1>
        <p class="text-muted">All confirmed and pending reservations.</p>
      </div>
      <router-link to="/bookings/new" class="btn btn-success">
        + New Booking
      </router-link>
    </div>

    <AlertMessage :message="error" type="error" />
    <LoadingSpinner :loading="loading" />

    <div v-if="!loading">
      <div class="table-container" v-if="bookings.length > 0">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Customer</th>
              <th>Tour</th>
              <th>Date</th>
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
              <td>{{ booking.tour ? booking.tour.name : '—' }}</td>
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
                <router-link
                  :to="`/bookings/${booking.id}/edit`"
                  class="btn btn-outline btn-sm"
                >
                  View
                </router-link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-else class="empty-state card">
        <div style="font-size: 3rem;">📭</div>
        <p>No bookings yet.</p>
        <router-link to="/bookings/new" class="btn btn-primary">Create First Booking</router-link>
      </div>

      <!-- Pagination -->
      <div v-if="pagination && pagination.last_page > 1" class="pagination">
        <button
          class="pagination-btn"
          :disabled="pagination.current_page === 1"
          @click="goToPage(pagination.current_page - 1)"
        >Prev</button>
        <span class="pagination-info">
          Page {{ pagination.current_page }} of {{ pagination.last_page }}
        </span>
        <button
          class="pagination-btn"
          :disabled="pagination.current_page === pagination.last_page"
          @click="goToPage(pagination.current_page + 1)"
        >Next</button>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState, mapActions } from 'vuex';
import AlertMessage from '@/components/common/AlertMessage.vue';
import LoadingSpinner from '@/components/common/LoadingSpinner.vue';

export default {
  name: 'BookingsListView',
  components: { AlertMessage, LoadingSpinner },
  computed: {
    ...mapState('bookings', ['bookings', 'pagination', 'loading', 'error']),
  },
  methods: {
    ...mapActions('bookings', ['fetchBookings']),
    formatDate(dateStr) {
      if (!dateStr) return '—';
      return new Date(dateStr).toLocaleDateString('vi-VN');
    },
    goToPage(page) {
      this.fetchBookings({ page });
    }
  },
  created() {
    this.fetchBookings();
  }
};
</script>

<style scoped>
.text-muted {
  color: var(--color-text-secondary);
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

.pagination-info {
  color: var(--color-text-secondary);
  font-size: var(--font-size-sm);
  padding: 0 var(--space-4);
}
</style>
