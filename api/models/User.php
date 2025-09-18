<?php
require_once 'config/database.php';

class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $full_name;
    public $email;
    public $password;
    public $profession;
    public $phone;
    public $location;
    public $website;
    public $profile_photo;
    public $summary;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create user
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET full_name=:full_name, email=:email, password=:password, 
                      profession=:profession, phone=:phone, location=:location, 
                      website=:website, profile_photo=:profile_photo, summary=:summary";

        $stmt = $this->conn->prepare($query);

        // Hash password
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);

        // Bind values
        $stmt->bindParam(":full_name", $this->full_name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":profession", $this->profession);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":location", $this->location);
        $stmt->bindParam(":website", $this->website);
        $stmt->bindParam(":profile_photo", $this->profile_photo);
        $stmt->bindParam(":summary", $this->summary);

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Get user by email
    public function getByEmail($email) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get user by ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update user
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET full_name=:full_name, profession=:profession, phone=:phone, 
                      location=:location, website=:website, profile_photo=:profile_photo, 
                      summary=:summary 
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":full_name", $this->full_name);
        $stmt->bindParam(":profession", $this->profession);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":location", $this->location);
        $stmt->bindParam(":website", $this->website);
        $stmt->bindParam(":profile_photo", $this->profile_photo);
        $stmt->bindParam(":summary", $this->summary);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }

    // Verify password
    public function verifyPassword($password, $hashedPassword) {
        return password_verify($password, $hashedPassword);
    }

    // Get complete user profile with all related data
    public function getCompleteProfile($id) {
        $user = $this->getById($id);
        if (!$user) return null;

        // Get skills
        $skillsQuery = "SELECT * FROM skills WHERE user_id = :user_id";
        $skillsStmt = $this->conn->prepare($skillsQuery);
        $skillsStmt->bindParam(":user_id", $id);
        $skillsStmt->execute();
        $user['skills'] = $skillsStmt->fetchAll(PDO::FETCH_ASSOC);

        // Get experience
        $expQuery = "SELECT * FROM experience WHERE user_id = :user_id ORDER BY start_date DESC";
        $expStmt = $this->conn->prepare($expQuery);
        $expStmt->bindParam(":user_id", $id);
        $expStmt->execute();
        $user['experience'] = $expStmt->fetchAll(PDO::FETCH_ASSOC);

        // Get education
        $eduQuery = "SELECT * FROM education WHERE user_id = :user_id ORDER BY start_date DESC";
        $eduStmt = $this->conn->prepare($eduQuery);
        $eduStmt->bindParam(":user_id", $id);
        $eduStmt->execute();
        $user['education'] = $eduStmt->fetchAll(PDO::FETCH_ASSOC);

        // Get projects
        $projQuery = "SELECT * FROM projects WHERE user_id = :user_id ORDER BY id DESC";
        $projStmt = $this->conn->prepare($projQuery);
        $projStmt->bindParam(":user_id", $id);
        $projStmt->execute();
        $user['projects'] = $projStmt->fetchAll(PDO::FETCH_ASSOC);

        return $user;
    }
}
?>
