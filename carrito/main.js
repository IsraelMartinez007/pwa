// Configurar Service Worker
if ('serviceWorker' in navigator) {
  let swLocation = "../service-worker.js";

  // Ajustar según el entorno
  if (window.location.href.includes("localhost")) {
      swLocation = "../service-worker.js";
  }

  navigator.serviceWorker
      .register(swLocation)
      .then((reg) => console.log("Service Worker registrado correctamente:", reg))
      .catch((err) => console.error("Error al registrar el Service Worker:", err));
} else {
  console.warn("Service Worker no es soportado en este navegador.");
}

console.log("Probando el Service Worker...");
