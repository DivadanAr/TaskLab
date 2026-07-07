/**
 * Theme switcher (Light / Dark)
 * Preferensi disimpan di localStorage supaya tidak balik ke default saat refresh.
 * Catatan: penerapan tema awal (sebelum CSS render) dilakukan lewat inline script
 * kecil di <head> tiap halaman (lihat includes/theme-init.php), supaya tidak
 * terjadi "kilatan" (flash) warna default sebelum JS ini sempat jalan.
 */
const Theme = {
  KEY: 'tasklab-theme',

  get() {
    return localStorage.getItem(this.KEY) || 'dark';
  },

  apply(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem(this.KEY, theme);
  },

  toggle() {
    const next = this.get() === 'light' ? 'dark' : 'light';
    this.apply(next);
  },

  init() {
    // Tema sebenarnya sudah dipasang oleh inline script di <head>.
    // Ini hanya jaga-jaga kalau inline script tidak ada.
    if (!document.documentElement.hasAttribute('data-theme')) {
      this.apply(this.get());
    }
  }
};

document.addEventListener('DOMContentLoaded', () => Theme.init());
