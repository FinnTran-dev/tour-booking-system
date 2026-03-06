<template>
  <div class="booking-form-view">
    <div class="page-header">
      <h1 class="page-title">{{ isEdit ? 'Edit Booking' : 'New Booking' }}</h1>
    </div>

    <AlertMessage :message="error" type="error" />
    <LoadingSpinner :loading="isLoading" />

    <div class="card" v-if="!isLoading">
      <form @submit.prevent="submitForm">
        <!-- Tour Selection -->
        <div class="form-group">
          <label class="form-label">Select Tour *</label>
          <select class="form-control" v-model="form.tour_id" @change="fetchTourDetails" required :disabled="isEdit">
             <option value="" disabled>-- Choose a Tour --</option>
             <option v-for="t in toursList" :key="t.id" :value="t.id">
               {{ t.name }}
             </option>
          </select>
          <div class="form-error" v-if="validationErrors.tour_id">{{ validationErrors.tour_id[0] }}</div>
        </div>

        <!-- Date Selection -->
        <div class="form-group" v-if="form.tour_id">
          <label class="form-label">Select Date *</label>
          <select class="form-control" v-model="form.tour_date_id" required>
             <option value="" disabled>-- Choose a Date --</option>
             <option v-for="d in availableDates" :key="d.id" :value="d.id">
               {{ d.date }}
             </option>
          </select>
          <div class="form-error" v-if="validationErrors.tour_date_id">{{ validationErrors.tour_date_id[0] }}</div>
        </div>

        <!-- Booking Status (Edit only) -->
        <div class="form-group" v-if="isEdit">
          <label class="form-label">Booking Status</label>
          <select class="form-control" v-model="form.status">
            <option value="Submitted">Submitted</option>
            <option value="Confirmed">Confirmed</option>
            <option value="Cancelled">Cancelled</option>
          </select>
        </div>

        <!-- Customer Lead Info -->
        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <div class="form-group" style="flex: 1;">
              <label class="form-label">Main Contact Name *</label>
              <input type="text" class="form-control" v-model="form.customer_name" required />
              <div class="form-error" v-if="validationErrors.customer_name">{{ validationErrors.customer_name[0] }}</div>
            </div>

            <div class="form-group" style="flex: 1;">
              <label class="form-label">Main Contact Email *</label>
              <input type="email" class="form-control" v-model="form.customer_email" required />
              <div class="form-error" v-if="validationErrors.customer_email">{{ validationErrors.customer_email[0] }}</div>
            </div>
        </div>

        <hr style="border: 1px solid var(--color-border); margin: 2rem 0;" />

        <!-- Passengers Array -->
        <div class="passengers-section">
            <h3 style="margin-bottom: 1rem;">Passengers</h3>
            <div class="form-error mb-2" v-if="validationErrors.passengers">{{ validationErrors.passengers[0] }}</div>

            <div v-for="(passenger, index) in form.passengers" :key="index" class="passenger-card card">
               <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                  <h4 style="font-size: 1rem;">Passenger {{ index + 1 }}</h4>
                  <button type="button" class="btn btn-outline btn-sm text-danger" @click="removePassenger(index)" v-if="form.passengers.length > 1">
                     Remove
                  </button>
               </div>
               
               <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                  <div class="form-group mb-0">
                      <label class="form-label">Given Name *</label>
                      <input type="text" class="form-control" v-model="passenger.given_name" required />
                      <div class="form-error" v-if="validationErrors[`passengers.${index}.given_name`]">{{ validationErrors[`passengers.${index}.given_name`][0] }}</div>
                  </div>
                  <div class="form-group mb-0">
                      <label class="form-label">Surname *</label>
                      <input type="text" class="form-control" v-model="passenger.surname" required />
                      <div class="form-error" v-if="validationErrors[`passengers.${index}.surname`]">{{ validationErrors[`passengers.${index}.surname`][0] }}</div>
                  </div>
                  <div class="form-group mb-0">
                      <label class="form-label">Date of Birth *</label>
                      <input type="date" class="form-control" v-model="passenger.date_of_birth" required />
                      <div class="form-error" v-if="validationErrors[`passengers.${index}.date_of_birth`]">{{ validationErrors[`passengers.${index}.date_of_birth`][0] }}</div>
                  </div>
                  <div class="form-group mb-0">
                      <label class="form-label">Special Request (Dietary, Accessibility)</label>
                      <input type="text" class="form-control" v-model="passenger.special_request" />
                  </div>
               </div>
            </div>

            <button type="button" class="btn btn-outline" @click="addPassenger" style="margin-top: 1rem;">
               + Add Another Passenger
            </button>
        </div>

        <div style="margin-top: 2rem; display: flex; justify-content: flex-end; gap: 1rem; align-items: center;">
            <p v-if="form.passengers.length > 0" style="margin-right: auto;">
              <strong style="font-size: 1.25rem;">Est. Total: ${{ form.passengers.length * 100 }}.00</strong>
            </p>
            <router-link to="/tours" class="btn btn-outline">Cancel</router-link>
            <button type="submit" class="btn btn-primary" :disabled="isLoading">
               {{ isLoading ? 'Processing...' : (isEdit ? 'Save Changes' : 'Confirm Booking') }}
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
  name: 'BookingFormView',
  components: { AlertMessage, LoadingSpinner },
  data() {
    return {
      toursList: [],
      availableDates: [],
      form: {
        tour_id: '',
        tour_date_id: '',
        customer_name: '',
        customer_email: '',
        status: 'Submitted',
        passengers: [this.getEmptyPassenger()],
      },
    };
  },
  computed: {
    ...mapState('bookings', ['loading', 'error', 'validationErrors']),
    isEdit() {
      return !!this.$route.params.id;
    },
    isLoading: {
      get() { return this.loading; },
      set() {}
    }
  },
  methods: {
    ...mapActions('bookings', ['createBooking', 'updateBooking', 'fetchBooking', 'clearCurrentBooking']),
    
    getEmptyPassenger() {
      return {
        given_name: '',
        surname: '',
        date_of_birth: '',
        special_request: '',
      };
    },
    addPassenger() {
      this.form.passengers.push(this.getEmptyPassenger());
    },
    removePassenger(index) {
        if(this.form.passengers.length > 1) {
            this.form.passengers.splice(index, 1);
        }
    },
    async loadToursList() {
      try {
        const res = await tourService.list({ per_page: 50, status: 'all' });
        this.toursList = res.data.data;

        if (this.$route.query.tour_id) {
           this.form.tour_id = Number(this.$route.query.tour_id);
           this.fetchTourDetails();
        }
      } catch (e) {
        console.error(e);
      }
    },
    async fetchTourDetails() {
       if (!this.form.tour_id) return;
       try {
           const res = await tourService.get(this.form.tour_id);
           this.availableDates = res.data.data.tour_dates;
           if (this.availableDates.length > 0 && !this.form.tour_date_id) {
               this.form.tour_date_id = this.availableDates[0].id;
           }
       } catch (e) {
           console.error(e);
       }
    },
    async loadExistingBooking() {
       try {
           await this.$store.dispatch('bookings/fetchBooking', this.$route.params.id);
           const booking = this.$store.state.bookings.currentBooking;
           if (!booking) return;

           this.form.tour_id      = booking.tour?.id ?? '';
           this.form.tour_date_id = booking.tour_date?.id ?? '';
           this.form.customer_name  = booking.customer_name;
           this.form.customer_email = booking.customer_email;
           this.form.status = booking.status ?? 'Submitted';

           if (this.form.tour_id) await this.fetchTourDetails();

           if (booking.passengers?.length) {
               this.form.passengers = booking.passengers.map(p => ({
                   id:              p.id,
                   given_name:      p.given_name,
                   surname:         p.surname,
                   date_of_birth:   p.date_of_birth ?? '',
                   special_request: p.special_request ?? '',
               }));
           }
       } catch (e) {
           console.error('Failed to load booking:', e);
       }
    },
    async submitForm() {
       try {
           if (this.isEdit) {
               await this.updateBooking({ id: this.$route.params.id, data: this.form });
           } else {
               await this.createBooking(this.form);
           }
           this.$router.push('/bookings');
       } catch (e) {
           window.scrollTo(0, 0);
       }
    }
  },
  async mounted() {
     this.clearCurrentBooking();
     await this.loadToursList();
     
     if (this.isEdit) {
       await this.loadExistingBooking();
     }
  }
};
</script>

<style scoped>
.passenger-card {
    background: var(--color-bg);
    border: 1px dashed var(--color-border);
    margin-bottom: 1rem;
    padding: var(--space-4);
}
.passenger-card:hover { border-color: var(--color-primary); }
</style>
