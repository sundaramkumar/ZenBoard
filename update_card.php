<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $card_id = $_POST['card_id'];
    $column_id = $_POST['column_id'];

    try {
        // Get the highest order in the target column
        $stmt = $pdo->prepare("SELECT MAX(`order`) FROM tasks WHERE column_id = ?");
        $stmt->execute([$column_id]);
        $maxOrder = $stmt->fetchColumn() ?: 0;

        // Update card
        $stmt = $pdo->prepare("UPDATE tasks SET column_id = ?, `order` = ? WHERE id = ?");
        $stmt->execute([$column_id, $maxOrder + 1, $card_id]);

        echo json_encode(['success' => true]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>