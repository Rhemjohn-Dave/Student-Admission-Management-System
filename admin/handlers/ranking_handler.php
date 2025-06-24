<?php
// Function to recalculate rankings
function recalculateRankings($conn) {
    // Clear existing rankings
    $conn->query("TRUNCATE TABLE program_rankings");
    
    // Get all applicants with scores and their global rank
    $global_rank_query = "
        SELECT a.applicant_id, esc.score,
            RANK() OVER (ORDER BY esc.score DESC) as global_rank
        FROM applicants a
        JOIN exam_registrations reg ON a.applicant_id = reg.applicant_id
        JOIN exam_scores esc ON reg.registration_id = esc.registration_id
    ";
    $global_ranks = $conn->query($global_rank_query);
    $applicant_ranks = [];
    while ($row = $global_ranks->fetch_assoc()) {
        $applicant_ranks[$row['applicant_id']] = [
            'score' => $row['score'],
            'global_rank' => $row['global_rank']
        ];
    }
    
    // Get all programs and their cutoff ranges
    $programs = $conn->query("SELECT program_id, start_rank, end_rank FROM program_cutoffs WHERE is_active = 1");
    while ($program = $programs->fetch_assoc()) {
        $program_id = $program['program_id'];
        $start_rank = $program['start_rank'];
        $end_rank = $program['end_rank'];
        
        // For each applicant, check if their global rank falls within the program's range
        foreach ($applicant_ranks as $applicant_id => $data) {
            $score = $data['score'];
            $global_rank = $data['global_rank'];
            $is_eligible = ($global_rank >= $start_rank && $global_rank <= $end_rank) ? 1 : 0;
            if ($is_eligible) {
                // Insert into program_rankings
                $stmt = $conn->prepare("INSERT INTO program_rankings (applicant_id, program_id, exam_score, rank_position, is_eligible) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("iidii", $applicant_id, $program_id, $score, $global_rank, $is_eligible);
                $stmt->execute();
            }
        }
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