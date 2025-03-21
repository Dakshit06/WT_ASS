<?php
function calculateCompatibilityScore($mentor, $mentee) {
    $score = 0;
    
    // Skills matching
    $mentorSkills = array_map('trim', explode(',', strtolower($mentor['skills'])));
    $menteeSkills = array_map('trim', explode(',', strtolower($mentee['skills'])));
    $commonSkills = array_intersect($mentorSkills, $menteeSkills);
    $score += count($commonSkills) * 2;
    
    // Department matching
    if ($mentor['department'] === $mentee['department']) {
        $score += 3;
    }
    
    // Availability matching
    if ($mentor['availability'] === $mentee['availability']) {
        $score += 2;
    }
    
    // Experience level consideration
    $mentorPreferences = json_decode($mentor['preferences'], true);
    $menteeGoals = json_decode($mentee['goals'], true);
    
    if ($mentorPreferences && $menteeGoals) {
        $commonGoals = array_intersect($mentorPreferences['teaching_areas'] ?? [], $menteeGoals['learning_areas'] ?? []);
        $score += count($commonGoals);
    }
    
    return $score;
}

function findBestMentorMatch($mentee_id, $conn) {
    $mentee = $conn->prepare("SELECT * FROM users WHERE id = ? AND role = 'mentee'");
    $mentee->bind_param("i", $mentee_id);
    $mentee->execute();
    $menteeData = $mentee->get_result()->fetch_assoc();
    
    $mentors = $conn->query("SELECT * FROM users WHERE role = 'mentor' AND status = 'active'");
    $matches = [];
    
    while ($mentor = $mentors->fetch_assoc()) {
        $score = calculateCompatibilityScore($mentor, $menteeData);
        $matches[] = [
            'mentor_id' => $mentor['id'],
            'score' => $score
        ];
    }
    
    // Sort by score descending
    usort($matches, function($a, $b) {
        return $b['score'] - $a['score'];
    });
    
    return array_slice($matches, 0, 3); // Return top 3 matches
}
?>
