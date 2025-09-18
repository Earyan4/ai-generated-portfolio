<?php
// MySQL (existing) connection used by current models
class Database {
    private $host = "localhost";
    private $db_name = "portfolio_system";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Connection error (MySQL): " . $exception->getMessage();
        }
        return $this->conn;
    }
}

// MongoDB connection for migration (non-breaking addition)
// Requires PHP MongoDB driver and composer package mongodb/mongodb
// vendor/autoload.php should exist if using composer
class MongoConnection {
    private $uri = "mongodb://localhost:27017";
    private $db_name = "portfolio_system";

    public function getDatabase() {
        // Attempt to load Composer autoload if present
        $autoloadPath = __DIR__ . '/../../vendor/autoload.php';
        if (file_exists($autoloadPath)) {
            require_once $autoloadPath;
        }

        try {
            // Prefer library client if available
            if (class_exists('MongoDB\\Client')) {
                $client = new MongoDB\Client($this->uri);
                return $client->selectDatabase($this->db_name);
            }

            // Fallback to ext-mongodb Manager if library not installed
            if (class_exists('MongoDB\\Driver\\Manager')) {
                $manager = new MongoDB\Driver\Manager($this->uri);
                // Provide a lightweight wrapper exposing db name and manager
                return (object) [
                    'manager' => $manager,
                    'db' => $this->db_name
                ];
            }

            throw new RuntimeException('MongoDB client not found. Install ext-mongodb and composer package mongodb/mongodb.');
        } catch (Throwable $e) {
            echo 'Connection error (MongoDB): ' . $e->getMessage();
            return null;
        }
    }
}
?>
