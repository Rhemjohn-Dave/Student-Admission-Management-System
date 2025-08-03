<?php
require_once "../../config/database.php";

// Function to update program rankings for a specific applicant
function updateProgramRankings($conn, $applicant_id) {
    // Get applicant's exam score
    $exam_query = "
        SELECT es.score as exam_score
        FROM applicants a
        LEFT JOIN exam_registrations er ON a.applicant_id = er.applicant_id
        LEFT JOIN exam_scores es ON er.registration_id = es.registration_id
        WHERE a.applicant_id = ?
    ";
    
    $stmt = mysqli_prepare($conn, $exam_query);
    mysqli_stmt_bind_param($stmt, "i", $applicant_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $applicant_data = mysqli_fetch_assoc($result);
    
    if (!$applicant_data || !$applicant_data['exam_score']) {
        return false; // No exam score available
    }
    
    $exam_score = $applicant_data['exam_score'];
    
    // Get all programs the applicant is interested in (primary and secondary)
    $programs_query = "
        SELECT primary_program_id, secondary_program_id
        FROM applicants
        WHERE applicant_id = ?
    ";
    
    $stmt = mysqli_prepare($conn, $programs_query);
    mysqli_stmt_bind_param($stmt, "i", $applicant_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $programs_data = mysqli_fetch_assoc($result);
    
    if (!$programs_data) {
        return false;
    }
    
    $programs = array_filter([$programs_data['primary_program_id'], $programs_data['secondary_program_id']]);
    
    foreach ($programs as $program_id) {
        if (!$program_id) continue;
        
        // Delete existing ranking for this applicant and program
        $delete_query = "DELETE FROM program_rankings WHERE applicant_id = ? AND program_id = ?";
        $stmt = mysqli_prepare($conn, $delete_query);
        mysqli_stmt_bind_param($stmt, "ii", $applicant_id, $program_id);
        mysqli_stmt_execute($stmt);
        
        // Get all applicants for this program with their exam scores
        $rankings_query = "
            SELECT 
                a.applicant_id,
                COALESCE(es.score, 0) as exam_score
            FROM applicants a
            LEFT JOIN exam_registrations er ON a.applicant_id = er.applicant_id
            LEFT JOIN exam_scores es ON er.registration_id = es.registration_id
            WHERE (a.primary_program_id = ? OR a.secondary_program_id = ?)
            AND a.applicant_id != ?
            ORDER BY COALESCE(es.score, 0) DESC
        ";
        
        $stmt = mysqli_prepare($conn, $rankings_query);
        mysqli_stmt_bind_param($stmt, "iii", $program_id, $program_id, $applicant_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $rankings = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rankings[] = $row;
        }
        
        // Find position for current applicant
        $position = 1;
        foreach ($rankings as $ranking) {
            if ($ranking['exam_score'] >= $exam_score) {
                $position++;
            }
        }
        
        // Insert new ranking
        $insert_query = "
            INSERT INTO program_rankings (applicant_id, program_id, exam_score, rank_position, is_eligible)
            VALUES (?, ?, ?, ?, 0)
        ";
        
        $stmt = mysqli_prepare($conn, $insert_query);
        mysqli_stmt_bind_param($stmt, "iidi", $applicant_id, $program_id, $exam_score, $position);
        mysqli_stmt_execute($stmt);
        
        // Update eligibility based on cutoffs
        $cutoff_query = "
            SELECT start_rank, end_rank
            FROM program_cutoffs
            WHERE program_id = ? AND is_active = 1
            ORDER BY start_rank ASC
            LIMIT 1
        ";
        
        $stmt = mysqli_prepare($conn, $cutoff_query);
        mysqli_stmt_bind_param($stmt, "i", $program_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $cutoff = mysqli_fetch_assoc($result);
        
        if ($cutoff && $position >= $cutoff['start_rank'] && $position <= $cutoff['end_rank']) {
            $update_eligibility = "UPDATE program_rankings SET is_eligible = 1 WHERE applicant_id = ? AND program_id = ?";
            $stmt = mysqli_prepare($conn, $update_eligibility);
            mysqli_stmt_bind_param($stmt, "ii", $applicant_id, $program_id);
            mysqli_stmt_execute($stmt);
        }
    }
    
    return true;
}

// Handle AJAX request to update rankings
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_rankings') {
    $applicant_id = $_POST['applicant_id'];
    
    if (updateProgramRankings($conn, $applicant_id)) {
        echo json_encode(['success' => true, 'message' => 'Program rankings updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update program rankings']);
    }
    exit();
}
?> 