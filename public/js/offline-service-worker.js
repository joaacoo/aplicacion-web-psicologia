const CACHE_NAME = 'clinical-history-v1';
const OFFLINE_NOTES_KEY = 'pending_clinical_notes';

self.addEventListener('install', (event) => {
    console.log('[Service Worker] Installing...');
    event.waitUntil(caches.open(CACHE_NAME));
});

self.addEventListener('activate', (event) => {
    console.log('[Service Worker] Activating...');
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        console.log('[Service Worker] Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});

self.addEventListener('fetch', (event) => {
    if (event.request.method === 'GET') {
        event.respondWith(
            fetch(event.request)
                .catch(() => caches.match(event.request))
        );
    }
});

self.addEventListener('sync', (event) => {
    if (event.tag === 'sync-clinical-notes') {
        console.log('[Service Worker] Syncing clinical notes...');
        event.waitUntil(syncPendingNotes());
    }
});

async function syncPendingNotes() {
    const pendingNotes = JSON.parse(localStorage.getItem(OFFLINE_NOTES_KEY) || '[]');

    console.log('[Service Worker] Pending notes to sync:', pendingNotes.length);

    for (const note of pendingNotes) {
        try {
            const response = await fetch(
                `/admin/pacientes/${note.paciente_id}/historia-clinica/${note.turno_id}`,
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': note.csrf_token,
                    },
                    body: JSON.stringify({ content: note.content }),
                }
            );

            if (response.ok) {
                console.log('[Service Worker] Note synced successfully:', note.turno_id);
                const updated = pendingNotes.filter(n => n.turno_id !== note.turno_id);
                localStorage.setItem(OFFLINE_NOTES_KEY, JSON.stringify(updated));
            } else {
                console.error('[Service Worker] Sync failed for note:', note.turno_id, response.status);
            }
        } catch (error) {
            console.error('[Service Worker] Sync error:', error);
        }
    }
}
