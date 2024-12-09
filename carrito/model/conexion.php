<?php
class Conexion {
    private $host = 'localhost'; // Cambiar según sea necesario
    private $db = 'evnddata_tienda_con_carrito'; // Cambiar según sea necesario
    private $user = 'evnddata_juan'; // Cambiar según sea necesario
    private $pass = 'XAqRt7x4@j9R6:'; // Cambiar según sea necesario
    private $charset = 'utf8mb4';
    private $conn = null;

    public function conectar() {
        try {
            // Usando PDO para la conexión
            $dsn = "mysql:host={$this->host};dbname={$this->db};charset={$this->charset}";
            $this->conn = new PDO($dsn, $this->user, $this->pass);
            // Configurar el PDO para manejar excepciones y establecer el modo de error
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        } catch (PDOException $e) {
            // Si hay un error al conectar, lo lanzamos
            die("Error de conexión: " . $e->getMessage());
        }
    }
}
?>

