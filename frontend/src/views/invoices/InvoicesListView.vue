<template>
  <div class="invoices-view">
    <div class="page-header">
      <div>
        <h1 class="page-title">Invoices</h1>
        <p class="text-muted">Manage payment status for all bookings.</p>
      </div>
    </div>

    <!-- Filters -->
    <div class="toolbar card">
      <div class="form-group" style="margin-bottom: 0; min-width: 160px;">
        <select class="form-control" v-model="statusFilter" @change="loadPage(1)">
          <option value="">All Statuses</option>
          <option value="Unpaid">Unpaid</option>
          <option value="Paid">Paid</option>
          <option value="Cancelled">Cancelled</option>
        </select>
      </div>
      <div style="margin-left: auto; color: var(--color-text-secondary); font-size: var(--font-size-sm); display: flex; align-items: center;">
        Total: {{ pagination ? pagination.total : 0 }} invoices
      </div>
    </div>

    <LoadingSpinner :loading="loading" />

    <div v-if="!loading">
      <div class="table-container" v-if="invoices.length > 0">
        <table>
          <thead>
            <tr>
              <th>Invoice #</th>
              <th>Booking</th>
              <th>Tour</th>
              <th>Date</th>
              <th>Amount</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="inv in invoices" :key="inv.id">
              <td><strong>#{{ inv.id }}</strong></td>
              <td>
                <div v-if="inv.booking">
                  <router-link :to="`/bookings/${inv.booking.id}/edit`" style="color: var(--color-primary);">
                    #{{ inv.booking.id }} — {{ inv.booking.customer_name }}
                  </router-link>
                </div>
                <span v-else class="text-muted">—</span>
              </td>
              <td>{{ inv.booking && inv.booking.tour ? inv.booking.tour.name : '—' }}</td>
              <td>{{ inv.booking && inv.booking.tour_date ? inv.booking.tour_date.date : '—' }}</td>
              <td><strong>${{ inv.amount }}</strong></td>
              <td>
                <span :class="`badge badge-${inv.status.toLowerCase()}`">
                  {{ inv.status }}
                </span>
              </td>
              <td>
                <div style="display: flex; gap: 0.5rem;">
                  <button
                    v-if="inv.status === 'Unpaid'"
                    class="btn btn-success btn-sm"
                    :disabled="inv.updating"
                    @click="markAs(inv, 'Paid')"
                  >
                    <i class="fa-solid fa-check"></i> Mark Paid
                  </button>
                  <button
                    v-if="inv.status !== 'Cancelled'"
                    class="btn btn-outline btn-sm"
                    :disabled="inv.updating"
                    @click="markAs(inv, 'Cancelled')"
                  >
                    Cancel
                  </button>
                  <button
                    v-if="inv.status === 'Cancelled'"
                    class="btn btn-outline btn-sm"
                    :disabled="inv.updating"
                    @click="markAs(inv, 'Unpaid')"
                  >
                    Reopen
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-else class="empty-state card">
        <div class="empty-icon"><i class="fa-solid fa-file-invoice-dollar"></i></div>
        <p>No invoices found.</p>
      </div>

      <!-- Pagination -->
      <div v-if="pagination && pagination.last_page > 1" class="pagination">
        <button
          class="pagination-btn"
          :disabled="pagination.current_page === 1"
          @click="loadPage(pagination.current_page - 1)"
        >Prev</button>
        <span class="pagination-info">
          Page {{ pagination.current_page }} of {{ pagination.last_page }}
        </span>
        <button
          class="pagination-btn"
          :disabled="pagination.current_page === pagination.last_page"
          @click="loadPage(pagination.current_page + 1)"
        >Next</button>
      </div>
    </div>
  </div>
</template>

<script>
import LoadingSpinner from '@/components/common/LoadingSpinner.vue';
import apiClient from '@/services/apiClient';

export default {
  name: 'InvoicesListView',
  components: { LoadingSpinner },
  data() {
    return {
      invoices: [],
      pagination: null,
      loading: false,
      statusFilter: '',
    };
  },
  methods: {
    async loadPage(page = 1) {
      this.loading = true;
      try {
        const params = { page };
        if (this.statusFilter) params.status = this.statusFilter;
        const res = await apiClient.get('/invoices', { params });
        this.invoices = res.data.data.map(inv => ({ ...inv, updating: false }));
        this.pagination = res.data.meta;
      } catch (e) {
        console.error(e);
      } finally {
        this.loading = false;
      }
    },
    async markAs(invoice, status) {
      invoice.updating = true;
      try {
        const res = await apiClient.patch(`/invoices/${invoice.id}`, { status });
        // Update in-place
        invoice.status = res.data.data.status;
      } catch (e) {
        alert('Failed to update invoice status.');
        console.error(e);
      } finally {
        invoice.updating = false;
      }
    },
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

.pagination-info {
  color: var(--color-text-secondary);
  font-size: var(--font-size-sm);
  padding: 0 var(--space-4);
}
</style>
