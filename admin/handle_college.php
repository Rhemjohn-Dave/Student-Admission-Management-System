<?php
session_start();
require_once "../config/database.php";

// Check if user is admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    $_SESSION['error'] = "Unauthorized access";
    header("Location: index.php?page=colleges");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"])) {
    switch ($_POST["action"]) {
        case "add_college":
            $college_name = trim($_POST["college_name"]);
            $college_code = trim($_POST["college_code"]);
            $description = trim($_POST["description"] ?? '');
            
            if (empty($college_name) || empty($college_code)) {
                $_SESSION['error'] = "College name and code are required";
            } else {
                $sql = "INSERT INTO colleges (college_name, college_code, description) VALUES (?, ?, ?)";
                if ($stmt = mysqli_prepare($conn, $sql)) {
                    mysqli_stmt_bind_param($stmt, "sss", $college_name, $college_code, $description);
                    if (mysqli_stmt_execute($stmt)) {
                        $_SESSION['success'] = "College added successfully";
                    } else {
                        $_SESSION['error'] = "Error adding college: " . mysqli_error($conn);
                    }
                }
            }
            break;

        case "update_college":
            $college_id = $_POST["college_id"];
            $college_name = trim($_POST["college_name"]);
            $college_code = trim($_POST["college_code"]);
            $description = trim($_POST["description"] ?? '');
            
            if (empty($college_name) || empty($college_code)) {
                $_SESSION['error'] = "College name and code are required";
            } else {
                $sql = "UPDATE colleges SET college_name = ?, college_code = ?, description = ? WHERE college_id = ?";
                if ($stmt = mysqli_prepare($conn, $sql)) {
                    mysqli_stmt_bind_param($stmt, "sssi", $college_name, $college_code, $description, $college_id);
                    if (mysqli_stmt_execute($stmt)) {
                        $_SESSION['success'] = "College updated successfully";
                    } else {
                        $_SESSION['error'] = "Error updating college: " . mysqli_error($conn);
                    }
                }
            }
            break;

        case "delete_college":
            $college_id = $_POST["college_id"];
            $sql = "DELETE FROM colleges WHERE college_id = ?";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "i", $college_id);
                if (mysqli_stmt_execute($stmt)) {
                    $_SESSION['success'] = "College deleted successfully";
                } else {
                    $_SESSION['error'] = "Error deleting college: " . mysqli_error($conn);
                }
            }
            break;
    }
}

// Redirect back to colleges page
header("Location: index.php?page=colleges");
exit(); 