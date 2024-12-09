<?php
// Iniciar sesión si no se ha hecho ya
session_start();

// Incluir el archivo de conexión
require_once '../model/conexion.php';

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recuperar los datos del formulario
    $email = $_POST['email'];
    $password = $_POST['password'];
    $nombre_usuario = $_POST['nombre_usuario']; // Nuevo campo requerido

    // Validar que el correo, la contraseña y el nombre de usuario no estén vacíos
    if (empty($email) || empty($password) || empty($nombre_usuario)) {
        $error = "Por favor, ingrese su nombre de usuario, correo y contraseña.";
    } else {
        // Conectar a la base de datos usando la clase Conexion
        $conexion = new Conexion();
        $conn = $conexion->conectar();

        if (!$conn) {
            echo "Error al conectar a la base de datos.";
            exit;
        }

        // Usar consultas preparadas para evitar inyecciones SQL
        $sql = "SELECT id_usuario, nombre_usuario, email, clave, id_rol FROM Usuarios WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        // Verificar si se encontró un usuario
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificar si la contraseña es correcta (usando password_verify)
            if (password_verify($password, $user['clave'])) {
                // Almacenar datos del usuario en la sesión
                $_SESSION['id_usuario'] = $user['id_usuario'];
                $_SESSION['nombre_usuario'] = $user['nombre_usuario'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['id_rol'] = $user['id_rol'];

                // Redirigir al menú
                header("Location: menu.php");
                exit();
            } else {
                // La contraseña no coincide
                $error = "Correo o contraseña incorrectos.";
            }
        } else {
            // No se encontró el usuario
            $error = "Correo o contraseña incorrectos.";
        }

        // Cerrar la conexión
        $conn = null;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="manifest" href="../manifest.json">
    <script src="../main.js" defer></script>
    

</head>
<body>
    <div class="login-page">
        <div class="login-container">
            <h2>Iniciar Sesión</h2>

            <!-- Mostrar mensaje de error si los datos son incorrectos -->
            <?php if (isset($error)): ?>
                <div class="error-message">
                    <p><?php echo $error; ?></p>
                </div>
            <?php endif; ?>

            <!-- Formulario de inicio de sesión -->
            <form action="index.php" method="POST"> <!-- Cambiado a index.php -->
                <input type="text" name="nombre_usuario" placeholder="Nombre de usuario" required>
                <input type="email" name="email" placeholder="Correo electrónico" required>
                <input type="password" name="password" placeholder="Contraseña" required>
                <button type="submit">Ingresar</button> <!-- Botón para enviar el formulario -->
            </form>

            <p>¿No tienes una cuenta? <a href="registro.php">Regístrate aquí</a></p>
        </div>
    </div>
    
</body>
</html>
