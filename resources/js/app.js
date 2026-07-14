import './bootstrap';

import Alpine from 'alpinejs';
import Swal from 'sweetalert2';
import ApexCharts from 'apexcharts';
import { Html5Qrcode } from 'html5-qrcode';
import Sortable from 'sortablejs';

window.Alpine = Alpine;
window.Swal = Swal;
window.ApexCharts = ApexCharts;
window.Html5Qrcode = Html5Qrcode;
window.Sortable = Sortable;

// Tracks which row action menu (kebab) is currently open — only one at a time.
Alpine.store('menu', { current: null });

// AJAX pagination for dashboard cards — swaps the card body in place, no page reload.
Alpine.data('ajaxPager', () => ({
    loading: false,
    async go(e) {
        const link = e.target.closest('a');
        if (! link) return;
        // Only intercept the pagination links — let normal links (e.g. asset tags) navigate.
        const pager = link.closest('[data-pagination]');
        if (! pager || ! this.$root.contains(pager)) return;
        e.preventDefault();
        this.loading = true;
        try {
            const res = await fetch(link.getAttribute('href'), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (res.ok) {
                this.$root.querySelector('[data-pager-body]').innerHTML = await res.text();
            }
        } finally {
            this.loading = false;
        }
    },
}));

// Barcode / QR scanner (camera) — reads 1D & 2D codes and auto-fills the asset form.
// Surfaces clear errors (permission, no camera, insecure page) instead of failing silently.
Alpine.data('barcodeScanner', () => ({
    open: false,
    qr: null,
    error: '',
    status: '',
    done: false,
    cameras: [],
    cameraId: '',
    scannerId: 'scanner-' + Math.floor(Math.random() * 1e9),
    config: {
        fps: 10,
        qrbox: (vw, vh) => { const m = Math.floor(Math.min(vw, vh) * 0.8); return { width: m, height: m }; },
        experimentalFeatures: { useBarCodeDetectorIfSupported: true },
    },

    toggle() { this.open ? this.stop() : this.start(); },

    async start() {
        this.error = '';
        this.status = '';
        this.done = false;
        this.open = true;

        const host = location.hostname;
        const secure = window.isSecureContext || host === 'localhost' || host === '127.0.0.1';
        if (! secure) {
            this.error = 'Camera is blocked because this page is not secure. Open the app at http://localhost, set up HTTPS, or use a USB scanner. (Currently: ' + location.origin + ')';
            return;
        }
        if (! navigator.mediaDevices || ! navigator.mediaDevices.getUserMedia) {
            this.error = 'This browser can’t access the camera. Try Chrome or Edge, or use a USB barcode scanner.';
            return;
        }

        this.status = 'Requesting camera…';
        await this.$nextTick();

        try {
            const cams = await Html5Qrcode.getCameras(); // also triggers the permission prompt
            if (! cams || ! cams.length) {
                this.error = 'No camera was found on this device. Use a USB barcode scanner instead.';
                this.status = '';
                return;
            }
            this.cameras = cams.map((c, i) => ({ id: c.id, label: c.label || ('Camera ' + (i + 1)) }));
            // Default to a rear/back camera when we can identify one.
            const rear = this.cameras.find(c => /back|rear|environment/i.test(c.label));
            this.cameraId = (rear || this.cameras[0]).id;
            await this.run(this.cameraId);
        } catch (e) {
            this.error = this.friendly(e);
            this.status = '';
        }
    },

    // (Re)start scanning on the chosen camera.
    async run(id) {
        if (! this.qr) this.qr = new Html5Qrcode(this.scannerId);
        try { await this.qr.stop(); } catch (_) {}
        this.status = 'Starting camera…';
        await this.qr.start(id, this.config, (t) => this.onScan(t), () => {});
        this.status = 'Point the camera at a 1D barcode or QR code…';
    },

    async changeCamera() {
        this.error = '';
        this.done = false;
        try {
            await this.run(this.cameraId);
        } catch (e) {
            this.error = this.friendly(e);
            this.status = '';
        }
    },

    friendly(e) {
        const msg = (e && (e.name || e.message || e) || '').toString();
        if (/NotAllowedError|Permission|denied/i.test(msg)) return 'Camera permission was denied. Click the camera icon in your browser’s address bar, allow access, then try again.';
        if (/NotFoundError|Requested device not found|no camera/i.test(msg)) return 'No camera was found on this device. Use a USB barcode scanner instead.';
        if (/NotReadableError|in use|Could not start video source/i.test(msg)) return 'The camera is in use by another app (e.g. Zoom/Teams). Close it and try again.';
        if (/OverconstrainedError/i.test(msg)) return 'The selected camera isn’t available. Try again or use a USB scanner.';
        if (/secure|https/i.test(msg)) return 'Camera needs HTTPS or localhost.';
        return 'Could not start the camera: ' + msg;
    },

    async stop() {
        this.status = '';
        if (this.qr) {
            try { await this.qr.stop(); } catch (_) {}
            try { this.qr.clear(); } catch (_) {}
            this.qr = null;
        }
        this.open = false;
    },

    onScan(text) {
        if (this.done) return;
        this.done = true;

        const set = (id, val) => {
            const el = document.getElementById(id);
            if (el && val) { el.value = val; el.dispatchEvent(new Event('input', { bubbles: true })); el.dispatchEvent(new Event('change', { bubbles: true })); }
        };
        // Our own asset QR encodes "Model: … / Serial: … / Assignee: …" — parse it; otherwise use the raw code.
        let serial = text.trim();
        const sm = text.match(/Serial:\s*(.+)/i);
        if (sm) serial = sm[1].trim();
        set('serial_number', serial);

        const mm = text.match(/Model:\s*(.+)/i);
        if (mm) {
            const modelSel = document.getElementById('model_id');
            if (modelSel) {
                const name = mm[1].trim().toLowerCase();
                Array.from(modelSel.options).forEach(o => { if (o.text.trim().toLowerCase() === name) modelSel.value = o.value; });
                modelSel.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }
        if (window.showToast) window.showToast('success', 'Scanned: ' + serial);
        this.stop();
    },
}));

Alpine.start();

/**
 * Kanban board — drag sticky notes between columns and reorder them.
 * Persists the new column + order to the server via AJAX.
 */
document.addEventListener('DOMContentLoaded', () => {
    const lists = document.querySelectorAll('[data-kanban-list]');
    if (! lists.length) return;

    const moveUrl = document.querySelector('meta[name="board-move-url"]')?.content;
    const token   = document.querySelector('meta[name="csrf-token"]')?.content;

    const refreshCounts = () => {
        document.querySelectorAll('[data-kanban-list]').forEach((l) => {
            const badge = document.querySelector(`[data-count-for="${l.dataset.kanbanList}"]`);
            if (badge) badge.textContent = l.querySelectorAll('[data-ticket-id]').length;
        });
    };

    lists.forEach((list) => {
        Sortable.create(list, {
            group: 'kanban',
            animation: 150,
            ghostClass: 'kanban-ghost',
            // Let buttons/links/forms inside a card stay clickable.
            filter: 'a, button, form, input, select, textarea',
            preventOnFilter: false,
            onEnd: async (evt) => {
                const target = evt.to;
                const status = target.dataset.kanbanList;
                const id     = Number(evt.item.dataset.ticketId);
                const ids    = Array.from(target.querySelectorAll('[data-ticket-id]'))
                    .map((el) => Number(el.dataset.ticketId));

                refreshCounts();

                try {
                    const res = await fetch(moveUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token,
                        },
                        body: JSON.stringify({ id, status, ids }),
                    });
                    if (! res.ok) throw new Error('save failed');
                } catch (e) {
                    if (window.showToast) window.showToast('error', 'Could not save — refresh and try again.');
                }
            },
        });
    });
});

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
