<?php
require 'database.php'; // Include PDO configuration

// Read JSON input
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['id'])) {
    $id = $data['id'];

    // Check if the subtask exists and fetch the current status
    $stmt = $pdo->prepare("SELECT status FROM subtasks WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $subtask = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($subtask) {
        // Toggle the status between 'Completed' and 'Pending'
        $newStatus = ($subtask['status'] === 'Completed') ? 'Pending' : 'Completed';

        // Update the status in the database
        $updateStmt = $pdo->prepare("UPDATE subtasks SET status = :status WHERE id = :id");
        $updateStmt->execute([':status' => $newStatus, ':id' => $id]);

        // Return a success response with the new status
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Subtask status updated successfully.',
            'newStatus' => $newStatus
        ]);
    } else {
        // Subtask not found
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Subtask not found.']);
    }
} else {
    // Invalid subtask ID
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid subtask ID.']);
}
