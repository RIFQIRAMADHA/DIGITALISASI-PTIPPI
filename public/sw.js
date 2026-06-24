// Ini file Service Worker dasar.
// Sengaja dibikin kosong di bagian 'fetch' biar nggak ada masalah cache 
// pas lu lagi rajin ngoding/update fitur.

self.addEventListener('install', (event) => {
    console.log('[PWA] Service Worker Ter-install');
});

self.addEventListener('activate', (event) => {
    console.log('[PWA] Service Worker Aktif');
});

self.addEventListener('fetch', (event) => {
    // Biarin kosong biar selalu narik data terbaru dari server (nggak nyimpen cache lama)
});