<?php
// Function to recalculate rankings
function recalculateRankings($conn) {
    // Clear existing rankings
    $conn->query("TRUNCATE TABLE program_rankings");
    
    // Get all programs
    $programs = $conn->query("SELECT program_id FROM programs");
    
    while ($program = $programs->fetch_assoc()) {
        $program_id = $program['program_id'];
        
        // Get cutoff rank for this program
        $cutoff_query = "SELECT cutoff_rank FROM program_cutoffs WHERE program_id = ? AND is_active = 1";
        $cutoff_stmt = $conn->prepare($cutoff_query);
        $cutoff_stmt->bind_param("i", $program_id);
        $cutoff_stmt->execute();
        $cutoff_result = $cutoff_stmt->get_result();
        $cutoff_rank = $cutoff_result->fetch_assoc()['cutoff_rank'] ?? PHP_INT_MAX;
        
        // Get all applicants with exam scores for this program
        $rank_query = "
            INSERT INTO program_rankings (applicant_id, program_id, exam_score, rank_position, is_eligible)
            SELECT 
                a.applicant_id,
                ?,
                er.score,
                RANK() OVER (ORDER BY er.score DESC),
                CASE WHEN RANK() OVER (ORDER BY er.score DESC) <= ? THEN TRUE ELSE FALSE END
            FROM applicants a
            JOIN exam_results er ON a.user_id = er.user_id
            WHERE a.primary_program_id = ? OR a.secondary_program_id = ?
            AND er.score IS NOT NULL
        ";
        
        $rank_stmt = $conn->prepare($rank_query);
        $rank_stmt->bind_param("iiii", $program_id, $cutoff_rank, $program_id, $program_id);
        $rank_stmt->execute();
    }
    
    // Assign programs based on rankings
    assignPrograms($conn);
}

// Function to assign programs based on rankings
function assignPrograms($conn) {
    // Get all applicants
    $applicants = $conn->query("
        SELECT DISTINCT applicant_id 
        FROM program_rankings 
        ORDER BY exam_score DESC
    ");
    
    while ($applicant = $applicants->fetch_assoc()) {
        $applicant_id = $applicant['applicant_id'];
        
        // Get applicant's rankings
        $rankings = $conn->query("
            SELECT pr.*, p.program_name 
            FROM program_rankings pr
            JOIN programs p ON pr.program_id = p.program_id
            WHERE pr.applicant_id = $applicant_id
            ORDER BY pr.rank_position ASC
        ");
        
        $assigned = false;
        while ($ranking = $rankings->fetch_assoc()) {
            if ($ranking['is_eligible']) {
                // Update assigned program
                $update = $conn->prepare("
                    UPDATE program_rankings 
                    SET assigned_program_id = ? 
                    WHERE applicant_id = ?
                ");
                $update->bind_param("ii", $ranking['program_id'], $applicant_id);
                $update->execute();
                $assigned = true;
                break;
            }
        }
        
        // If no eligible program found, assign based on best rank
        if (!$assigned) {
            $best_rank = $conn->query("
                SELECT program_id 
                FROM program_rankings 
                WHERE applicant_id = $applicant_id 
                ORDER BY rank_position ASC 
                LIMIT 1
            ")->fetch_assoc();
            
            if ($best_rank) {
                $update = $conn->prepare("
                    UPDATE program_rankings 
                    SET assigned_program_id = ? 
                    WHERE applicant_id = ?
                ");
                $update->bind_param("ii", $best_rank['program_id'], $applicant_id);
                $update->execute();
            }
        }
    }
} 