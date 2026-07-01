import './bootstrap';

import Alpine from 'alpinejs';
import Swal from 'sweetalert2';
import ApexCharts from 'apexcharts';

window.Alpine = Alpine;
window.Swal = Swal;
window.ApexCharts = ApexCharts;

// Tracks which row action menu (kebab) is currently open — only one at a time.
Alpine.store('menu', { current: null });

Alpine.start();

/**
 * Reusable toast (top-right, auto-dismiss).
 */
window.showToast = function (icon, title) {
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: icon,
        title: title,
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
    });
};

/**
 * Global delete/confirm handler.
 * Any <form data-confirm="message…"> shows a SweetAlert confirm dialog
 * and only submits when the user confirms — replaces native confirm().
 */
document.addEventListener('submit', function (e) {
    const form = e.target;
    if (!(form instanceof HTMLFormElement)) return;
    if (!form.hasAttribute('data-confirm')) return;
    if (form.dataset.confirmed === 'true') return; // already confirmed, let it through

    e.preventDefault();

    Swal.fire({
        title: form.dataset.confirmTitle || 'Are you sure?',
        text: form.getAttribute('data-confirm'),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: form.dataset.confirmButton || 'Yes, delete it',
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            form.dataset.confirmed = 'true';
            form.submit();
        }
    });
}, true);
