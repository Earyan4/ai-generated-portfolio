<?php
require_once 'config/database.php';

class Project {
    private $conn;
    private $table_name = "projects";

    public $id;
    public $user_id;
    public $project_name;
    public $project_url;
    public $technologies;
    public $duration;
    public $description;
    public $project_image;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET user_id=:user_id, project_name=:project_name, project_url=:project_url, 
                      technologies=:technologies, duration=:duration, 
                      description=:description, project_image=:project_image";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":project_name", $this->project_name);
        $stmt->bindParam(":project_url", $this->project_url);
        $stmt->bindParam(":technologies", $this->technologies);
        $stmt->bindParam(":duration", $this->duration);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":project_image", $this->project_image);

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
