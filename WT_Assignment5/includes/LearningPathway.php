<?php
class LearningPathway {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function createPathway($mentee_id, $data) {
        $stmt = $this->conn->prepare("INSERT INTO learning_paths 
            (mentee_id, title, description, objectives, duration) 
            VALUES (?, ?, ?, ?, ?)");
            
        $stmt->bind_param("issss", 
            $mentee_id, 
            $data['title'], 
            $data['description'],
            json_encode($data['objectives']),
            $data['duration']
        );
        
        return $stmt->execute();
    }
    
    public function addMilestone($pathway_id, $milestone) {
        $stmt = $this->conn->prepare("INSERT INTO milestones 
            (pathway_id, title, description, deadline, completion_criteria) 
            VALUES (?, ?, ?, ?, ?)");
            
        $stmt->bind_param("issss", 
            $pathway_id,
            $milestone['title'],
            $milestone['description'],
            $milestone['deadline'],
            json_encode($milestone['criteria'])
        );
        
        return $stmt->execute();
    }
    
    public function getPathwayProgress($pathway_id) {
        $stmt = $this->conn->prepare("SELECT 
            COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed,
            COUNT(*) as total
            FROM milestones 
            WHERE pathway_id = ?");
        $stmt->bind_param("i", $pathway_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function updateMilestoneStatus($milestone_id, $status) {
        $stmt = $this->conn->prepare("UPDATE milestones SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $milestone_id);
        return $stmt->execute();
    }
    
    public function assignResources($milestone_id, $resource_ids) {
        $stmt = $this->conn->prepare("INSERT INTO milestone_resources (milestone_id, resource_id) VALUES (?, ?)");
        foreach ($resource_ids as $resource_id) {
            $stmt->bind_param("ii", $milestone_id, $resource_id);
            $stmt->execute();
        }
        return true;
    }

    public function getAllPathways($mentee_id) {
        $stmt = $this->conn->prepare("
            SELECT lp.*, 
                   u.name as mentor_name,
                   COUNT(m.id) as total_milestones,
                   COUNT(CASE WHEN m.status = 'completed' THEN 1 END) as completed_milestones
            FROM learning_paths lp
            LEFT JOIN users u ON lp.mentor_id = u.id
            LEFT JOIN milestones m ON lp.id = m.pathway_id
            WHERE lp.mentee_id = ?
            GROUP BY lp.id
            ORDER BY lp.created_at DESC");
        
        $stmt->bind_param("i", $mentee_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getCompletionStatus($pathway_id) {
        $stmt = $this->conn->prepare("
            SELECT 
                (COUNT(CASE WHEN status = 'completed' THEN 1 END) * 100 / COUNT(*)) as completion_percentage,
                COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed,
                COUNT(*) as total
            FROM milestones 
            WHERE pathway_id = ?");
        
        $stmt->bind_param("i", $pathway_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
?>
