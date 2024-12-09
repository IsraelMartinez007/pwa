<?php
// Configuración de la conexión a la base de datos
$host = "localhost";
$dbname = "evnddata_tienda_con_carrito"; // Cambia este valor por el nombre de tu base de datos
$username = "evnddata_juan";
$password = "XAqRt7x4@j9R6:";

// Conexión a la base de datos
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Procesar el formulario al enviar
$mensaje = ""; // Variable para mensajes de éxito o error
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_producto = $_POST['nombre_producto'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $id_categoria = $_POST['id_categoria'];

    // Subir la imagen
    $imagen_url = "";
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $directorio_destino = "img/"; // Cambiar a "img/"
        $nombre_archivo = basename($_FILES['imagen']['name']);
        $ruta_destino = $directorio_destino . $nombre_archivo;

        // Crear el directorio si no existe
        if (!file_exists($directorio_destino)) {
            mkdir($directorio_destino, 0777, true);
        }

        // Mover el archivo subido al directorio destino
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino)) {
            $imagen_url = $ruta_destino; // Almacenar la ruta en la base de datos
        } else {
            $mensaje = "<div class='error-message'>Error al subir la imagen. Verifica los permisos del directorio.</div>";
        }
    } else {
        $mensaje = "<div class='error-message'>Error al cargar la imagen: " . $_FILES['imagen']['error'] . "</div>";
    }

    // Insertar en la base de datos si no hay errores con la imagen
    if (empty($mensaje)) {
        $stmt = $conn->prepare("INSERT INTO productos (nombre_producto, descripcion, precio, stock, id_categoria, imagen_url) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdiss", $nombre_producto, $descripcion, $precio, $stock, $id_categoria, $imagen_url);

        if ($stmt->execute()) {
            $mensaje = "<div class='success-message'>Producto registrado exitosamente.</div>";
        } else {
            $mensaje = "<div class='error-message'>Error al registrar el producto: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }
}

// Cerrar la conexión al final del script
$conn->close();
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Producto</title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="manifest" href="../manifest.json">
    <script src="../main.js" defer></script>
    <style>
        /* Centrar todo */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f9; /* Fondo claro */
        }

        .login-page {
            width: 100%;
            max-width: 400px; /* Limita el ancho del contenedor */
            padding: 20px;
            background: rgba(255, 255, 255, 0.9); /* Fondo semitransparente */
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .login-container h2 {
            text-align: center; /* Centra el título */
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column; /* Hace que los elementos estén uno debajo del otro */
            gap: 15px; /* Espaciado entre los elementos */
        }

        form input, 
        form textarea, 
        form select, 
        form button {
            width: 100%; /* Ocupa todo el ancho disponible */
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        form textarea {
            height: 80px;
            resize: none; /* Desactiva el redimensionamiento manual */
        }

        form button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        form button:hover {
            background-color: #0056b3;
        }

        select option {
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="login-page">
        <div class="login-container">
            <h2>Registrar Producto</h2>
            
            <!-- Mostrar mensajes de éxito o error -->
            <?php if (!empty($mensaje)) echo $mensaje; ?>
            
            <!-- Formulario de registro -->
            <form action="" method="post" enctype="multipart/form-data">
                <input type="text" name="nombre_producto" placeholder="Nombre del producto" required>
                <textarea name="descripcion" placeholder="Descripción" required></textarea>
                <input type="number" name="precio" placeholder="Precio" step="0.01" required>
                <input type="number" name="stock" placeholder="Stock" required>
                <select name="id_categoria" required>
                    <option value="" disabled selected>Selecciona una categoría</option>
                    <option value="1">Categoría 1</option>
                    <option value="2">Categoría 2</option>
                </select>
                <input type="file" name="imagen" accept="image/*" required>
                <button type="submit">Registrar Producto</button>
            </form>
        </div>
    </div>
</body>
</html>
