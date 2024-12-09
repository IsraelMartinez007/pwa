<?php
// Conexión a la base de datos
$servername = "localhost"; // Cambia según tu configuración
$username = "evnddata_juan"; // Cambia según tu configuración
$password = "XAqRt7x4@j9R6:"; // Cambia según tu configuración
$dbname = "avnddata_tienda_con_carrito"; // Nombre de la base de datos

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si se ha enviado un archivo de imagen
if(isset($_FILES['imagen']['name']) && $_FILES['imagen']['error'] == 0) {
    $imagen_tmp = $_FILES['imagen']['tmp_name'];
    $imagen_nombre = $_FILES['imagen']['name'];

    // Mover la imagen a la carpeta de destino (ajustar la ruta si es necesario)
    if(move_uploaded_file($imagen_tmp, "../img/" . $imagen_nombre)) {
        // Obtener los datos del formulario
        $nombre_producto = $_POST['nombre_producto'];
        $descripcion = $_POST['descripcion'];
        $precio = $_POST['precio'];

        // Insertar los datos en la base de datos, incluyendo el nombre de la imagen
        $sql = "INSERT INTO productos (nombre_producto, descripcion, precio, imagen_url) 
                VALUES ('$nombre_producto', '$descripcion', '$precio', '$imagen_nombre')";

        if ($conn->query($sql) === TRUE) {
            echo "Producto registrado exitosamente.";
        } else {
            echo "Error al registrar el producto: " . $conn->error;
        }
    } else {
        echo "Error al cargar la imagen.";
    }
} else {
    echo "No se ha seleccionado ninguna imagen.";
}

$conn->close();
?>
