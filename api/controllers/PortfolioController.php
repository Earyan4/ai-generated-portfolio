<?php
require_once '../models/User.php';
require_once '../models/PortfolioTemplate.php';

class PortfolioController {
    private $user;
    private $template;

    public function __construct($db) {
        $this->user = new User($db);
        $this->template = new PortfolioTemplate($db);
    }

    // Generate portfolio HTML
    public function generatePortfolio($userId, $templateType = 'default') {
        try {
            $profile = $this->user->getCompleteProfile($userId);
            if (!$profile) {
                return ['success' => false, 'message' => 'User not found'];
            }

            $template = $this->template->getByProfession($profile['profession']);
            if (!$template) {
                $template = $this->template->getDefault();
            }

            $html = $this->buildPortfolioHTML($profile, $template);
            
            return [
                'success' => true, 
                'html' => $html,
                'profile' => $profile
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Get portfolio data
    public function getPortfolio($userId) {
        try {
            $profile = $this->user->getCompleteProfile($userId);
            if (!$profile) {
                return ['success' => false, 'message' => 'User not found'];
            }

            $template = $this->template->getByProfession($profile['profession']);
            if (!$template) {
                $template = $this->template->getDefault();
            }

            return [
                'success' => true,
                'profile' => $profile,
                'template' => $template
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Get available templates
    public function getTemplates() {
        try {
            $templates = $this->template->getAll();
            return ['success' => true, 'templates' => $templates];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // Build portfolio HTML based on profession
    private function buildPortfolioHTML($profile, $template) {
        $profession = $profile['profession'];
        
        switch ($profession) {
            case 'developer':
                return $this->buildDeveloperPortfolio($profile, $template);
            case 'doctor':
                return $this->buildDoctorPortfolio($profile, $template);
            case 'photographer':
                return $this->buildPhotographerPortfolio($profile, $template);
            case 'video_editor':
                return $this->buildVideoEditorPortfolio($profile, $template);
            case 'marketing':
                return $this->buildMarketingPortfolio($profile, $template);
            case 'designer':
                return $this->buildDesignerPortfolio($profile, $template);
            case 'writer':
                return $this->buildWriterPortfolio($profile, $template);
            case 'consultant':
                return $this->buildConsultantPortfolio($profile, $template);
            default:
                return $this->buildDefaultPortfolio($profile, $template);
        }
    }

    // Developer Portfolio Template
    private function buildDeveloperPortfolio($profile, $template) {
        $skills = $this->groupSkillsByType($profile['skills']);
        
        return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . htmlspecialchars($profile['full_name']) . ' - Developer Portfolio</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        .hero { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 100px 0; text-align: center; }
        .hero h1 { font-size: 3.5rem; margin-bottom: 1rem; }
        .hero p { font-size: 1.3rem; margin-bottom: 2rem; }
        .section { padding: 80px 0; }
        .section h2 { text-align: center; margin-bottom: 3rem; font-size: 2.5rem; color: #333; }
        .skills-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; }
        .skill-category h3 { color: #667eea; margin-bottom: 1rem; }
        .skill-tag { display: inline-block; background: #667eea; color: white; padding: 8px 16px; margin: 5px; border-radius: 20px; font-size: 0.9rem; }
        .experience-item { border-left: 3px solid #667eea; padding-left: 20px; margin-bottom: 2rem; }
        .project-card { background: #f8f9fa; padding: 2rem; border-radius: 10px; margin-bottom: 2rem; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .contact-info { background: #f8f9fa; padding: 2rem; border-radius: 10px; text-align: center; }
    </style>
</head>
<body>
    <section class="hero">
        <div class="container">
            ' . ($profile['profile_photo'] ? '<img src="' . htmlspecialchars($profile['profile_photo']) . '" alt="Profile Photo" style="width: 200px; height: 200px; border-radius: 50%; object-fit: cover; margin-bottom: 2rem; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">' : '') . '
            <h1>' . htmlspecialchars($profile['full_name']) . '</h1>
            <p>' . htmlspecialchars($profile['summary']) . '</p>
            <div class="contact-info">
                <p><i class="fas fa-envelope"></i> ' . htmlspecialchars($profile['email']) . '</p>
                <p><i class="fas fa-phone"></i> ' . htmlspecialchars($profile['phone']) . '</p>
                <p><i class="fas fa-map-marker-alt"></i> ' . htmlspecialchars($profile['location']) . '</p>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <h2>Skills & Technologies</h2>
            <div class="skills-grid">
                <div class="skill-category">
                    <h3>Technical Skills</h3>
                    ' . $this->renderSkills($skills['technical']) . '
                </div>
                <div class="skill-category">
                    <h3>Tools & Technologies</h3>
                    ' . $this->renderSkills($skills['tools']) . '
                </div>
                <div class="skill-category">
                    <h3>Soft Skills</h3>
                    ' . $this->renderSkills($skills['soft']) . '
                </div>
            </div>
        </div>
    </section>

    <section class="section" style="background: #f8f9fa;">
        <div class="container">
            <h2>Professional Experience</h2>
            ' . $this->renderExperience($profile['experience']) . '
        </div>
    </section>

    <section class="section">
        <div class="container">
            <h2>Projects</h2>
            ' . $this->renderProjects($profile['projects']) . '
        </div>
    </section>

    <section class="section" style="background: #f8f9fa;">
        <div class="container">
            <h2>Education</h2>
            ' . $this->renderEducation($profile['education']) . '
        </div>
    </section>
</body>
</html>';
    }

    // Doctor Portfolio Template
    private function buildDoctorPortfolio($profile, $template) {
        return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dr. ' . htmlspecialchars($profile['full_name']) . ' - Medical Professional</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: "Georgia", serif; line-height: 1.6; color: #333; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        .hero { background: linear-gradient(135deg, #2c5aa0 0%, #1e3a8a 100%); color: white; padding: 100px 0; text-align: center; }
        .hero h1 { font-size: 3.5rem; margin-bottom: 1rem; }
        .hero p { font-size: 1.3rem; margin-bottom: 2rem; }
        .section { padding: 80px 0; }
        .section h2 { text-align: center; margin-bottom: 3rem; font-size: 2.5rem; color: #2c5aa0; }
        .specialization { background: #f0f8ff; padding: 2rem; border-radius: 10px; margin-bottom: 2rem; border-left: 5px solid #2c5aa0; }
        .education-item { background: white; padding: 2rem; border-radius: 10px; margin-bottom: 2rem; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .contact-info { background: #f0f8ff; padding: 2rem; border-radius: 10px; text-align: center; }
    </style>
</head>
<body>
    <section class="hero">
        <div class="container">
            <h1>Dr. ' . htmlspecialchars($profile['full_name']) . '</h1>
            <p>' . htmlspecialchars($profile['summary']) . '</p>
            <div class="contact-info">
                <p><i class="fas fa-envelope"></i> ' . htmlspecialchars($profile['email']) . '</p>
                <p><i class="fas fa-phone"></i> ' . htmlspecialchars($profile['phone']) . '</p>
                <p><i class="fas fa-map-marker-alt"></i> ' . htmlspecialchars($profile['location']) . '</p>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <h2>Specializations</h2>
            ' . $this->renderSkills($profile['skills']) . '
        </div>
    </section>

    <section class="section" style="background: #f8f9fa;">
        <div class="container">
            <h2>Education & Training</h2>
            ' . $this->renderEducation($profile['education']) . '
        </div>
    </section>

    <section class="section">
        <div class="container">
            <h2>Professional Experience</h2>
            ' . $this->renderExperience($profile['experience']) . '
        </div>
    </section>
</body>
</html>';
    }

    // Photographer Portfolio Template
    private function buildPhotographerPortfolio($profile, $template) {
        return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . htmlspecialchars($profile['full_name']) . ' - Photographer</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: "Helvetica Neue", Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        .hero { background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%); color: white; padding: 100px 0; text-align: center; }
        .hero h1 { font-size: 3.5rem; margin-bottom: 1rem; }
        .hero p { font-size: 1.3rem; margin-bottom: 2rem; }
        .section { padding: 80px 0; }
        .section h2 { text-align: center; margin-bottom: 3rem; font-size: 2.5rem; color: #333; }
        .gallery { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; }
        .gallery-item { position: relative; overflow: hidden; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .gallery-item img { width: 100%; height: 300px; object-fit: cover; transition: transform 0.3s; }
        .gallery-item:hover img { transform: scale(1.05); }
        .contact-info { background: #f8f9fa; padding: 2rem; border-radius: 10px; text-align: center; }
    </style>
</head>
<body>
    <section class="hero">
        <div class="container">
            <h1>' . htmlspecialchars($profile['full_name']) . '</h1>
            <p>' . htmlspecialchars($profile['summary']) . '</p>
            <div class="contact-info">
                <p><i class="fas fa-envelope"></i> ' . htmlspecialchars($profile['email']) . '</p>
                <p><i class="fas fa-phone"></i> ' . htmlspecialchars($profile['phone']) . '</p>
                <p><i class="fas fa-map-marker-alt"></i> ' . htmlspecialchars($profile['location']) . '</p>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <h2>Portfolio Gallery</h2>
            <div class="gallery">
                ' . $this->renderProjectGallery($profile['projects']) . '
            </div>
        </div>
    </section>

    <section class="section" style="background: #f8f9fa;">
        <div class="container">
            <h2>Services</h2>
            ' . $this->renderSkills($profile['skills']) . '
        </div>
    </section>
</body>
</html>';
    }

    // Helper methods
    private function groupSkillsByType($skills) {
        $grouped = ['technical' => [], 'soft' => [], 'tools' => []];
        foreach ($skills as $skill) {
            $grouped[$skill['skill_type']][] = $skill;
        }
        return $grouped;
    }

    private function renderSkills($skills) {
        if (empty($skills)) return '<p>No skills listed</p>';
        
        $html = '';
        foreach ($skills as $skill) {
            $html .= '<span class="skill-tag">' . htmlspecialchars($skill['skill_name']) . '</span>';
        }
        return $html;
    }

    private function renderExperience($experience) {
        if (empty($experience)) return '<p>No experience listed</p>';
        
        $html = '';
        foreach ($experience as $exp) {
            $html .= '<div class="experience-item">
                <h3>' . htmlspecialchars($exp['job_title']) . '</h3>
                <h4>' . htmlspecialchars($exp['company']) . '</h4>
                <p><strong>Duration:</strong> ' . $exp['start_date'] . ' - ' . ($exp['end_date'] ?: 'Present') . '</p>
                <p>' . htmlspecialchars($exp['description']) . '</p>
            </div>';
        }
        return $html;
    }

    private function renderEducation($education) {
        if (empty($education)) return '<p>No education listed</p>';
        
        $html = '';
        foreach ($education as $edu) {
            $html .= '<div class="education-item">
                <h3>' . htmlspecialchars($edu['degree']) . '</h3>
                <h4>' . htmlspecialchars($edu['institution']) . '</h4>
                <p><strong>Duration:</strong> ' . $edu['start_date'] . ' - ' . $edu['end_date'] . '</p>
                <p><strong>Grade:</strong> ' . htmlspecialchars($edu['grade']) . '</p>
                <p><strong>Location:</strong> ' . htmlspecialchars($edu['location']) . '</p>
            </div>';
        }
        return $html;
    }

    private function renderProjects($projects) {
        if (empty($projects)) return '<p>No projects listed</p>';
        
        $html = '';
        foreach ($projects as $project) {
            $html .= '<div class="project-card">
                <h3>' . htmlspecialchars($project['project_name']) . '</h3>
                <p><strong>Technologies:</strong> ' . htmlspecialchars($project['technologies']) . '</p>
                <p><strong>Duration:</strong> ' . htmlspecialchars($project['duration']) . '</p>
                <p>' . htmlspecialchars($project['description']) . '</p>
                ' . ($project['project_url'] ? '<a href="' . htmlspecialchars($project['project_url']) . '" target="_blank">View Project</a>' : '') . '
            </div>';
        }
        return $html;
    }

    private function renderProjectGallery($projects) {
        if (empty($projects)) return '<p>No projects to display</p>';
        
        $html = '';
        foreach ($projects as $project) {
            $html .= '<div class="gallery-item">
                <img src="' . htmlspecialchars($project['project_image'] ?: 'https://via.placeholder.com/400x300') . '" alt="' . htmlspecialchars($project['project_name']) . '">
                <div style="padding: 1rem;">
                    <h3>' . htmlspecialchars($project['project_name']) . '</h3>
                    <p>' . htmlspecialchars($project['description']) . '</p>
                </div>
            </div>';
        }
        return $html;
    }

    // Additional portfolio templates for other professions...
    private function buildVideoEditorPortfolio($profile, $template) {
        // Similar structure with video-focused layout
        return $this->buildDefaultPortfolio($profile, $template);
    }

    private function buildMarketingPortfolio($profile, $template) {
        // Similar structure with marketing-focused layout
        return $this->buildDefaultPortfolio($profile, $template);
    }

    private function buildDesignerPortfolio($profile, $template) {
        // Similar structure with design-focused layout
        return $this->buildDefaultPortfolio($profile, $template);
    }

    private function buildWriterPortfolio($profile, $template) {
        // Similar structure with writing-focused layout
        return $this->buildDefaultPortfolio($profile, $template);
    }

    private function buildConsultantPortfolio($profile, $template) {
        // Similar structure with consulting-focused layout
        return $this->buildDefaultPortfolio($profile, $template);
    }

    private function buildDefaultPortfolio($profile, $template) {
        return $this->buildDeveloperPortfolio($profile, $template);
    }
}
?>
