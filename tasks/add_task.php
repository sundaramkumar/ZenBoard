<?php
require_once '../db/dbconn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $column_id = $_POST['column_id'];
    $user_id = $_POST['user_id'];
    $description = $_POST['description'];

    // try {
    //     $stmt = $pdo->prepare("INSERT INTO tasks (title, column_id, user_id) VALUES (?, ?, ?)");
    //     $stmt->execute([$title, $column_id, $user_id]);
    //     echo json_encode(['success' => true, 'card_id' => $pdo->lastInsertId()]);
    // } catch (PDOException $e) {
    //     echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    // }


    // Fetch the highest order value in the column
    $stmt = $pdo->prepare("SELECT MAX(`order`) as max_order FROM tasks WHERE column_id = ?");
    $stmt->execute([$column_id]);
    $max_order = $stmt->fetch(PDO::FETCH_ASSOC)['max_order'];
    $new_order = $max_order + 1;

    $stmt = $pdo->prepare("INSERT INTO tasks (title, description, column_id, user_id, `order`, created_by) VALUES (?, ?, ?, ?, ?, ?)");
    $success = $stmt->execute([$title, $description, $column_id, $user_id, $new_order, $_SESSION['userId']]);
    $stmt->debugDumpParams();
    if ($success) {
        echo json_encode(['success' => true, 'card_id' => $pdo->lastInsertId()]);
    } else {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>