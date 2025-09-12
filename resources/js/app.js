import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Client-side cart utilities
window.ClientCart = {
  get() {
    try { return JSON.parse(localStorage.getItem('cart') || '[]'); } catch (e) { return []; }
  },
  set(items) {
    localStorage.setItem('cart', JSON.stringify(items));
  },
  count() {
    return this.get().reduce((sum, it) => sum + (parseInt(it.quantity) || 0), 0);
  }
};

document.addEventListener('DOMContentLoaded', function () {
  const counter = document.querySelector('.cart-count');
  if (counter) {
    counter.textContent = window.ClientCart.count();
  }
});
