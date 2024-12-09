<?php
// Iniciar sesión
session_start();

// Conexión a la base de datos
$servername = "localhost"; // Cambia según tu configuración
$username = "evnddata_juan"; // Cambia según tu configuración
$password = "XAqRt7x4@j9R6:"; // Cambia según tu configuración
$dbname = "evnddata_tienda_con_carrito"; // Nombre de la base de datos

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si el usuario está logueado, de lo contrario mostrar 'Invitado'
$nombre_usuario = "Invitado";
if (isset($_SESSION['id_usuario'])) {
    // Obtener el nombre del usuario desde la base de datos
    $id_usuario = $_SESSION['id_usuario']; // Suponiendo que el ID de usuario está en la sesión
    $sql_usuario = "SELECT nombre_usuario FROM usuarios WHERE id_usuario = ?";
    $stmt = $conn->prepare($sql_usuario);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result_usuario = $stmt->get_result();

    if ($result_usuario->num_rows > 0) {
        // Obtener el nombre del usuario
        $row_usuario = $result_usuario->fetch_assoc();
        $nombre_usuario = htmlspecialchars($row_usuario['nombre_usuario']); // Escapar el nombre de usuario
    }
}

// Realizar la consulta a la base de datos para obtener los productos
$sql = "SELECT id_producto, nombre_producto, descripcion, precio, stock, id_categoria, imagen_url FROM productos ORDER BY id_producto DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú de Productos</title>
    <!-- Vincular el archivo CSS -->
    <link rel="stylesheet" href="../style/style.css">
    <link rel="manifest" href="../manifest.json">
    <script src="../main.js" defer></script>
    <style>
        /* Estilo para la caja del producto */
        .product-box {
            position: relative;
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
    </style>
</head>
<body>
    <div class="page-container">
        <header>
            <div class="logo">
                <img src="../img/logo.png" alt="Logo">
            </div>
            <div class="welcome-message">
                <p>Hola, <?php echo $nombre_usuario; ?>!</p>
            </div>
            <div class="buttons">
                <button onclick="window.location.href='carrito.php'">Ir al Carrito</button>
                <?php if (isset($_SESSION['id_usuario'])): ?>
                    <button onclick="window.location.href='logout.php'">Cerrar Sesión</button>
                <?php endif; ?>
            </div>
        </header>

        <main>
            <section class="product-container">
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo '<div class="product-box">';
                        echo '<img src="' . $row["imagen_url"] . '" alt="' . $row["nombre_producto"] . '" />';
                        echo '<div class="product-details">';
                        echo '<p><strong>' . $row["nombre_producto"] . '</strong></p>';
                        echo '<p><strong>$' . $row["precio"] . '</strong></p>';
                        echo '</div>';
                        echo '<button class="add-to-cart" onclick="agregarAlCarrito(' . $row["id_producto"] . ')">Agregar al carrito</button>';
                        echo '</div>';
                    }
                } else {
                    echo "<p>No hay productos disponibles.</p>";
                }
                $conn->close();
                ?>
            </section>
        </main>

        <footer>
            <p>&copy; 2024 Tienda EFECTIVA. Todos los derechos reservados</p>
        </footer>

        <script>
        function agregarAlCarrito(idProducto) {
            fetch('agregar_carrito.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    id_producto: idProducto,
                    cantidad: 1
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.href = 'carrito.php';
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ocurrió un error al agregar al carrito.');
            });
        }
        </script>
    </div>
</body>
</html>
