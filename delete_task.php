<?php
require 'database.php'; // Включи PDO конфигурацията тук

$data = json_decode(file_get_contents('php://input'), true);
if (isset($data['id']) && isset($data['type'])) {
    $id = $data['id'];
    $type = $data['type'];

    if ($type === 'todo') {
        // Изтрий основна задача и свързаните подзадачи
        $stmt = $pdo->prepare("DELETE FROM todos WHERE id = :id");
    } elseif ($type === 'subtask') {
        // Изтрий подзадача
        $stmt = $pdo->prepare("DELETE FROM subtasks WHERE id = :id");
    }

    $stmt->execute([':id' => $id]);
    echo json_encode(['success' => true]);
    exit;
}

echo json_encode(['success' => false]);
?>
