<template>
  <div class="booking-form-view">
    <div class="page-header">
      <h1 class="page-title">{{ isEdit ? 'Edit Booking' : 'New Booking' }}</h1>
    </div>

    <AlertMessage :message="error" type="error" />
    <LoadingSpinner :loading="isLoading" />

    <div class="booking-layout" v-if="!isLoading">
      <!-- MAIN FORM COLUMN -->
      <div class="booking-main-col card">
        <form @submit.prevent="submitForm">
          <section class="form-section">
            <h3 class="section-title"><i class="fa-solid fa-map-location-dot"></i> Tour & Date</h3>
            <div class="form-grid">
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
                     {{ d.date }} <span v-if="d.end_date && d.end_date !== d.date">(to {{ d.end_date }})</span>
                   </option>
                </select>
                <div class="form-error" v-if="validationErrors.tour_date_id">{{ validationErrors.tour_date_id[0] }}</div>
              </div>
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
          </section>

          <section class="form-section">
            <h3 class="section-title"><i class="fa-solid fa-address-card"></i> Main Contact</h3>
            <div class="form-grid">
                <div class="form-group">
                  <label class="form-label">Full Name *</label>
                  <input type="text" class="form-control" v-model="form.customer_name" required placeholder="John Doe" />
                  <div class="form-error" v-if="validationErrors.customer_name">{{ validationErrors.customer_name[0] }}</div>
                </div>

                <div class="form-group">
                  <label class="form-label">Email Address *</label>
                  <input type="email" class="form-control" v-model="form.customer_email" required placeholder="john@example.com" />
                  <div class="form-error" v-if="validationErrors.customer_email">{{ validationErrors.customer_email[0] }}</div>
                </div>
            </div>
          </section>

          <section class="form-section">
            <div class="section-header-flex">
              <h3 class="section-title"><i class="fa-solid fa-user-group"></i> Passengers in this Booking</h3>
              <div class="form-error" v-if="validationErrors.passengers">{{ validationErrors.passengers[0] }}</div>
            </div>

            <div class="booking-passengers-list">
              <div v-for="(passenger, index) in form.passengers" :key="index" class="passenger-item-row" :class="{ 'is-existing': passenger.id }">
                 <div class="passenger-item-header">
                    <div class="p-identity">
                      <span class="p-index">#{{ index + 1 }}</span>
                      <span v-if="passenger.id" class="badge badge-info">Existing</span>
                      <span v-else class="badge badge-success">New</span>
                    </div>
                    <button type="button" class="btn-icon-text text-danger" @click="removePassengerFromList(index)" v-if="form.passengers.length > 1">
                       <i class="fa-solid fa-trash-can"></i> Remove
                    </button>
                 </div>
                 
                 <div v-if="passenger.id" class="p-details-static">
                   <div class="p-name-line">
                     <strong>{{ passenger.given_name }} {{ passenger.surname }}</strong>
                     <span v-if="passenger.email" class="text-muted ml-2" style="font-size: 0.8rem;">({{ passenger.email }})</span>
                   </div>
                   <div class="form-group mt-3">
                      <label class="form-label-sub">Special Request</label>
                      <input type="text" class="form-control" v-model="passenger.special_request" placeholder="e.g. Vegetarian, Wheelchair access..." />
                   </div>
                 </div>

                  <div v-else class="p-details-form">
                    <div class="form-grid">
                      <div class="form-group mb-0">
                          <label class="form-label-sub">Given Name *</label>
                          <input type="text" class="form-control form-control-sm" v-model="passenger.given_name" required />
                          <div class="form-error" v-if="validationErrors[`passengers.${index}.given_name`]">{{ validationErrors[`passengers.${index}.given_name`][0] }}</div>
                      </div>
                      <div class="form-group mb-0">
                          <label class="form-label-sub">Surname *</label>
                          <input type="text" class="form-control form-control-sm" v-model="passenger.surname" required />
                          <div class="form-error" v-if="validationErrors[`passengers.${index}.surname`]">{{ validationErrors[`passengers.${index}.surname`][0] }}</div>
                      </div>
                      <div class="form-group mb-0">
                          <label class="form-label-sub">Email</label>
                          <input type="email" class="form-control form-control-sm" v-model="passenger.email" />
                          <div class="form-error" v-if="validationErrors[`passengers.${index}.email`]">{{ validationErrors[`passengers.${index}.email`][0] }}</div>
                      </div>
                      <div class="form-group mb-0">
                          <label class="form-label-sub">Phone</label>
                          <input type="text" class="form-control form-control-sm" v-model="passenger.phone" />
                          <div class="form-error" v-if="validationErrors[`passengers.${index}.phone`]">{{ validationErrors[`passengers.${index}.phone`][0] }}</div>
                      </div>
                      <div class="form-group mb-0">
                          <label class="form-label-sub">Date of Birth *</label>
                          <input type="date" class="form-control form-control-sm" v-model="passenger.date_of_birth" required />
                          <div class="form-error" v-if="validationErrors[`passengers.${index}.date_of_birth`]">{{ validationErrors[`passengers.${index}.date_of_birth`][0] }}</div>
                      </div>
                      <div class="form-group mb-0">
                          <label class="form-label-sub">Special Request</label>
                          <input type="text" class="form-control form-control-sm" v-model="passenger.special_request" />
                      </div>
                    </div>
                 </div>
              </div>
            </div>

            <div class="add-new-p-box">
              <button type="button" class="btn btn-outline btn-full" @click="addPassenger">
                 <i class="fa-solid fa-user-plus"></i> Add a New Passenger
              </button>
            </div>
          </section>

          <footer class="form-footer">
              <div class="price-summary">
                <span class="price-label">Estimated Total</span>
                <span class="price-amount">${{ form.passengers.length * 100 }}.00</span>
              </div>
              <div class="footer-actions">
                <router-link to="/bookings" class="btn btn-outline">Cancel</router-link>
                <button type="submit" class="btn btn-primary btn-lg" :disabled="isLoading">
                   {{ isLoading ? 'Processing...' : (isEdit ? 'Save Changes' : 'Confirm Booking') }}
                </button>
              </div>
          </footer>
        </form>
      </div>

      <!-- SIDEBAR PASSENGER LIBRARY -->
      <aside class="booking-sidebar">
        <div class="card sidebar-sticky">
          <h3 class="sidebar-title"><i class="fa-solid fa-address-book"></i> Passenger Directory</h3>
          <p class="sidebar-hint">Select existing passengers from our database to add them to this booking.</p>
          
          <div class="sidebar-search">
            <div class="search-input-wrapper">
              <i class="fa-solid fa-magnifying-glass search-icon"></i>
              <input 
                type="text" 
                class="form-control" 
                placeholder="Filter by name or email..." 
                v-model="passengerSearch"
              />
            </div>
          </div>

          <div class="directory-list">
            <div v-for="p in filteredExistingPassengers" :key="p.id" class="directory-item" :class="{ 'is-selected': isPassengerSelected(p.id) }">
              <div class="directory-info">
                <div class="directory-name">{{ p.given_name }} {{ p.surname }}</div>
                <div class="directory-sub">{{ p.email || 'No email' }}</div>
              </div>
              <button 
                type="button" 
                class="btn btn-sm" 
                :class="isPassengerSelected(p.id) ? 'btn-danger' : 'btn-success'"
                @click="togglePassenger(p)"
              >
                {{ isPassengerSelected(p.id) ? 'Remove' : 'Select' }}
              </button>
            </div>
            
            <div v-if="filteredExistingPassengers.length === 0" class="directory-empty">
              No matching passengers found.
            </div>
          </div>
        </div>
      </aside>
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
      existingPassengers: [],
      passengerSearch: '',
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
    },
    filteredExistingPassengers() {
      const query = (this.passengerSearch || '').toLowerCase();
      return this.existingPassengers.filter(p => 
        p.given_name.toLowerCase().includes(query) || 
        p.surname.toLowerCase().includes(query) ||
        (p.email && p.email.toLowerCase().includes(query))
      );
    }
  },
  methods: {
    ...mapActions('bookings', ['createBooking', 'updateBooking', 'fetchBooking', 'clearCurrentBooking']),
    ...mapActions('passengers', ['fetchPassengers']),
    
    getEmptyPassenger() {
      return {
        given_name: '',
        surname: '',
        email: '',
        phone: '',
        date_of_birth: '',
        special_request: '',
      };
    },
    addPassenger() {
      this.form.passengers.push(this.getEmptyPassenger());
    },
    removePassengerFromList(index) {
        if(this.form.passengers.length > 1) {
            this.form.passengers.splice(index, 1);
        }
    },
    isPassengerSelected(id) {
      return this.form.passengers.some(p => p.id === id);
    },
    togglePassenger(passenger) {
      const index = this.form.passengers.findIndex(p => p.id === passenger.id);
      if (index > -1) {
        // Remove
        this.form.passengers.splice(index, 1);
      } else {
        // Add
        this.form.passengers.push({
          id: passenger.id,
          given_name: passenger.given_name,
          surname: passenger.surname,
          email: passenger.email,
          phone: passenger.phone,
          special_request: '',
        });
      }
    },
    async searchExistingPassengers() {
      // We already have existingPassengers loaded or could fetch dynamically
      // For now let's just use the client-side filter in computed
    },
    async loadAllPassengers() {
      try {
        const res = await this.fetchPassengers({ per_page: 100 });
        this.existingPassengers = res.data.data;
      } catch (e) {
        console.error('Failed to load passengers:', e);
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
                   email:           p.email || '',
                   phone:           p.phone || '',
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
     await this.loadAllPassengers();
     
     if (this.isEdit) {
       await this.loadExistingBooking();
     }
  }
};
</script>

<style scoped>
/* Layout */
.booking-layout {
  display: grid;
  grid-template-columns: 1fr 340px;
  gap: var(--space-8);
  align-items: start;
}

.booking-main-col {
  max-width: 900px;
  padding: var(--space-8) !important;
}

.booking-sidebar {
  position: sticky;
  top: var(--space-6);
}

/* Sections */
.form-section {
  margin-bottom: var(--space-10);
}

.section-title {
  font-size: var(--font-size-lg);
  font-weight: 700;
  color: var(--color-primary);
  margin-bottom: var(--space-6);
  display: flex;
  align-items: center;
  gap: var(--space-3);
  border-bottom: 2px solid var(--color-primary-light);
  padding-bottom: var(--space-2);
}

.section-header-flex {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: var(--space-6);
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: var(--space-6);
}

/* Passenger List Items */
.booking-passengers-list {
  display: flex;
  flex-direction: column;
  gap: var(--space-4);
}

.passenger-item-row {
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
  padding: var(--space-5);
  transition: all var(--transition-base);
}

.passenger-item-row.is-existing {
  background: var(--color-bg-tertiary);
  border-left: 4px solid var(--color-secondary);
}

.passenger-item-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: var(--space-4);
}

.p-identity {
  display: flex;
  align-items: center;
  gap: var(--space-3);
}

.p-index {
  background: var(--color-primary);
  color: white;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 10px;
  font-weight: bold;
}

.p-name-line {
  font-size: var(--font-size-base);
}

.form-label-sub {
  display: block;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  color: var(--color-text-muted);
  margin-bottom: 4px;
}

.form-control-sm {
  padding: var(--space-2) var(--space-3);
  font-size: 13px;
}

.add-new-p-box {
  margin-top: var(--space-6);
}

.btn-full {
  width: 100%;
  padding: var(--space-4);
  border-style: dashed;
}

.btn-icon-text {
  background: none;
  border: none;
  color: var(--color-text-secondary);
  font-size: 12px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 4px;
}

.btn-icon-text:hover {
  text-decoration: underline;
}

/* Footer & Price */
.form-footer {
  margin-top: var(--space-12);
  padding-top: var(--space-8);
  border-top: 1px solid var(--color-border);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.price-summary {
  display: flex;
  flex-direction: column;
}

.price-label {
  font-size: 12px;
  color: var(--color-text-secondary);
  text-transform: uppercase;
  font-weight: 600;
}

.price-amount {
  font-size: var(--font-size-3xl);
  font-weight: 800;
  color: var(--color-text);
}

.footer-actions {
  display: flex;
  gap: var(--space-4);
}

.btn-lg {
  padding: var(--space-4) var(--space-10);
  font-size: var(--font-size-base);
}

/* Sidebar Directory */
.sidebar-title {
  font-size: var(--font-size-base);
  margin-bottom: var(--space-2);
  color: var(--color-primary);
}

.sidebar-hint {
  font-size: 12px;
  color: var(--color-text-secondary);
  margin-bottom: var(--space-6);
}

.sidebar-search {
  margin-bottom: var(--space-4);
}

.search-input-wrapper {
  position: relative;
}

.search-icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--color-text-muted);
  font-size: 12px;
}

.search-input-wrapper .form-control {
  padding-left: 32px;
  font-size: 13px;
}

.directory-list {
  max-height: 500px;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: var(--space-2);
}

.directory-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: var(--space-3);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  transition: all var(--transition-fast);
}

.directory-item.is-selected {
  background: var(--color-success-light);
  border-color: var(--color-success);
}

.directory-name {
  font-size: 13px;
  font-weight: 600;
}

.directory-sub {
  font-size: 11px;
  color: var(--color-text-secondary);
}

.directory-empty {
  text-align: center;
  padding: var(--space-4);
  color: var(--color-text-muted);
  font-size: 12px;
}
</style>
