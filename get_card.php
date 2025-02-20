<?php
require_once 'config.php';

if (isset($_POST['card_id'])) {
    $card_id = $_POST['card_id'];

    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
    $stmt->execute([$card_id]);
    $card = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($card) {
        echo json_encode(['success' => true, 'card' => $card]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Card not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>