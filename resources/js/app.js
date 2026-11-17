import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Logika UI dashboard: tab, modal, pencarian, toast.
Alpine.data('dashboard', () => ({
  tab: location.hash.slice(1) || 'inventory',
  modal: null,
  data: {},
  qItems: '',
  filterCategory: '',
  filterUnit: '',
  filterSupplier: '',
  filtersOpen: false,
  qSuppliers: '',
  sidebarOpen: false,
  profileOpen: false,
  toastOn: false,
  toastMsg: '',

  init() {
    // Hash yang tidak diizinkan (mis. #logs untuk admin) kembali ke tab awal
    const allowed = [...this.$root.querySelectorAll('[data-tab]')].map(b => b.dataset.tab);
    if (!allowed.includes(this.tab)) this.tab = 'inventory';
    this.$watch('tab', t => history.replaceState(null, '', '#' + t));
  },

  open(name, data = {}) { this.data = data; this.modal = name; },
  close() {
    this.modal = null;
    this.profileOpen = false;
  },
  toggleProfile() { this.profileOpen = !this.profileOpen; },

  toast(msg) {
    this.toastMsg = msg;
    this.toastOn = true;
    clearTimeout(this._t);
    this._t = setTimeout(() => (this.toastOn = false), 3000);
  },

  formatPrice(event) {
    const digits = event.target.value.replace(/\D/g, '');
    event.target.value = digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  },
}));

Alpine.start();
