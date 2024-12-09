<?php
session_start();
require_once '../model/conexion.php'; // Archivo para la conexión a la base de datos

// Crear una instancia de la clase Conexion
$conexion = new Conexion();
$conn = $conexion->conectar(); // Obtener la conexión PDO

// Verificar si la conexión fue exitosa
if ($conn === null) {
    die('Error de conexión a la base de datos.');
}

// Obtener el ID del usuario (supongamos que está almacenado en la sesión después del login)
$id_usuario = $_SESSION['id_usuario'] ?? null;

if (!$id_usuario) {
    die('Por favor, inicia sesión para ver tu carrito.');
}

// Verificar si se ha enviado el formulario de eliminación
if (isset($_POST['eliminar']) && isset($_POST['id_producto'])) {
    $id_producto = $_POST['id_producto'];

    // Preparar la consulta para eliminar el producto del carrito
    $sql_eliminar = "DELETE FROM carrito WHERE id_producto = :id_producto AND id_usuario = :id_usuario";
    $stmt_eliminar = $conn->prepare($sql_eliminar);
    $stmt_eliminar->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
    $stmt_eliminar->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);

    // Ejecutar la consulta
    if ($stmt_eliminar->execute()) {
        // Redirigir al carrito después de eliminar el producto
        header("Location: carrito.php");
        exit();
    } else {
        echo "Error al eliminar el producto.";
    }
}

// Obtener el nombre del usuario
$sql_usuario = "SELECT nombre_usuario FROM usuarios WHERE id_usuario = :id_usuario";
$stmt_usuario = $conn->prepare($sql_usuario);
$stmt_usuario->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$stmt_usuario->execute();
$nombre_usuario = $stmt_usuario->fetchColumn();

// Consultar los productos del carrito del usuario usando PDO
$sql = "SELECT p.id_producto, p.nombre_producto, c.cantidad, c.precio 
        FROM carrito c
        JOIN productos p ON c.id_producto = p.id_producto
        WHERE c.id_usuario = :id_usuario";
$stmt = $conn->prepare($sql); // Preparar la consulta
$stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT); // Vincular el parámetro
$stmt->execute(); // Ejecutar la consulta

$result = $stmt->fetchAll(PDO::FETCH_ASSOC); // Obtener los resultados

// Calcular el total y guardarlo en la sesión
$total = 0;

if (count($result) > 0) {
    foreach ($result as $row) {
        $subtotal = $row['cantidad'] * $row['precio'];
        $total += $subtotal;
    }
}

// Guardar el total en la sesión
$_SESSION['total_carrito'] = $total;
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <link rel="stylesheet" href="../style/style.css"> <!-- Archivo CSS -->
    <link rel="manifest" href="../manifest.json">
    <script src="../main.js" defer></script>
    <style>
       /* Estilos de la tabla */
table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
    background-color: white; /* Fondo blanco para la tabla */
    text-align: right; /* Centrar contenido */
}

table th, table td {
    padding: 15px; /* Incrementar el espacio interno para mejor legibilidad */
    text-align: center; /* Centrar el texto en celdas */
    border-bottom: 1px solid #ddd;
    font-size: 1em;
    color: #333;
    vertical-align: middle; /* Centrar verticalmente */
}

table th {
    background-color: #007bff;
    color: white;
    text-transform: uppercase; /* Estilizar encabezados */
    font-weight: bold;
}

table tr:nth-child(even) {
    background-color: #f9f9f9;
}

table tr:hover {
    background-color: #e9f0ff;
}

/* Estilo para el total */
.total {
    background-color: white; /* Fondo blanco */
    text-align: center; /* Centrar texto */
    font-size: 1.5em;
    margin-top: 20px;
    font-weight: bold;
    color: #333;
    padding: 15px; /* Espaciado interno */
    border: 1px solid #ddd; /* Borde para resaltar */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Sombra ligera */
    display: inline-block; /* Evita que ocupe todo el ancho */
}

/* Botones */
.actions {
    margin-top: 20px;
    display: flex;
    justify-content: center; /* Centrar los botones */
    gap: 10px;
}

.actions .btn {
    padding: 12px 25px;
    color: white;
    background-color: #007bff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    text-decoration: none;
    font-size: 1.1em;
    transition: background-color 0.3s;
    text-align: center;
}

.actions .btn:hover {
    background-color: #0056b3;
}

.actions .btn[style="background-color: #6c757d;"] {
    background-color: #6c757d;
}

.actions .btn[style="background-color: #6c757d;"]:hover {
    background-color: #5a6268;
}

/* Pie de página */
footer {
    background-color: #333;
    color: white;
    text-align: center;
    padding: 20px;
    width: 100%;
    position: fixed;
    bottom: 0;
    left: 0;
}

footer p {
    margin: 0;
    font-size: 0.9em;
}

/* Media Queries para hacer la tabla responsiva */
@media (max-width: 768px) {
    table th, table td {
        font-size: 0.9em;
        padding: 10px;
    }

    table {
        display: block;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    table thead {
        display: none; /* Ocultar los encabezados en pantallas pequeñas */
    }

    table tr {
        display: block;
        border-bottom: 1px solid #ddd;
        margin-bottom: 10px;
    }

    table td {
        display: inline;
        text-align: right;
        padding: 10px;
        border: none;
        position: relative;
    }

    table td:before {
        content: attr(data-label);
        font-weight: bold;
        position: absolute;
        left: 10px;
        top: 10px;
    }

    .total {
        font-size: 1.2em;
    }

    .actions .btn {
        width: 100%;
        margin-bottom: 10px;
    }
}

/* Mejoras adicionales en móviles */
@media (max-width: 480px) {
    .page-container {
        padding: 15px;
    }

    .logo img {
        height: 40px;
    }

    h1 {
        font-size: 1.5em;
    }

    .actions .btn {
        font-size: 1em;
    }

    footer p {
        font-size: 0.8em;
    }
}

/* Estilo para la barra de navegación */
header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 3px;
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

/* Botones del navbar */
.buttons button {
    margin: 0 5px;
    padding: 5px;
    background-color: #ff5c5c;
    color: white;
    border: none;
    cursor: pointer;
}

    </style>
</head>
<body>
    <div class="page-container">
        <!-- Cabecera -->
        <header>
            <div class="logo">
                <img src="../img/logo.png" alt="Logo">
            </div>
            <div class="welcome-message">
                <p>Hola, <?php echo htmlspecialchars($nombre_usuario); ?>!</p>
            </div>
            <div class="buttons">
                <button onclick="window.location.href='index.php'">Cerrar Sesión</button>
            </div>
        </header>

        <!-- Contenedor de carrito -->
        <main>
            <div class="container">
                <h1>Tu Carrito de Compras</h1>
                <table>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Subtotal</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total = 0;

                        if (count($result) > 0) {
                            foreach ($result as $row) {
                                $subtotal = $row['cantidad'] * $row['precio'];
                                $total += $subtotal;
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['nombre_producto']); ?></td>
                                    <td><?php echo $row['cantidad']; ?></td>
                                    <td>$<?php echo number_format($row['precio'], 2); ?></td>
                                    <td>$<?php echo number_format($subtotal, 2); ?></td>
                                    <td>
                                        <!-- Formulario para eliminar el producto -->
                                        <form action="carrito.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="id_producto" value="<?php echo $row['id_producto']; ?>">
                                            <button type="submit" name="eliminar" class="btn" style="background-color: #ff5c5c; cursor:pointer;">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan='5'>Tu carrito está vacío.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>

                <div class="total">
                    Total: $<?php echo number_format($total, 2); ?>
                </div>

                <div class="actions">
                    <a href="pago.php" class="btn">Proceder al pago</a>
                    <a href="menu.php" class="btn" style="background-color: #6c757d;">Seguir comprando</a>
                </div>
            </div>
        </main>
    </div>

    <!-- Pie de página -->
    <footer>
        <p>&copy; 2024 Tienda EFECTIVA. Todos los derechos reservados</p>
    </footer>
</body>
</html>










