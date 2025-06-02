<?php
// Function to get all active programs
function getActivePrograms($conn) {
    $programs = array();
    $sql = "SELECT program_id, program_name FROM programs ORDER BY program_name";
    
    if($result = mysqli_query($conn, $sql)){
        while($row = mysqli_fetch_assoc($result)){
            $programs[] = $row;
        }
        mysqli_free_result($result);
    }
    return $programs;
}

// Function to get program details by ID
function getProgramById($conn, $program_id) {
    $sql = "SELECT * FROM programs WHERE program_id = ?";
    
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "i", $program_id);
        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);
            if($row = mysqli_fetch_assoc($result)){
                return $row;
            }
        }
        mysqli_stmt_close($stmt);
    }
    return false;
}
?> 