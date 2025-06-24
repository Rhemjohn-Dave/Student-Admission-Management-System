<?php
// Simple test to verify handler is accessible
header('Content-Type: application/json');
echo json_encode(['status' => 'success', 'message' => 'Test file is accessible']);
?> 