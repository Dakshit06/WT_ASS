<?php
class MentorMatcher {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function findMatches($mentee_id, $limit = 5) {
        // Get mentee data
        $mentee = $this->getMenteeData($mentee_id);
        $mentors = $this->getAvailableMentors();
        $matches = [];
        
        foreach ($mentors as $mentor) {
            $score = $this->calculateMatchScore($mentor, $mentee);
            $matches[] = [
                'mentor_id' => $mentor['id'],
                'score' => $score,
                'mentor_data' => $mentor
            ];
        }
        
        usort($matches, fn($a, $b) => $b['score'] - $a['score']);
        return array_slice($matches, 0, $limit);
    }
    
    private function calculateMatchScore($mentor, $mentee) {
        $score = 0;
        
        // Skills matching (35%)
        $mentorSkills = explode(',', strtolower($mentor['skills']));
        $menteeSkills = explode(',', strtolower($mentee['skills']));
        $commonSkills = array_intersect($mentorSkills, $menteeSkills);
        $skillScore = (count($commonSkills) / max(count($mentorSkills), count($menteeSkills))) * 35;
        $score += $skillScore;
        
        // Experience level match (25%)
        $experienceMatch = $this->calculateExperienceMatch($mentor['experience_level'], $mentee['desired_experience']);
        $score += $experienceMatch * 25;
        
        // Availability and schedule compatibility (20%)
        $availabilityScore = $this->calculateAvailabilityScore($mentor['availability'], $mentee['preferred_times']);
        $score += $availabilityScore * 20;
        
        // Personality and learning style match (20%)
        $personalityScore = $this->calculatePersonalityMatch($mentor['teaching_style'], $mentee['learning_style']);
        $score += $personalityScore * 20;
        
        return round($score, 2);
    }

    private function calculateExperienceMatch($mentor_level, $desired_level) {
        $levels = ['beginner' => 1, 'intermediate' => 2, 'advanced' => 3, 'expert' => 4];
        $mentor_value = $levels[$mentor_level] ?? 2;
        $desired_value = $levels[$desired_level] ?? 2;
        return 1 - (abs($mentor_value - $desired_value) / 3);
    }

    private function calculatePersonalityMatch($teaching_style, $learning_style) {
        $styles = [
            'visual' => ['visual' => 1.0, 'auditory' => 0.6, 'kinesthetic' => 0.4],
            'auditory' => ['visual' => 0.6, 'auditory' => 1.0, 'kinesthetic' => 0.5],
            'kinesthetic' => ['visual' => 0.4, 'auditory' => 0.5, 'kinesthetic' => 1.0]
        ];
        return $styles[$teaching_style][$learning_style] ?? 0.5;
    }
    
    private function getMenteeData($mentee_id) {
        $stmt = $this->conn->prepare("
            SELECT u.*, 
                   GROUP_CONCAT(c.title) as certifications,
                   COUNT(DISTINCT lp.id) as completed_pathways,
                   AVG(f.rating) as avg_engagement
            FROM users u
            LEFT JOIN certifications c ON u.id = c.user_id
            LEFT JOIN learning_paths lp ON u.id = lp.mentee_id
            LEFT JOIN feedback f ON u.id = f.mentee_id
            WHERE u.id = ?
            GROUP BY u.id");
        $stmt->bind_param("i", $mentee_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    private function getAvailableMentors() {
        return $this->conn->query("
            SELECT u.*, 
                   COUNT(DISTINCT m.id) as active_mentees,
                   AVG(f.rating) as mentor_rating
            FROM users u
            LEFT JOIN matches m ON u.id = m.mentor_id
            LEFT JOIN feedback f ON u.id = f.mentor_id
            WHERE u.role = 'mentor'
            GROUP BY u.id
            HAVING active_mentees < 5")->fetch_all(MYSQLI_ASSOC);
    }
}
?>
