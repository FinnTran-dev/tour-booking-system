<template>
  <div class="tour-form-view">
    <div class="page-header">
      <h1 class="page-title">{{ isEdit ? 'Edit Tour' : 'Create New Tour' }}</h1>
    </div>

    <!-- Error/Loading states -->
    <AlertMessage :message="error" type="error" />
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
              <input 
                type="date" 
                class="form-control" 
                v-model="dateObj.value" 
                :min="dateObj.isOld ? undefined : todayDate"
                :disabled="dateObj.isOld && dateObj.value < todayDate"
                required 
              />
              <button 
                type="button" 
                class="btn btn-outline text-danger" 
                @click="removeDate(index)"
                v-if="!dateObj.isOld || dateObj.value >= todayDate"
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
      form: {
        name: '',
        description: '',
        status: 'Draft',
        dates: [],
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
      this.form.dates.push({ value: '', isOld: false });
    },
    removeDate(index) {
      this.form.dates.splice(index, 1);
    },
    async submitForm() {
      this.validationErrors = {};
      try {
        if (this.isEdit) {
          // Prepare payload (removing empty dates and map values)
          const validDates = this.form.dates.filter(d => !!d.value).map(d => d.value);
          const payload = { ...this.form, dates: validDates };
          await this.updateTour({ id: this.$route.params.id, data: payload });
          this.$router.push('/tours');
        } else {
          const validDates = this.form.dates.filter(d => !!d.value).map(d => d.value);
          const payload = { ...this.form, dates: validDates };
          await this.createTour(payload);
          this.$router.push('/tours');
        }
      } catch (err) {
        if (err.apiErrors) {
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
        // Map existing dates strings for the form
        if (tour.tour_dates) {
          this.form.dates = tour.tour_dates.map(d => ({ value: d.date, isOld: true }));
        }
      } catch (e) {
        // Handled by interceptor theoretically
        console.error(e);
      }
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
  gap: var(--space-3);
  align-items: center;
}

.date-row .form-control {
  max-width: 250px;
}
</style>
