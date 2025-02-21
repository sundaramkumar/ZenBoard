<?php
require_once 'config.php';

if (isset($_POST['card_id'])) {
    $card_id = $_POST['card_id'];

    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
    $stmt->execute([$card_id]);
    $card = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($card) {
        $stmt = $pdo->prepare("SELECT tags.name FROM tags
                               JOIN task_tags ON tags.id = task_tags.tag_id
                               WHERE task_tags.task_id = ?");
        $stmt->execute([$card_id]);
        $tags = $stmt->fetchAll(PDO::FETCH_COLUMN);

        echo json_encode(['success' => true, 'card' => $card, 'tags' => $tags]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Card not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>