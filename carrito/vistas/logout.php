<?php
// Iniciar sesión
session_start();

// Destruir la sesión
session_destroy();

// Redirigir al usuario al archivo index.php
header("Location:index.php");
exit();
?>
