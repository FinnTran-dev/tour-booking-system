<template>
  <div class="tour-form-view">
    <div class="page-header">
      <h1 class="page-title">{{ isEdit ? 'Edit Tour' : 'Create New Tour' }}</h1>
    </div>

    <!-- Error/Loading states -->
    <div v-if="isStale" class="alert alert-warning" style="display: flex; justify-content: space-between; align-items: center; border: 1px solid var(--color-warning);">
      <div style="display: flex; align-items: center; gap: var(--space-3);">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span>{{ error }}</span>
      </div>
      <button @click="reloadPage" class="btn btn-sm btn-primary" style="background: var(--color-warning); border: none;">
        <i class="fa-solid fa-rotate"></i> Reload Data
      </button>
    </div>
    <AlertMessage v-else :message="error" type="error" />
    <LoadingSpinner :loading="isLoading" />

    <div class="card" v-if="!isLoading">
      <form @submit.prevent="submitForm">
        <div class="form-group">
          <label class="form-label" for="name">Tour Name *</label>
          <input 
            type="text" 
            id="name" 
            class="form-control" 
            v-model="form.name" 
            placeholder="e.g. Grand Canyon Adventure" 
            required 
          />
          <div class="form-error" v-if="validationErrors.name">{{ validationErrors.name[0] }}</div>
        </div>

        <div class="form-group" v-if="isEdit">
          <label class="form-label" for="status">Status</label>
          <select id="status" class="form-control" v-model="form.status" required>
            <option value="Draft">Draft</option>
            <option value="Public">Public</option>
          </select>
          <div class="form-error" v-if="validationErrors.status">{{ validationErrors.status[0] }}</div>
        </div>

        <div class="form-group">
          <label class="form-label" for="description">Description</label>
          <textarea 
            id="description" 
            class="form-control" 
            v-model="form.description" 
            rows="4" 
            placeholder="Detailed description of the tour..."
          ></textarea>
        </div>

        <!-- Tour Dates Management -->
        <div class="form-group">
          <label class="form-label">Available Dates</label>
          <p class="text-muted" style="margin-bottom: var(--space-2); font-size: var(--font-size-sm);">
            Add dates when this tour is available. You can add more later.
          </p>
          
          <div class="dates-list">
            <div v-for="(dateObj, index) in form.dates" :key="index" class="date-row">
              <div class="date-inputs">
                <div class="input-group">
                  <label :for="'start-date-' + index">Start Date</label>
                  <input 
                    :id="'start-date-' + index"
                    type="date" 
                    class="form-control" 
                    v-model="dateObj.date" 
                    :min="todayDate"
                    required 
                  />
                </div>
                <div class="input-group">
                  <label :for="'end-date-' + index">End Date</label>
                  <input 
                    :id="'end-date-' + index"
                    type="date" 
                    class="form-control" 
                    v-model="dateObj.end_date" 
                    :min="dateObj.date || todayDate"
                    required 
                  />
                </div>
                <div class="input-group" style="max-width: 100px;">
                  <label :for="'capacity-' + index">Capacity</label>
                  <input 
                    :id="'capacity-' + index"
                    type="number" 
                    class="form-control" 
                    v-model.number="dateObj.capacity" 
                    min="1"
                    required 
                  />
                  <div v-if="dateObj.isOld" style="font-size: 10px; color: var(--color-text-muted); margin-top: 2px;">
                    Booked: {{ dateObj.booked_count || 0 }}
                  </div>
                </div>
              </div>
              <button 
                type="button" 
                class="btn btn-outline text-danger" 
                @click="removeDate(index)"
                v-if="!dateObj.isOld || dateObj.date >= todayDate"
              >
                Remove
              </button>
            </div>
          </div>
          
          <button type="button" class="btn btn-outline btn-sm" @click="addDate">
            + Add Date
          </button>
          <div class="form-error" v-if="validationErrors.dates">{{ validationErrors.dates[0] }}</div>
        </div>

        <div class="form-actions" style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: flex-end;">
          <router-link to="/tours" class="btn btn-outline">Cancel</router-link>
          <button type="submit" class="btn btn-primary" :disabled="isLoading">
            {{ isEdit ? 'Update Tour' : 'Create Tour & Save Draft' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { mapState, mapActions } from 'vuex';
import AlertMessage from '@/components/common/AlertMessage.vue';
import LoadingSpinner from '@/components/common/LoadingSpinner.vue';
import tourService from '@/services/tourService';

export default {
  name: 'TourFormView',
  components: {
    AlertMessage,
    LoadingSpinner,
  },
  data() {
    return {
      validationErrors: {},
      isStale: false,
      form: {
        name: '',
        description: '',
        status: 'Draft',
        dates: [],
        last_updated_at: null,
      },
    };
  },
  computed: {
    ...mapState('tours', ['loading', 'error']),
    isEdit() {
      return !!this.$route.params.id;
    },
    isLoading: {
      get() { return this.loading; },
      set(val) {}
    },
    todayDate() {
      const d = new Date();
      return d.toISOString().split('T')[0];
    }
  },
  methods: {
    ...mapActions('tours', ['createTour', 'updateTour', 'fetchTour', 'clearCurrentTour']),
    
    addDate() {
      this.form.dates.push({ date: '', end_date: '', capacity: 10, isOld: false });
    },
    removeDate(index) {
      this.form.dates.splice(index, 1);
    },
    async submitForm() {
      this.validationErrors = {};
      try {
        // Prepare payload
        const validDates = this.form.dates
          .filter(d => !!d.date && !!d.end_date)
          .map(d => ({ 
             id: d.id, 
             date: d.date, 
             end_date: d.end_date,
             capacity: d.capacity 
          }));
        
        const payload = { ...this.form, dates: validDates };

        if (this.isEdit) {
          await this.updateTour({ id: this.$route.params.id, data: payload });
          this.$router.push('/tours');
        } else {
          await this.createTour(payload);
          this.$router.push('/tours');
        }
      } catch (err) {
        if (err.status === 409 || (err.apiMessage && err.apiMessage.includes('modified by another user'))) {
          this.isStale = true;
          this.$store.commit('tours/SET_ERROR', err.apiMessage);
        } else if (err.apiErrors) {
          this.validationErrors = err.apiErrors;
        }
      }
    },
    async loadExistingTour() {
      try {
        const res = await tourService.get(this.$route.params.id);
        const tour = res.data.data;
        this.form.name = tour.name;
        this.form.description = tour.description;
        this.form.status = tour.status;
        this.form.last_updated_at = tour.updated_at;
        // Map existing dates strings for the form
        if (tour.tour_dates) {
          this.form.dates = tour.tour_dates.map(d => ({ 
            id: d.id,
            date: d.date, 
            end_date: d.end_date || d.date, 
            capacity: d.capacity,
            booked_count: d.booked_count,
            isOld: true 
          }));
        }
      } catch (e) {
        // Handled by interceptor theoretically
        console.error(e);
      }
    },
    reloadPage() {
      window.location.reload();
    }
  },
  async mounted() {
    this.clearCurrentTour();
    if (this.isEdit) {
      await this.loadExistingTour();
    } else {
      // Start with one empty date field
      this.addDate();
    }
  },
};
</script>

<style scoped>
.dates-list {
  display: flex;
  flex-direction: column;
  gap: var(--space-3);
  margin-bottom: var(--space-3);
}

.date-row {
  display: flex;
  gap: var(--space-4);
  align-items: flex-end;
  background: var(--color-bg-secondary);
  padding: var(--space-4);
  border-radius: var(--radius-md);
  border: 1px solid var(--color-border);
}

.date-inputs {
  display: flex;
  gap: var(--space-4);
  flex: 1;
}

.input-group {
  display: flex;
  flex-direction: column;
  gap: var(--space-1);
  flex: 1;
}

.input-group label {
  font-size: var(--font-size-xs);
  font-weight: 600;
  color: var(--color-text-secondary);
}

.date-row .form-control {
  width: 100%;
}
</style>
