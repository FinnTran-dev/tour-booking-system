<template>
  <div class="passenger-form-view">
    <div class="page-header">
      <h1 class="page-title">{{ isEdit ? 'Edit Passenger' : 'Create New Passenger' }}</h1>
    </div>

    <AlertMessage :message="error" type="error" />
    <LoadingSpinner :loading="isLoading" />

    <div class="card" v-if="!isLoading">
      <form @submit.prevent="submitForm">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
          <div class="form-group">
            <label class="form-label" for="given_name">Given Name *</label>
            <input type="text" id="given_name" class="form-control" v-model="form.given_name" required />
            <div class="form-error" v-if="validationErrors.given_name">{{ validationErrors.given_name[0] }}</div> 
          </div>

          <div class="form-group">
            <label class="form-label" for="surname">Surname *</label>
            <input type="text" id="surname" class="form-control" v-model="form.surname" required />
            <div class="form-error" v-if="validationErrors.surname">{{ validationErrors.surname[0] }}</div>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
          <div class="form-group">
            <label class="form-label" for="email">Email</label>
            <input type="email" id="email" class="form-control" v-model="form.email" />
            <div class="form-error" v-if="validationErrors.email">{{ validationErrors.email[0] }}</div>
          </div>

          <div class="form-group">
            <label class="form-label" for="phone">Phone</label>
            <input type="text" id="phone" class="form-control" v-model="form.phone" />
            <div class="form-error" v-if="validationErrors.phone">{{ validationErrors.phone[0] }}</div>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
          <div class="form-group">
            <label class="form-label" for="date_of_birth">Date of Birth *</label>
            <input type="date" id="date_of_birth" class="form-control" v-model="form.date_of_birth" required />
            <div class="form-error" v-if="validationErrors.date_of_birth">{{ validationErrors.date_of_birth[0] }}</div>
          </div>

          <div class="form-group">
            <label class="form-label" for="status">Status</label>
            <select id="status" class="form-control" v-model="form.status">
              <option value="Enabled">Enabled</option>
              <option value="Disabled">Disabled</option>
            </select>
            <div class="form-error" v-if="validationErrors.status">{{ validationErrors.status[0] }}</div>
          </div>
        </div>

        <div class="form-actions" style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: flex-end;">
          <router-link to="/passengers" class="btn btn-outline">Cancel</router-link>
          <button type="submit" class="btn btn-primary" :disabled="isLoading">
            {{ isEdit ? 'Update Passenger' : 'Create Passenger' }}
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
import passengerService from '@/services/passengerService';

export default {
  name: 'PassengerFormView',
  components: { AlertMessage, LoadingSpinner },
  data() {
    return {
      validationErrors: {},
      form: {
        given_name: '',
        surname: '',
        email: '',
        phone: '',
        date_of_birth: '',
        status: 'Enabled',
      },
    };
  },
  computed: {
    ...mapState('passengers', ['loading', 'error']),
    isEdit() {
      return !!this.$route.params.id;
    },
    isLoading: {
      get() { return this.loading; },
      set() {}
    }
  },
  methods: {
    ...mapActions('passengers', ['createPassenger', 'updatePassenger', 'fetchPassenger']),
    async submitForm() {
      this.validationErrors = {};
      try {
        if (this.isEdit) {
          await this.updatePassenger({ id: this.$route.params.id, data: this.form });
        } else {
          await this.createPassenger(this.form);
        }
        this.$router.push('/passengers');
      } catch (err) {
        if (err.apiErrors) {
          this.validationErrors = err.apiErrors;
        }
      }
    },
    async loadExistingPassenger() {
      try {
        const res = await passengerService.get(this.$route.params.id);
        const p = res.data.data;
        this.form = {
          given_name: p.given_name,
          surname: p.surname,
          email: p.email,
          phone: p.phone,
          date_of_birth: p.date_of_birth,
          status: p.status,
        };
      } catch (e) {
        console.error(e);
      }
    }
  },
  async mounted() {
    if (this.isEdit) {
      await this.loadExistingPassenger();
    }
  }
};
</script>
