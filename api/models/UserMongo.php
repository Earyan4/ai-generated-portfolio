<?php
require_once __DIR__ . '/../config/database.php';

class UserMongo {
    private $db; // MongoDB\Database or fallback object { manager, db }
    private $collectionName = 'users';

    public function __construct($mongoDb) {
        $this->db = $mongoDb;
    }

    private function getCollection() {
        if ($this->db instanceof MongoDB\Database) {
            return $this->db->selectCollection($this->collectionName);
        }
        throw new RuntimeException('MongoDB collection access requires mongodb/mongodb library');
    }

    public function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public function verifyPassword($password, $hashedPassword) {
        return password_verify($password, $hashedPassword);
    }

    public function getByEmail($email) {
        $col = $this->getCollection();
        return $col->findOne(['email' => $email]);
    }

    public function getById($id) {
        $col = $this->getCollection();
        // ids in this app are numeric (user_id). Store as integer key `id`
        return $col->findOne(['id' => (int)$id]);
    }

    public function upsertUserProfile($id, array $data) {
        $col = $this->getCollection();
        $now = new MongoDB\BSON\UTCDateTime((int)(microtime(true) * 1000));

        $doc = [
            'id' => (int)$id,
            'full_name' => $data['full_name'] ?? '',
            'email' => $data['email'] ?? '',
            'password' => $data['password'] ?? null,
            'profession' => $data['profession'] ?? '',
            'phone' => $data['phone'] ?? '',
            'location' => $data['location'] ?? '',
            'website' => $data['website'] ?? '',
            'profile_photo' => $data['profile_photo'] ?? '',
            'summary' => $data['summary'] ?? '',
            'skills' => $data['skills'] ?? [],
            'experience' => $data['experience'] ?? [],
            'education' => $data['education'] ?? [],
            'projects' => $data['projects'] ?? [],
            'updated_at' => $now,
        ];

        if (!$this->getById($id)) {
            $doc['created_at'] = $now;
        }

        $col->updateOne(
            ['id' => (int)$id],
            ['$set' => $doc],
            ['upsert' => true]
        );

        return true;
    }

    public function getCompleteProfile($id) {
        $doc = $this->getById($id);
        if (!$doc) return null;
        // Normalize MongoDB\Model\BSONDocument to array
        $profile = json_decode(json_encode($doc), true);
        return $profile;
    }
}
?>


