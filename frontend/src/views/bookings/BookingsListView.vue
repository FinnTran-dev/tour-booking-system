<template>
  <div class="bookings-view">
    <div class="page-header">
      <div>
        <h1 class="page-title">Bookings</h1>
      </div>
      <router-link to="/bookings/new" class="btn btn-success">
        + New Booking
      </router-link>
    </div>

    <!-- Filters -->
    <div class="toolbar card">
      <div class="form-group" style="margin-bottom: 0; flex: 1;">
        <input
          type="text"
          class="form-control"
          placeholder="Search by customer name or email..."
          v-model="searchQuery"
          @input="onSearchInput"
        />
      </div>
      <div class="form-group" style="margin-bottom: 0; min-width: 160px;">
        <select class="form-control" v-model="statusFilter" @change="loadPage(1)">
          <option value="">All Statuses</option>
          <option value="Submitted">Submitted</option>
          <option value="Confirmed">Confirmed</option>
          <option value="Cancelled">Cancelled</option>
        </select>
      </div>
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
              <td>
                <div v-if="booking.tour_date">
                  {{ booking.tour_date.date }}
                  <span v-if="booking.tour_date.end_date && booking.tour_date.end_date !== booking.tour_date.date" class="text-muted" style="font-size: 0.8rem;">
                    – {{ booking.tour_date.end_date }}
                  </span>
                </div>
                <div v-else>—</div>
              </td>
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
        <div class="empty-icon"><i class="fa-regular fa-calendar-xmark"></i></div>
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
  data() {
    return {
      searchQuery: '',
      statusFilter: '',
      searchTimeout: null,
    };
  },
  computed: {
    ...mapState('bookings', ['bookings', 'pagination', 'loading', 'error']),
  },
  methods: {
    ...mapActions('bookings', ['fetchBookings']),
    formatDate(dateStr) {
      if (!dateStr) return '—';
      return new Date(dateStr).toLocaleDateString('vi-VN');
    },
    onSearchInput() {
      clearTimeout(this.searchTimeout);
      this.searchTimeout = setTimeout(() => this.loadPage(1), 500);
    },
    loadPage(page = 1) {
      const params = { page };
      if (this.searchQuery) params.search = this.searchQuery;
      if (this.statusFilter) params.status = this.statusFilter;
      this.fetchBookings(params);
    },
    goToPage(page) {
      this.loadPage(page);
    }
  },
  created() {
    this.loadPage(1);
  }
};
</script>

<style scoped>
.text-muted {
  color: var(--color-text-secondary);
}

.toolbar {
  display: flex;
  gap: var(--space-4);
  margin-bottom: var(--space-6);
  padding: var(--space-4) var(--space-6);
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
