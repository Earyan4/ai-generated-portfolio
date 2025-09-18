<?php
require_once '../models/User.php';
require_once '../models/UserMongo.php';
require_once '../models/Skill.php';
require_once '../models/Experience.php';
require_once '../models/Education.php';
require_once '../models/Project.php';

class UserController {
    private $user;
    private $skill;
    private $experience;
    private $education;
    private $project;
    private $userMongo; // optional

    public function __construct($db, $mongoDb = null) {
        $this->user = new User($db);
        $this->skill = new Skill($db);
        $this->experience = new Experience($db);
        $this->education = new Education($db);
        $this->project = new Project($db);

        if ($mongoDb) {
            try {
                $this->userMongo = new UserMongo($mongoDb);
            } catch (Exception $e) {
                $this->userMongo = null;
            }
        }
    }

    // Register new user
    public function register($data) {
        try {
            if ($this->userMongo) {
                $existingUser = $this->userMongo->getByEmail($data['email']);
                if ($existingUser) {
                    return ['success' => false, 'message' => 'Email already exists'];
                }

                $doc = $data;
                $doc['password'] = (new UserMongo(null))->hashPassword($data['password']);
                // Generate numeric id if not provided
                $newId = isset($data['id']) ? (int)$data['id'] : (int)round(microtime(true));
                $this->userMongo->upsertUserProfile($newId, $doc);
                return ['success' => true, 'user_id' => $newId, 'message' => 'User registered successfully'];
            }
            // Check if email already exists
            $existingUser = $this->user->getByEmail($data['email']);
            if ($existingUser) {
                return ['success' => false, 'message' => 'Email already exists'];
            }

            // Set user properties
            $this->user->full_name = $data['full_name'];
            $this->user->email = $data['email'];
            $this->user->password = $data['password'];
            $this->user->profession = $data['profession'];
            $this->user->phone = $data['phone'] ?? '';
            $this->user->location = $data['location'] ?? '';
            $this->user->website = $data['website'] ?? '';
            $this->user->profile_photo = $data['profile_photo'] ?? '';
            $this->user->summary = $data['summary'] ?? '';

            $userId = $this->user->create();
            if ($userId) {
                return ['success' => true, 'user_id' => $userId, 'message' => 'User registered successfully'];
            }
            return ['success' => false, 'message' => 'Failed to register user'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Login user
    public function login($email, $password) {
        try {
            if ($this->userMongo) {
                $user = $this->userMongo->getByEmail($email);
                if ($user && $this->userMongo->verifyPassword($password, $user['password'])) {
                    unset($user['password']);
                    return ['success' => true, 'user' => $user];
                }
                return ['success' => false, 'message' => 'Invalid credentials'];
            }
            $user = $this->user->getByEmail($email);
            if ($user && $this->user->verifyPassword($password, $user['password'])) {
                unset($user['password']); // Remove password from response
                return ['success' => true, 'user' => $user];
            }
            return ['success' => false, 'message' => 'Invalid credentials'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Get user profile
    public function getProfile($userId) {
        try {
            $profile = $this->userMongo ? $this->userMongo->getCompleteProfile($userId) : $this->user->getCompleteProfile($userId);
            if ($profile) {
                unset($profile['password']); // Remove password from response
                return ['success' => true, 'profile' => $profile];
            }
            return ['success' => false, 'message' => 'User not found'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Update user profile
    public function updateProfile($userId, $data) {
        try {
            $this->user->id = $userId;
            $this->user->full_name = $data['full_name'];
            $this->user->profession = $data['profession'];
            $this->user->phone = $data['phone'] ?? '';
            $this->user->location = $data['location'] ?? '';
            $this->user->website = $data['website'] ?? '';
            $this->user->profile_photo = $data['profile_photo'] ?? '';
            $this->user->summary = $data['summary'] ?? '';

            if ($this->user->update()) {
                return ['success' => true, 'message' => 'Profile updated successfully'];
            }
            return ['success' => false, 'message' => 'Failed to update profile'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Save complete profile data
    public function saveCompleteProfile($userId, $data) {
        try {
            if ($this->userMongo) {
                $this->userMongo->upsertUserProfile($userId, $data);
                return ['success' => true, 'message' => 'Profile saved successfully'];
            }
            // Update basic user info
            $this->updateProfile($userId, $data);

            // Save skills - handle the new structure
            if (isset($data['skills'])) {
                $this->skill->deleteByUserId($userId);
                
                // Handle skills grouped by type
                foreach ($data['skills'] as $skillType => $skills) {
                    if (is_array($skills)) {
                        foreach ($skills as $skill) {
                            $this->skill->user_id = $userId;
                            $this->skill->skill_name = $skill['name'];
                            $this->skill->skill_type = $skill['type'];
                            $this->skill->proficiency_level = $skill['level'] ?? 50;
                            $this->skill->create();
                        }
                    }
                }
            }

            // Save experience
            if (isset($data['experience'])) {
                $this->experience->deleteByUserId($userId);
                foreach ($data['experience'] as $exp) {
                    $this->experience->user_id = $userId;
                    $this->experience->job_title = $exp['title'];
                    $this->experience->company = $exp['company'];
                    $this->experience->start_date = $exp['start_date'];
                    $this->experience->end_date = $exp['end_date'];
                    $this->experience->is_current = $exp['is_current'] ?? false;
                    $this->experience->description = $exp['description'];
                    $this->experience->create();
                }
            }

            // Save education
            if (isset($data['education'])) {
                $this->education->deleteByUserId($userId);
                foreach ($data['education'] as $edu) {
                    $this->education->user_id = $userId;
                    $this->education->degree = $edu['degree'];
                    $this->education->institution = $edu['institution'];
                    $this->education->start_date = $edu['start_date'];
                    $this->education->end_date = $edu['end_date'];
                    $this->education->grade = $edu['grade'];
                    $this->education->location = $edu['location'];
                    $this->education->create();
                }
            }

            // Save projects
            if (isset($data['projects'])) {
                $this->project->deleteByUserId($userId);
                foreach ($data['projects'] as $proj) {
                    $this->project->user_id = $userId;
                    $this->project->project_name = $proj['name'];
                    $this->project->project_url = $proj['url'];
                    $this->project->technologies = $proj['technologies'];
                    $this->project->duration = $proj['duration'];
                    $this->project->description = $proj['description'];
                    $this->project->project_image = $proj['image'];
                    $this->project->create();
                }
            }

            return ['success' => true, 'message' => 'Profile saved successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
?>
