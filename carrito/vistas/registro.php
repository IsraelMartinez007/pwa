<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "evnddata_juan", "XAqRt7x4@j9R6:", "evnddata_tienda_con_carrito"); // Cambia estos valores por tus credenciales de base de datos

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Validar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre_usuario = $conexion->real_escape_string($_POST['nombre_usuario']);
    $email = $conexion->real_escape_string($_POST['email']);
    $clave = $conexion->real_escape_string($_POST['clave']);
    $confirmar_password = $conexion->real_escape_string($_POST['confirmar_password']);

    // Validar que las contraseñas coincidan
    if ($clave !== $confirmar_password) {
        echo "<script>
            alert('Las contraseñas no coinciden.');
            window.history.back();
        </script>";
        exit();
    }

    // Validar si el nombre de usuario o el correo electrónico ya están registrados
    $sql_check_user = "SELECT * FROM Usuarios WHERE nombre_usuario = ? OR email = ?";
    $stmt = $conexion->prepare($sql_check_user);
    $stmt->bind_param("ss", $nombre_usuario, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['nombre_usuario'] === $nombre_usuario) {
            echo "<script>
                alert('El nombre de usuario ya está registrado. Intenta con otro.');
                window.location.href = 'registro.php'; // Redirige al formulario de registro
            </script>";
            exit();
        } elseif ($row['email'] === $email) {
            echo "<script>
                alert('El correo electrónico ya está registrado. Usa otro correo.');
                window.location.href = 'registro.php'; // Redirige al formulario de registro
            </script>";
            exit();
        }
    } else {
        // Encriptar la contraseña
        $password_hash = password_hash($clave, PASSWORD_DEFAULT);

        $sql_insert_user = "INSERT INTO Usuarios (nombre_usuario, email, clave) VALUES (?, ?, ?)";
        $stmt = $conexion->prepare($sql_insert_user);
        $stmt->bind_param("sss", $nombre_usuario, $email, $password_hash);

        if ($stmt->execute()) {
            echo "<script>
                alert('¡Registro exitoso! Ahora puedes iniciar sesión.');
                window.location.href = 'index.php'; // Redirige a la página de inicio de sesión
            </script>";
            exit();
        } else {
            echo "<script>
                alert('Error al registrar usuario: " . $stmt->error . "');
                window.location.href = 'registro.php'; // Redirige al formulario de registro
            </script>";
            exit();
        }
    }
    $stmt->close();
}

$conexion->close();
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="../style/style.css"> <!-- Vincula tu archivo CSS -->
    <link rel="manifest" href="../manifest.json">
    <script src="../main.js" defer></script>
</head>
<body>

    <div class="login-page">
        <div class="login-container">
            <h2>Regístrate</h2>

            <!-- Formulario de Registro -->
            <form action="registro.php" method="POST">
                <input type="text" name="nombre_usuario" placeholder="Nombre de Usuario" required>
                <input type="email" name="email" placeholder="Correo Electrónico" required>
                <input type="password" name="clave" placeholder="Contraseña" required>
                <input type="password" name="confirmar_password" placeholder="Confirmar Contraseña" required>
                <button type="submit">Registrarse</button>
            </form>

            <p>¿Ya tienes una cuenta? <a href="index.php">Iniciar sesión aquí</a></p>
        </div>
    </div>

</body>
</html>
