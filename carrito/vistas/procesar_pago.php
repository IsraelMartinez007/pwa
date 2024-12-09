<?php
session_start();

// Verificar si el usuario está autenticado
$nombre_usuario = $_SESSION['nombre_usuario'] ?? "Invitado";
$total_carrito = $_SESSION['total_carrito'] ?? 0; // Total del carrito

// Verificar si hay un monto en el carrito
if ($total_carrito <= 0) {
    die("El carrito está vacío o no tiene un monto válido.");
}

// Verificar el método de pago elegido (completo o en quincenas)
$metodo_pago = $_POST['payment_method'] ?? 'full'; // 'full' por defecto

// Inicializar variables de pago
$monto_pago = 0;
$monto_quincenal = 0;
$descuento = 0;

// Si el usuario eligió pagar en quincenas
if ($metodo_pago == 'installments') {
    $quincenas = $_POST['quincenas'] ?? 2; // Definir el número de quincenas
    $monto_quincenal = $total_carrito / $quincenas;
    $monto_pago = $monto_quincenal; // Mostrar el monto por quincena
} else {
    // Si se elige el pago completo, aplicar un descuento
    $descuento = 0.10; // Descuento del 10%
    $monto_pago = $total_carrito * (1 - $descuento); // Descuento aplicado
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procesar Pago - PayPal</title>
    <link rel="stylesheet" href="../style/style.css">
    <script src="https://www.paypal.com/sdk/js?client-id=AXjyAyaP7cuhYBsr7usI_-zm0DtULGOj4UFuqOBlX9LMKHeQ3qfVj8EBdtujYxuCWOwlkUFxkN4GDMbg&currency=MXN"></script> <!-- Tu cliente de PayPal -->
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
        /* Estilo para los detalles */
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
        .product-box:hover .product-details {
            opacity: 1;
        }
        .product-box:hover .add-to-cart {
            opacity: 1;
        }
        .product-box:hover {
            transform: scale(1.05);
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .logo img {
            height: 20px;
        }
        .welcome-message {
            margin-left: 20px;
            font-weight: bold;
            font-size: 18px;
            color: #333;
        }
        .buttons button {
            margin: 0 5px;
            padding: 5px;
            background-color: #ff5c5c;
            color: white;
            border: none;
            cursor: pointer;
        }
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
    <header>
        <div class="logo">
            <img src="../img/logo.png" alt="Logo de la tienda">
        </div>
        <div class="welcome-message">
            Hola, <?php echo htmlspecialchars($nombre_usuario); ?>
        </div>
        <div class="buttons">
            <button onclick="location.href='menu.php'">Inicio</button>
            <button onclick="location.href='index.php'">Cerrar Sesión</button>
        </div>
    </header>

    <main>
        <div class="login-container">
            <h2>Procesar Pago con PayPal</h2>

            <!-- Mostrar solo la opción seleccionada -->
            <?php if ($metodo_pago == 'installments'): ?>
                <p><strong>Total a pagar (MXN):</strong> $<?php echo number_format($total_carrito, 2); ?></p>
                <p><strong>Monto por Quincena (MXN):</strong> $<?php echo number_format($monto_quincenal, 2); ?></p>
            <?php else: ?>
                <p><strong>Total a pagar (MXN):</strong> $<?php echo number_format($monto_pago, 2); ?> (Con 10% de descuento)</p>
            <?php endif; ?>

            <!-- Contenedor para el botón de PayPal -->
            <div id="paypal-button-container"></div>

            <script>
                paypal.Buttons({
                    // Crear el pedido con el monto correspondiente
                    createOrder: function(data, actions) {
                        let amount = <?php echo $monto_pago; ?>; // El monto correspondiente dependiendo del método de pago
                        return actions.order.create({
                            purchase_units: [{
                                amount: {
                                    value: amount.toFixed(2) // Precio del producto o monto por quincena
                                }
                            }]
                        });
                    },

                    // Si el pago es aprobado
                    onApprove: function(data, actions) {
                        return actions.order.capture().then(function(details) {
                            alert('Pago completado por ' + details.payer.name.given_name);
                            // Redirigir al usuario a la página de éxito
                            window.location.href = "pago_exitoso.php"; // Cambiar esta URL si es necesario
                        });
                    },

                    // Manejar errores
                    onError: function(err) {
                        console.error('Error en el pago: ', err);
                        alert('Hubo un problema con el pago.');
                    }
                }).render('#paypal-button-container'); // Renderiza el botón en el contenedor
            </script>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Tienda EFECTIVA. Todos los derechos reservados</p>
    </footer>
</body>
</html>
