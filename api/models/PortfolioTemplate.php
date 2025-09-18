<?php
require_once 'config/database.php';

class PortfolioTemplate {
    private $conn;
    private $table_name = "portfolio_templates";

    public $id;
    public $profession;
    public $template_name;
    public $template_data;
    public $is_active;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getByProfession($profession) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE profession = :profession AND is_active = 1 
                  ORDER BY id ASC LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":profession", $profession);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getDefault() {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE profession = 'developer' AND is_active = 1 
                  ORDER BY id ASC LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE is_active = 1 
                  ORDER BY profession, template_name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET profession=:profession, template_name=:template_name, 
                      template_data=:template_data, is_active=:is_active";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":profession", $this->profession);
        $stmt->bindParam(":template_name", $this->template_name);
        $stmt->bindParam(":template_data", $this->template_data);
        $stmt->bindParam(":is_active", $this->is_active);

        return $stmt->execute();
    }
}
?>
