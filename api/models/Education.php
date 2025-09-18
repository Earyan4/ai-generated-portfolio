<?php
require_once 'config/database.php';

class Education {
    private $conn;
    private $table_name = "education";

    public $id;
    public $user_id;
    public $degree;
    public $institution;
    public $start_date;
    public $end_date;
    public $grade;
    public $location;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET user_id=:user_id, degree=:degree, institution=:institution, 
                      start_date=:start_date, end_date=:end_date, 
                      grade=:grade, location=:location";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":degree", $this->degree);
        $stmt->bindParam(":institution", $this->institution);
        $stmt->bindParam(":start_date", $this->start_date);
        $stmt->bindParam(":end_date", $this->end_date);
        $stmt->bindParam(":grade", $this->grade);
        $stmt->bindParam(":location", $this->location);

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
