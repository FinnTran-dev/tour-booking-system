<template>
  <div id="app">
    <aside class="sidebar">
      <div class="sidebar-brand">
        <h1>TourBook</h1>
      </div>
      <nav class="sidebar-nav">
        <router-link to="/tours" class="nav-link">
          <i class="fa-solid fa-map nav-icon"></i>
          <span>Tours</span>
        </router-link>
        <router-link to="/passengers" class="nav-link">
          <i class="fa-solid fa-users nav-icon"></i>
          <span>Passengers</span>
        </router-link>
        <router-link to="/bookings" class="nav-link">
          <i class="fa-solid fa-calendar-check nav-icon"></i>
          <span>Bookings</span>
        </router-link>
        <router-link to="/invoices" class="nav-link">
          <i class="fa-solid fa-file-invoice-dollar nav-icon"></i>
          <span>Invoices</span>
        </router-link>
        <router-link to="/bookings/new" class="nav-link">
          <i class="fa-solid fa-plus nav-icon"></i>
          <span>New Booking</span>
        </router-link>
      </nav>
    </aside>
    <main class="main-content">
      <router-view />
    </main>
  </div>
</template>

<script>
export default {
  name: 'App',
};
</script>

<style>
/* ============================================
   Design System — CSS Custom Properties (Light Theme)
   ============================================ */
:root {
  /* Brand Colors */
  --color-primary: #4f46e5;
  --color-primary-hover: #4338ca;
  --color-primary-light: rgba(79, 70, 229, 0.08);
  --color-success: #059669;
  --color-success-light: rgba(5, 150, 105, 0.08);
  --color-warning: #d97706;
  --color-warning-light: rgba(217, 119, 6, 0.08);
  --color-danger: #dc2626;
  --color-danger-light: rgba(220, 38, 38, 0.08);
  --color-info: #2563eb;
  --color-info-light: rgba(37, 99, 235, 0.08);

  /* Light Neutrals */
  --color-bg: #f5f6fa;
  --color-bg-secondary: #eef0f7;
  --color-bg-tertiary: #e2e5f0;
  --color-surface: #ffffff;
  --color-surface-hover: #f0f1f9;
  --color-border: #e2e8f0;
  --color-border-light: #cbd5e1;
  --color-text: #1e2440;
  --color-text-secondary: #64748b;
  --color-text-muted: #94a3b8;

  /* Typography */
  --font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
  --font-size-xs: 0.75rem;
  --font-size-sm: 0.875rem;
  --font-size-base: 1rem;
  --font-size-lg: 1.125rem;
  --font-size-xl: 1.25rem;
  --font-size-2xl: 1.5rem;
  --font-size-3xl: 1.875rem;

  /* Spacing */
  --space-1: 0.25rem;
  --space-2: 0.5rem;
  --space-3: 0.75rem;
  --space-4: 1rem;
  --space-5: 1.25rem;
  --space-6: 1.5rem;
  --space-8: 2rem;
  --space-10: 2.5rem;
  --space-12: 3rem;

  /* Borders */
  --radius-sm: 6px;
  --radius-md: 8px;
  --radius-lg: 12px;
  --radius-xl: 16px;

  /* Shadows — softer for light theme */
  --shadow-sm: 0 1px 3px rgba(30, 36, 64, 0.06), 0 1px 2px rgba(30, 36, 64, 0.04);
  --shadow-md: 0 4px 12px rgba(30, 36, 64, 0.08), 0 2px 4px rgba(30, 36, 64, 0.05);
  --shadow-lg: 0 10px 24px rgba(30, 36, 64, 0.1), 0 4px 8px rgba(30, 36, 64, 0.06);
  --shadow-glow: 0 0 0 3px rgba(79, 70, 229, 0.12);

  /* Transitions */
  --transition-fast: 150ms ease;
  --transition-base: 250ms ease;
  --transition-slow: 350ms ease;

  /* Layout */
  --sidebar-width: 260px;
}

/* ============================================
   Reset & Base
   ============================================ */
*,
*::before,
*::after {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

html {
  font-size: 16px;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

body {
  font-family: var(--font-family);
  background-color: var(--color-bg);
  color: var(--color-text);
  line-height: 1.6;
  min-height: 100vh;
}

a {
  color: var(--color-primary);
  text-decoration: none;
}

/* ============================================
   Layout
   ============================================ */
#app {
  display: flex;
  min-height: 100vh;
}

.sidebar {
  width: var(--sidebar-width);
  background: var(--color-surface);
  border-right: 1px solid var(--color-border);
  display: flex;
  flex-direction: column;
  position: fixed;
  top: 0;
  left: 0;
  bottom: 0;
  z-index: 100;
  box-shadow: var(--shadow-sm);
}

.sidebar-brand {
  padding: var(--space-6);
  border-bottom: 1px solid var(--color-border);
}

.sidebar-brand h1 {
  font-size: var(--font-size-xl);
  font-weight: 700;
  color: var(--color-primary);
}

.sidebar-nav {
  padding: var(--space-4);
  display: flex;
  flex-direction: column;
  gap: var(--space-1);
}

.nav-link {
  display: flex;
  align-items: center;
  gap: var(--space-3);
  padding: var(--space-3) var(--space-4);
  border-radius: var(--radius-md);
  color: var(--color-text-secondary);
  font-size: var(--font-size-sm);
  font-weight: 500;
  transition: all var(--transition-fast);
}

.nav-link:hover {
  background: var(--color-surface-hover);
  color: var(--color-text);
}

.nav-link.router-link-active {
  background: var(--color-primary-light);
  color: var(--color-primary);
}

.nav-icon {
  font-size: var(--font-size-base);
  width: 18px;
  text-align: center;
}

/* Empty state icon */
.empty-icon {
  font-size: 3rem;
  color: var(--color-text-muted);
  margin-bottom: var(--space-2);
}

.main-content {
  flex: 1;
  margin-left: var(--sidebar-width);
  padding: var(--space-8);
  min-height: 100vh;
}

/* ============================================
   Utility Classes
   ============================================ */
.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: var(--space-2);
  padding: var(--space-2) var(--space-5);
  font-family: var(--font-family);
  font-size: var(--font-size-sm);
  font-weight: 600;
  border: none;
  border-radius: var(--radius-md);
  cursor: pointer;
  transition: all var(--transition-fast);
  line-height: 1.5;
}

.btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.btn-primary {
  background: var(--color-primary);
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background: var(--color-primary-hover);
  box-shadow: var(--shadow-glow);
}

.btn-success {
  background: var(--color-success);
  color: white;
}

.btn-success:hover:not(:disabled) {
  background: #047857;
}

.btn-danger {
  background: var(--color-danger);
  color: white;
}

.btn-danger:hover:not(:disabled) {
  background: #b91c1c;
}

.btn-outline {
  background: transparent;
  border: 1px solid var(--color-border);
  color: var(--color-text-secondary);
}

.btn-outline:hover:not(:disabled) {
  border-color: var(--color-primary);
  color: var(--color-primary);
}

.btn-sm {
  padding: var(--space-1) var(--space-3);
  font-size: var(--font-size-xs);
}

/* Form Controls */
.form-group {
  margin-bottom: var(--space-5);
}

.form-label {
  display: block;
  font-size: var(--font-size-sm);
  font-weight: 500;
  color: var(--color-text-secondary);
  margin-bottom: var(--space-2);
}

.form-control {
  width: 100%;
  padding: var(--space-3) var(--space-4);
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  color: var(--color-text);
  font-family: var(--font-family);
  font-size: var(--font-size-sm);
  transition: border-color var(--transition-fast);
}

.form-control:focus {
  outline: none;
  border-color: var(--color-primary);
  box-shadow: 0 0 0 3px var(--color-primary-light);
}

.form-control::placeholder {
  color: var(--color-text-muted);
}

.form-error {
  font-size: var(--font-size-xs);
  color: var(--color-danger);
  margin-top: var(--space-1);
}

/* Cards */
.card {
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
  padding: var(--space-6);
  transition: all var(--transition-base);
}

.card:hover {
  border-color: var(--color-border-light);
  box-shadow: var(--shadow-md);
}

/* Status Badges */
.badge {
  display: inline-flex;
  align-items: center;
  padding: var(--space-1) var(--space-3);
  font-size: var(--font-size-xs);
  font-weight: 600;
  border-radius: 9999px;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.badge-public {
  background: var(--color-success-light);
  color: var(--color-success);
}

.badge-draft {
  background: var(--color-warning-light);
  color: var(--color-warning);
}

.badge-submitted {
  background: var(--color-info-light);
  color: var(--color-info);
}

.badge-confirmed {
  background: var(--color-success-light);
  color: var(--color-success);
}

.badge-cancelled {
  background: var(--color-danger-light);
  color: var(--color-danger);
}

.badge-paid {
  background: var(--color-success-light);
  color: var(--color-success);
}

.badge-unpaid {
  background: var(--color-warning-light);
  color: var(--color-warning);
}

.badge-enabled {
  background: var(--color-success-light);
  color: var(--color-success);
}

.badge-disabled {
  background: var(--color-danger-light);
  color: var(--color-danger);
}

/* Page Header */
.page-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: var(--space-8);
}

.page-title {
  font-size: var(--font-size-2xl);
  font-weight: 700;
}

/* Table */
.table-container {
  overflow-x: auto;
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
}

table {
  width: 100%;
  border-collapse: collapse;
}

th {
  text-align: left;
  padding: var(--space-3) var(--space-4);
  font-size: var(--font-size-xs);
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: var(--color-text-muted);
  background: var(--color-bg-secondary);
  border-bottom: 1px solid var(--color-border);
}

td {
  padding: var(--space-3) var(--space-4);
  font-size: var(--font-size-sm);
  border-bottom: 1px solid var(--color-border);
}

tr:last-child td {
  border-bottom: none;
}

tr:hover td {
  background: var(--color-surface-hover);
}

/* Pagination */
.pagination {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: var(--space-2);
  margin-top: var(--space-6);
}

.pagination-btn {
  padding: var(--space-2) var(--space-3);
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-sm);
  color: var(--color-text-secondary);
  font-size: var(--font-size-sm);
  cursor: pointer;
  transition: all var(--transition-fast);
}

.pagination-btn:hover:not(:disabled) {
  border-color: var(--color-primary);
  color: var(--color-primary);
}

.pagination-btn.active {
  background: var(--color-primary);
  border-color: var(--color-primary);
  color: white;
}

.pagination-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

/* Alerts */
.alert {
  padding: var(--space-3) var(--space-4);
  border-radius: var(--radius-md);
  margin-bottom: var(--space-4);
  font-size: var(--font-size-sm);
  font-weight: 500;
  border-left: 4px solid transparent;
}

.alert-error {
  background-color: var(--color-danger-light);
  color: var(--color-danger);
  border-left-color: var(--color-danger);
}

.alert-warning {
  background-color: var(--color-warning-light);
  color: var(--color-warning);
  border-left-color: var(--color-warning);
}

.alert-success {
  background-color: var(--color-success-light);
  color: var(--color-success);
  border-left-color: var(--color-success);
}

.alert-info {
  background-color: var(--color-info-light);
  color: var(--color-info);
  border-left-color: var(--color-info);
}
</style>
