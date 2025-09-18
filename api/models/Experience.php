<?php
require_once 'config/database.php';

class Experience {
    private $conn;
    private $table_name = "experience";

    public $id;
    public $user_id;
    public $job_title;
    public $company;
    public $start_date;
    public $end_date;
    public $is_current;
    public $description;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET user_id=:user_id, job_title=:job_title, company=:company, 
                      start_date=:start_date, end_date=:end_date, 
                      is_current=:is_current, description=:description";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":job_title", $this->job_title);
        $stmt->bindParam(":company", $this->company);
        $stmt->bindParam(":start_date", $this->start_date);
        $stmt->bindParam(":end_date", $this->end_date);
        $stmt->bindParam(":is_current", $this->is_current);
        $stmt->bindParam(":description", $this->description);

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
