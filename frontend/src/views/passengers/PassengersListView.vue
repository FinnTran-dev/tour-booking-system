<template>
  <div class="passengers-view">
    <div class="page-header">
      <div>
        <h1 class="page-title">Passengers</h1>
        <p class="text-muted">All registered passengers across bookings.</p>
      </div>
    </div>

    <!-- Filters -->
    <div class="toolbar card">
      <div class="form-group" style="margin-bottom: 0; flex: 1;">
        <input
          type="text"
          class="form-control"
          placeholder="Search by name or email..."
          v-model="searchQuery"
          @input="onSearchInput"
        />
      </div>
      <div class="form-group" style="margin-bottom: 0; min-width: 160px;">
        <select class="form-control" v-model="statusFilter" @change="loadPage(1)">
          <option value="">All Statuses</option>
          <option value="Enabled">Enabled</option>
          <option value="Disabled">Disabled</option>
        </select>
      </div>
    </div>

    <LoadingSpinner :loading="loading" />

    <div v-if="!loading">
      <div class="table-container" v-if="passengers.length > 0">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Date of Birth</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="p in passengers" :key="p.id">
              <td><strong>#{{ p.id }}</strong></td>
              <td>{{ p.given_name }} {{ p.surname }}</td>
              <td>{{ p.email || '—' }}</td>
              <td>{{ p.phone || '—' }}</td>
              <td>{{ p.date_of_birth || '—' }}</td>
              <td>
                <span :class="`badge badge-${(p.status || 'enabled').toLowerCase()}`">
                  {{ p.status || 'Enabled' }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-else class="empty-state card">
        <div class="empty-icon"><i class="fa-solid fa-users-slash"></i></div>
        <p>No passengers found.</p>
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
import passengerService from '@/services/passengerService';

export default {
  name: 'PassengersListView',
  components: { LoadingSpinner },
  data() {
    return {
      passengers: [],
      pagination: null,
      loading: false,
      searchQuery: '',
      statusFilter: '',
      searchTimeout: null,
    };
  },
  methods: {
    onSearchInput() {
      clearTimeout(this.searchTimeout);
      this.searchTimeout = setTimeout(() => this.loadPage(1), 500);
    },
    async loadPage(page = 1) {
      this.loading = true;
      try {
        const params = { page };
        if (this.searchQuery) params.search = this.searchQuery;
        if (this.statusFilter) params.status = this.statusFilter;
        const res = await passengerService.list(params);
        this.passengers = res.data.data;
        this.pagination = res.data.meta;
      } catch (e) {
        console.error(e);
      } finally {
        this.loading = false;
      }
    },
  },
  created() {
    this.loadPage(1);
  }
};
</script>

<style scoped>
.toolbar {
  margin-bottom: var(--space-6);
  padding: var(--space-4) var(--space-6);
  display: flex;
  gap: var(--space-4);
  align-items: center;
}

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
