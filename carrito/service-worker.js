const CACHE_NAME = 'Tienda-v1';
const urlsToCache = [
    '/',
    './vistas/index.php',
    './style/style.css',
    './img/laptop.png',
    './img/logo.png',
    './model/conexion.php',
    './model/procesar_registro.php',
    './vistas/offline.html', 
    'main.js',
    'manifest.json',
];

// Evento de instalación
self.addEventListener("fetch", (e) => {
    console.log("Interceptando fetch:", e.request.url);

    e.respondWith(
        caches.match(e.request).then((res) => {
            if (res) {
                console.log("Recurso encontrado en caché:", e.request.url);
                return res;
            }

            return fetch(e.request)
                .then((networkRes) => {
                    if (networkRes && networkRes.status === 200) {
                        const networkResClone = networkRes.clone(); // Clonar antes de cachear
                        caches.open(CACHE_NAME).then((cache) => {
                            if (e.request.url.includes('/vistas/') || e.request.url.includes('/img/') || e.request.url.includes('/style/')) {
                                cache.put(e.request, networkResClone);
                                console.log("Recurso cacheado:", e.request.url);
                            }
                        });
                    }
                    return networkRes;
                })
                .catch(() => {
                    if (e.request.mode === 'navigate') {
                        return caches.match('./vistas/offline.html');
                    }
                });
        })
    );
});


// Evento de fetch: Manejar solicitudes de recursos
self.addEventListener("fetch", (e) => {
    console.log("Interceptando fetch:", e.request.url);

    // Verificamos si la solicitud es una de las vistas o recursos
    e.respondWith(
        caches.match(e.request).then((res) => {
            // Si el recurso está en caché, lo devolvemos
            if (res) {
                console.log("Recurso encontrado en caché:", e.request.url);
                return res;
            }

            // Si no está en caché, intentamos obtenerlo de la red
            return fetch(e.request)
                .then((networkRes) => {
                    // Si la respuesta es exitosa y es una vista, la cacheamos
                    if (networkRes && networkRes.status === 200) {
                        caches.open(CACHE_NAME).then((cache) => {
                            // Cacheamos las vistas, solo si son páginas o archivos relevantes
                            if (e.request.url.includes('/vistas/')) {
                                cache.put(e.request, networkRes.clone());
                                console.log("Vista cacheada:", e.request.url);
                            }
                        });
                    }
                    return networkRes;
                })
                .catch(() => {
                    // Si hay error de red, devolvemos la página offline
                    if (e.request.mode === 'navigate') {
                        return caches.match('vistas/offline.html');
                    }
                });
        })
    );
});
