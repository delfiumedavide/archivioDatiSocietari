import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

// Notification bell component
Alpine.data('notificationBell', () => ({
    open: false,
    count: 0,
    notifications: [],

    init() {
        this.fetchCount();
        setInterval(() => this.fetchCount(), 60000);
    },

    async fetchCount() {
        try {
            const response = await fetch('/api/notifications/unread-count', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });
            if (response.ok) {
                const data = await response.json();
                this.count = data.count;
            }
        } catch (e) {
            // silently fail
        }
    },

    async markAsRead(id) {
        try {
            const token = document.querySelector('meta[name="csrf-token"]').content;
            await fetch(`/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });
            this.fetchCount();
        } catch (e) {
            // silently fail
        }
    },

    async markAllAsRead() {
        try {
            const token = document.querySelector('meta[name="csrf-token"]').content;
            await fetch('/notifications/read-all', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });
            this.count = 0;
        } catch (e) {
            // silently fail
        }
    },

    toggle() {
        this.open = !this.open;
    },
}));

// File upload component
Alpine.data('fileUpload', () => ({
    dragging: false,
    fileName: '',
    fileSize: '',

    handleDrop(event) {
        this.dragging = false;
        const files = event.dataTransfer.files;
        if (files.length > 0) {
            this.$refs.fileInput.files = files;
            this.updateFileInfo(files[0]);
        }
    },

    handleSelect(event) {
        const files = event.target.files;
        if (files.length > 0) {
            this.updateFileInfo(files[0]);
        }
    },

    updateFileInfo(file) {
        this.fileName = file.name;
        const mb = file.size / (1024 * 1024);
        this.fileSize = mb >= 1 ? mb.toFixed(2) + ' MB' : (file.size / 1024).toFixed(1) + ' KB';
    },
}));

// Confirm delete
Alpine.data('confirmDelete', () => ({
    showModal: false,
    formAction: '',

    confirm(action) {
        this.formAction = action;
        this.showModal = true;
    },

    submit() {
        const form = this.$refs.deleteForm;
        form.action = this.formAction;
        form.submit();
    },
}));

// Tab navigation
Alpine.data('tabs', (defaultTab = '') => ({
    activeTab: defaultTab,
    setTab(tab) {
        this.activeTab = tab;
        window.location.hash = tab;
    },
    init() {
        if (window.location.hash) {
            this.activeTab = window.location.hash.substring(1);
        }
    },
}));

Alpine.start();

// Register Service Worker for push notifications
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js').catch(() => {});
}
