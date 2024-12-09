<?php
session_start();

// Verificar si el usuario está autenticado
$nombre_usuario = $_SESSION['nombre_usuario'] ?? "Invitado";
$total_carrito = $_SESSION['total_carrito'] ?? 0; // Total del carrito

// Simular que el pago fue procesado exitosamente
$payment_success = true; // Esto se debe establecer después de recibir la respuesta de PayPal

// Datos del pago (pueden ser recibidos desde PayPal o base de datos)
$payment_id = $_GET['payment_id'] ?? null;
$transaction_id = $_GET['transaction_id'] ?? null;
$amount_paid = $_GET['amount'] ?? 0.00;
$currency = 'MXN'; // Asegúrate de que esto coincida con la moneda utilizada en PayPal

// Validar que los parámetros esenciales estén presentes
if (!$payment_id || !$transaction_id || $amount_paid <= 0) {
    $payment_success = false;
    $error_message = "Faltan datos del pago. Por favor, inténtalo nuevamente.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago Exitoso - Tienda EEFECTIVA</title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="manifest" href="../manifest.json">
    <script src="../main.js" defer></script>
    <style>
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
        .success-container{
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            font-family: Arial, sans-serif;
            text-align: center;
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
            <button onclick="location.href='logout.php'">Cerrar Sesión</button> <!-- Cambiar a logout.php -->
        </div>
    </header>

    <main>
        <div class="success-container">
            <h2>¡Pago Completado con Éxito!</h2>
            
            <?php if ($payment_success): ?>
                <p>Gracias por tu compra, <strong><?php echo htmlspecialchars($nombre_usuario); ?></strong>.</p>
                <p><strong>Detalles de la transacción:</strong></p>
                <ul>
                    <li><strong>Pago completado con éxito.</strong></li>
                    <li><strong>ID del pago:</strong> <?php echo htmlspecialchars($payment_id); ?></li>
                    <li><strong>ID de transacción:</strong> <?php echo htmlspecialchars($transaction_id); ?></li>
                    <li><strong>Monto pagado:</strong> $<?php echo number_format($amount_paid, 2); ?> <?php echo htmlspecialchars($currency); ?></li>
                </ul>
                <p>Te hemos enviado un correo de confirmación. ¡Gracias por confiar en nosotros!</p>
            <?php else: ?>
                <p><?php echo $error_message ?? "Hubo un problema con el pago. Por favor, inténtalo nuevamente."; ?></p>
            <?php endif; ?>

            <button onclick="location.href='menu.php'">Volver al inicio</button>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Tienda EFECTIVA. Todos los derechos reservados</p>
    </footer>
</body>
</html>
