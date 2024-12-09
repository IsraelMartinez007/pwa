<?php
session_start(); // Asegúrate de iniciar la sesión
// Recuperar el total del carrito guardado en la sesión
$total_carrito = $_SESSION['total_carrito'] ?? 0; // Valor predeterminado si no existe
$nombre_usuario = $_SESSION['nombre_usuario'] ?? "Invitado"; // Valor predeterminado si no hay usuario
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Opciones de Pago</title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="manifest" href="../manifest.json">
    <script src="../main.js" defer></script>
    <style>
    /* Estilo del saludo */
    .product-box {
        position: relative;
        width: 200px;
        height: 300px;
        margin: 20px;
        border: 1px solid #ddd;
        padding: 10px;
        text-align: center;
        overflow: hidden;
        background-color: #f9f9f9;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease-in-out;
    }

    /* Estilo para las imágenes */
    .product-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: opacity 0.3s ease-in-out;
    }

    /* Ocultar los detalles del producto por defecto */
    .product-box .product-details {
        opacity: 0;
        position: absolute;
        bottom: 0;
        width: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 10px;
        box-sizing: border-box;
        transition: opacity 0.3s ease-in-out;
    }

    /* Estilo para el botón de agregar al carrito */
    .product-box .add-to-cart {
        position: absolute;
        top: 10px;
        left: 50%;
        transform: translateX(-50%);
        padding: 10px 20px;
        background-color: #ff5c5c;
        color: white;
        border: none;
        cursor: pointer;
        opacity: 0;
        transition: opacity 0.3s ease-in-out;
    }

    /* Cuando el ratón pasa sobre el producto, se muestran los detalles y el botón */
    .product-box:hover .product-details {
        opacity: 1;
    }

    .product-box:hover .add-to-cart {
        opacity: 1;
    }

    /* Para que el contenedor de producto tenga una transición suave */
    .product-box:hover {
        transform: scale(1.05);
    }

    /* Estilo para la barra de navegación */
    header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0px;
        background-color: #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Logo */
    .logo img {
        height: 20px;
    }

    /* Estilo para el mensaje de bienvenida */
    .welcome-message {
        margin-left: 20px;
        font-weight: bold;
        font-size: 18px;
        color: #333;
    }

    /* Estilo para los botones en el navbar */
    .buttons button {
        margin: 0 5px;
        padding: 5px;
        background-color: #ff5c5c;
        color: white;
        border: none;
        cursor: pointer;
    }

    /* Estilos específicos para la sección de pago */
    .login-container {
        max-width: 600px;
        margin: 50px auto;
        padding: 20px;
        background-color: #f8f8f8;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        font-family: Arial, sans-serif;
    }

    .login-container h2 {
        text-align: center;
        color: #333;
        font-size: 24px;
        margin-bottom: 20px;
    }

    .login-container label {
        display: block;
        margin-bottom: 10px;
        font-size: 16px;
        color: #444;
    }

    .login-container p {
        font-size: 18px;
        color: #555;
        margin-bottom: 15px;
    }

    .login-container input[type="radio"] {
        margin-right: 10px;
        cursor: pointer;
    }

    #installments-section {
        margin-top: 20px;
        padding: 15px;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    #quincenas {
        display: block;
        width: 100%;
        padding: 10px;
        margin-top: 10px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 5px;
        background-color: #f9f9f9;
        color: #333;
    }

    .login-container button {
        display: block;
        width: 100%;
        padding: 12px;
        font-size: 18px;
        color: white;
        background-color: #ff5c5c;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        margin-top: 20px;
    }

    .login-container button:hover {
        background-color: #e74c3c;
    }

    #total-carrito,
    #monto-quincenal {
        font-weight: bold;
        color: #ff5c5c;
    }
</style>

</head>
<body>
    <div class="page-container">
        <!-- Header fijo -->
        <header>
            <div class="logo">
                <img src="../img/logo.png" alt="Logo de la tienda"> <!-- Cambia por tu logo -->
            </div>
            <div class="welcome-message">
                Hola, <?php echo htmlspecialchars($nombre_usuario); ?> <!-- Saludo dinámico -->
            </div>
            <div class="buttons">
                <button onclick="location.href='menu.php'">Inicio</button>
                <button onclick="window.location.href='index.php'">Cerrar Sesión</button>
            </div>
        </header>

        <!-- Contenido principal -->
        <main>
            <div class="login-container">
                <h2>Opciones de Pago</h2>
                <form id="payment-form" action="procesar_pago.php" method="POST">
                    <!-- Mostrar el monto total -->
                    <p><strong>Monto Total (MXN):</strong> $<span id="total-carrito"><?php echo number_format($total_carrito, 2); ?></span></p>

                    <!-- Selección de método de pago -->
                    <label>
                        <input type="radio" name="payment_method" value="full" checked>
                        Pago completo
                    </label>
                    <br>
                    <label>
                        <input type="radio" name="payment_method" value="installments">
                        Pago en quincenas
                    </label>

                    <!-- Selección de quincenas (oculto por defecto) -->
                    <div id="installments-section" style="display: none;">
                        <label for="quincenas">Selecciona el número de quincenas:</label>
                        <select id="quincenas" name="quincenas">
                            <option value="2">2 quincenas</option>
                            <option value="4">4 quincenas</option>
                            <option value="6">6 quincenas</option>
                            <option value="8">8 quincenas</option>
                        </select>

                        <!-- Mostrar el monto por quincena -->
                        <p><strong>Monto por Quincena (MXN):</strong> $<span id="monto-quincenal">0.00</span></p>
                    </div>

                    <!-- Botón de enviar -->
                    <button type="submit">Continuar con el pago</button>
                </form>
            </div>
        </main>

        <!-- Footer -->
        <footer>
            <p>&copy; 2024 Tienda EEFECTIVA. Todos los derechos reservados</p>
        </footer>
    </div>

    <script>
        // Obtener referencias de los elementos
        const totalCarrito = <?php echo $total_carrito; ?>; // Total del carrito desde PHP
        const paymentMethodRadios = document.querySelectorAll('input[name="payment_method"]');
        const installmentsSection = document.getElementById('installments-section');
        const quincenasSelect = document.getElementById('quincenas');
        const montoQuincenalSpan = document.getElementById('monto-quincenal');

        // Función para actualizar la visibilidad de la sección de quincenas
        function toggleInstallmentsSection() {
            const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;
            if (selectedMethod === 'installments') {
                installmentsSection.style.display = 'block';
                actualizarMontoQuincenal();
            } else {
                installmentsSection.style.display = 'none';
                montoQuincenalSpan.textContent = "0.00";
            }
        }

        // Función para actualizar el monto por quincena
        function actualizarMontoQuincenal() {
            const quincenas = parseInt(quincenasSelect.value, 10);
            const montoQuincenal = totalCarrito / quincenas;
            montoQuincenalSpan.textContent = montoQuincenal.toFixed(2); // Formato de moneda
        }

        // Escuchar cambios en los radios del método de pago
        paymentMethodRadios.forEach(radio => {
            radio.addEventListener('change', toggleInstallmentsSection);
        });

        // Escuchar cambios en el selector de quincenas
        quincenasSelect.addEventListener('change', actualizarMontoQuincenal);

        // Calcular el monto inicial
        toggleInstallmentsSection();
    </script>
</body>
</html>
