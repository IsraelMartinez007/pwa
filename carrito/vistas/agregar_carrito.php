<?php
session_start();
require_once '../model/conexion.php'; // Cambia a tu ruta correcta

// Crear una instancia de la clase Conexion
$conexion = new Conexion();
$conn = $conexion->conectar();

if (!$conn) {
    die(json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos.'])); // Error de conexión
}

// Verificar si el usuario está logueado
$id_usuario = $_SESSION['id_usuario'] ?? null;
if (!$id_usuario) {
    die(json_encode(['success' => false, 'message' => 'Debes iniciar sesión para agregar productos al carrito.'])); // Si no está logueado
}

// Obtener los datos del producto desde la solicitud POST
$id_producto = $_POST['id_producto'] ?? null;
$cantidad = $_POST['cantidad'] ?? 1; // Establece una cantidad predeterminada

// Validar los datos
if (!$id_producto || !is_numeric($cantidad) || $cantidad < 1) {
    die(json_encode(['success' => false, 'message' => 'Datos inválidos.'])); // Datos inválidos
}

// Consultar el precio del producto
$sql_producto = "SELECT precio FROM productos WHERE id_producto = :id_producto";
$stmt_producto = $conn->prepare($sql_producto);
$stmt_producto->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
$stmt_producto->execute();
$producto = $stmt_producto->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
    die(json_encode(['success' => false, 'message' => 'Producto no encontrado.'])); // Si no se encuentra el producto
}

// Verificar si el producto ya está en el carrito del usuario
$sql_carrito = "SELECT cantidad FROM carrito WHERE id_usuario = :id_usuario AND id_producto = :id_producto";
$stmt_carrito = $conn->prepare($sql_carrito);
$stmt_carrito->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$stmt_carrito->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
$stmt_carrito->execute();
$producto_en_carrito = $stmt_carrito->fetch(PDO::FETCH_ASSOC);

if ($producto_en_carrito) {
    // Si el producto ya está en el carrito, se actualiza la cantidad
    $nueva_cantidad = $producto_en_carrito['cantidad'] + $cantidad;
    $sql_actualizar = "UPDATE carrito SET cantidad = :cantidad WHERE id_usuario = :id_usuario AND id_producto = :id_producto";
    $stmt_actualizar = $conn->prepare($sql_actualizar);
    $stmt_actualizar->bindParam(':cantidad', $nueva_cantidad, PDO::PARAM_INT);
    $stmt_actualizar->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt_actualizar->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);

    if ($stmt_actualizar->execute()) {
        echo json_encode(['success' => true, 'message' => 'Cantidad actualizada en el carrito.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar la cantidad en el carrito.']);
    }
} else {
    // Si el producto no está en el carrito, se agrega
    $sql_insert = "INSERT INTO carrito (id_usuario, id_producto, cantidad, precio, fecha_agregada) 
                   VALUES (:id_usuario, :id_producto, :cantidad, :precio, NOW())";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt_insert->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
    $stmt_insert->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
    $stmt_insert->bindParam(':precio', $producto['precio'], PDO::PARAM_STR);

    if ($stmt_insert->execute()) {
        echo json_encode(['success' => true, 'message' => 'Producto agregado al carrito.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al agregar producto al carrito.']);
    }
}
?>
