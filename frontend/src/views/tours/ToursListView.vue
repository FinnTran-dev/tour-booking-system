<template>
  <div class="tours-view">
    <div class="page-header">
      <div>
        <h1 class="page-title">Tours Dashboard</h1>
        <p class="text-muted">Browse and manage available tours in the system. Use filters below.</p>
      </div>
      <div>
        <router-link to="/tours/create" class="btn btn-success flex-align-center">
          <span>+ Create New Tour</span>
        </router-link>
      </div>
    </div>

    <!-- Toolbar: Search -->
    <div class="toolbar card">
      <div class="form-group" style="margin-bottom: 0;">
        <input 
          type="text" 
          class="form-control" 
          placeholder="Search tours by name..." 
          v-model="searchQuery"
          @input="onSearchInput"
        />
      </div>
      <div class="form-group" style="margin-bottom: 0; min-width: 150px;">
        <select class="form-control" v-model="statusFilter" @change="loadPage(1)">
          <option value="">All Statuses</option>
          <option value="Public">Public</option>
          <option value="Draft">Draft</option>
        </select>
      </div>
    </div>

    <AlertMessage :message="error" type="error" />
    <LoadingSpinner :loading="isLoading" />

    <!-- Grid View -->
    <div v-if="!isLoading && tours.length > 0" class="tour-grid">
      <TourCard v-for="tour in tours" :key="tour.id" :tour="tour" />
    </div>

    <div v-else-if="!isLoading && tours.length === 0" class="empty-state card">
      <p>No tours match your criteria.</p>
      <button v-if="searchQuery" class="btn btn-outline" @click="searchQuery = ''; fetchTours()">Clear Search</button>
    </div>

    <!-- Pagination -->
    <div v-if="!isLoading && pagination && pagination.last_page > 1" class="pagination">
      <button 
        class="pagination-btn" 
        :disabled="pagination.current_page === 1"
        @click="goToPage(pagination.current_page - 1)">
        Prev
      </button>
      <span class="pagination-info">Page {{ pagination.current_page }} of {{ pagination.last_page }}</span>
      <button 
        class="pagination-btn" 
        :disabled="pagination.current_page === pagination.last_page"
        @click="goToPage(pagination.current_page + 1)">
        Next
      </button>
    </div>
  </div>
</template>

<script>
import { mapState, mapActions } from 'vuex';
import TourCard from '@/components/tours/TourCard.vue';
import AlertMessage from '@/components/common/AlertMessage.vue';
import LoadingSpinner from '@/components/common/LoadingSpinner.vue';

export default {
  name: 'ToursListView',
  components: {
    TourCard,
    AlertMessage,
    LoadingSpinner,
  },
  data() {
    return {
      searchQuery: '',
      statusFilter: '',
      searchTimeout: null,
    };
  },
  computed: {
    ...mapState('tours', ['tours', 'pagination', 'loading', 'error']),
    isLoading() {
      return this.loading;
    }
  },
  methods: {
    ...mapActions('tours', ['fetchTours']),
    
    // Debounce search input logic
    onSearchInput() {
      clearTimeout(this.searchTimeout);
      this.searchTimeout = setTimeout(() => {
        this.loadPage(1);
      }, 500); // 500ms debounce
    },

    loadPage(page = 1) {
      let params = { page: page };
      if (this.searchQuery) params.search = this.searchQuery;
      if (this.statusFilter) params.status = this.statusFilter;

      this.fetchTours(params).catch(() => {});
    },

    goToPage(page) {
      if (page >= 1 && page <= this.pagination.last_page) {
        this.loadPage(page);
      }
    }
  },
  created() {
    this.loadPage(1);
  },
};
</script>

<style scoped>
.toolbar {
  margin-bottom: var(--space-6);
  padding: var(--space-4) var(--space-6);
  display: flex;
  gap: var(--space-4);
}

.tour-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: var(--space-6);
}

.empty-state {
  text-align: center;
  padding: var(--space-12) var(--space-6);
  color: var(--color-text-secondary);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: var(--space-4);
}

.pagination-info {
  color: var(--color-text-secondary);
  font-size: var(--font-size-sm);
  padding: 0 var(--space-4);
}
</style>
