<?php
require_once 'config/database.php';

class Skill {
    private $conn;
    private $table_name = "skills";

    public $id;
    public $user_id;
    public $skill_name;
    public $skill_type;
    public $proficiency_level;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET user_id=:user_id, skill_name=:skill_name, 
                      skill_type=:skill_type, proficiency_level=:proficiency_level";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":skill_name", $this->skill_name);
        $stmt->bindParam(":skill_type", $this->skill_type);
        $stmt->bindParam(":proficiency_level", $this->proficiency_level);

        return $stmt->execute();
    }

    public function deleteByUserId($user_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        return $stmt->execute();
    }
}
?>
