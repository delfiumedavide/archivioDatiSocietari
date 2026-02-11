// Service Worker for Push Notifications
// Archivio Dati Societari - Gruppo di Martino

self.addEventListener('push', function(event) {
    if (!event.data) return;

    let data;
    try {
        data = event.data.json();
    } catch (e) {
        data = {
            title: 'Archivio Societario',
            body: event.data.text(),
            icon: '/images/logo-icon.svg',
            url: '/',
        };
    }

    const options = {
        body: data.body || '',
        icon: data.icon || '/images/logo-icon.svg',
        badge: '/images/logo-icon.svg',
        vibrate: [200, 100, 200],
        data: {
            url: data.url || '/',
        },
        actions: [
            { action: 'open', title: 'Apri' },
            { action: 'close', title: 'Chiudi' },
        ],
    };

    event.waitUntil(
        self.registration.showNotification(data.title || 'Archivio Societario', options)
    );
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();

    if (event.action === 'close') return;

    const url = event.notification.data?.url || '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then(function(clientList) {
                for (const client of clientList) {
                    if (client.url.includes(self.location.origin) && 'focus' in client) {
                        client.navigate(url);
                        return client.focus();
                    }
                }
                return clients.openWindow(url);
            })
    );
});

self.addEventListener('install', function(event) {
    self.skipWaiting();
});

self.addEventListener('activate', function(event) {
    event.waitUntil(self.clients.claim());
});
