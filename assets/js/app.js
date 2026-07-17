import Alpine from 'alpinejs';
import flatpickr from 'flatpickr';
import { Notyf } from 'notyf';
import Swal from 'sweetalert2';

window.Alpine = Alpine;
window.Notyf = Notyf;
window.Swal = Swal;

Alpine.start();

window.notyf = new Notyf({
  duration: 4000,
  position: { x: 'right', y: 'top' },
});

window.initialiserFlatpickr = function(selector) {
  document.querySelectorAll(selector).forEach((element) => {
    flatpickr(element, {
      dateFormat: 'd/m/Y',
      locale: 'fr',
    });
  });
};

window.confirmAction = function(options) {
  return Swal.fire({
    title: options.title || 'Confirmation',
    text: options.text || 'Voulez-vous continuer ?',
    icon: options.icon || 'warning',
    showCancelButton: true,
    confirmButtonText: options.confirmButtonText || 'Oui',
    cancelButtonText: options.cancelButtonText || 'Non',
    reverseButtons: true,
  });
};

document.addEventListener('DOMContentLoaded', () => {
    const themeToggle = document.getElementById('themeToggle');
    const root = document.documentElement;

    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        root.setAttribute('data-theme', savedTheme);
    }

    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const currentTheme = root.getAttribute('data-theme') || 'light';
            const nextTheme = currentTheme === 'dark' ? 'light' : 'dark';
            root.setAttribute('data-theme', nextTheme);
            localStorage.setItem('theme', nextTheme);
        });
    }
});
