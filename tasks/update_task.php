<?php
require_once '../db/dbconn.php';

function params($string, $data) {
    $indexed = $data == array_values($data);
    foreach ($data as $k => $v) {
        if (is_string($v)) $v = "'$v'";
        if ($indexed) $string = preg_replace('/\?/', $v, $string, 1);
        else $string = str_replace(":$k", $v, $string);
    }
    return $string;
}

if (isset($_POST['card_id'], $_POST['title'], $_POST['description'], $_POST['user_id'], $_POST['tags'], $_POST['due_date'])) {
    $card_id = $_POST['card_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $user_id = $_POST['user_id'];
    $tags = explode(',', $_POST['tags']);
    $due_date = $_POST['due_date'];

    $qry = "UPDATE tasks SET title = ?, description = ?, user_id = ?, due_date = ? WHERE id = ?";
    $data = array($title, $description, $user_id, $due_date, $card_id);

    $stmt = $pdo->prepare($qry);
    $stmt->execute($data);

    // Update tags
    $pdo->prepare("DELETE FROM task_tags WHERE task_id = ?")->execute([$card_id]);
    foreach ($tags as $tag) {
        $tag = trim($tag);
        if ($tag) {
            $stmt = $pdo->prepare("SELECT id FROM tags WHERE name = ?");
            $stmt->execute([$tag]);
            $tag_id = $stmt->fetchColumn();

            if (!$tag_id) {
                $stmt = $pdo->prepare("INSERT INTO tags (name) VALUES (?)");
                $stmt->execute([$tag]);
                $tag_id = $pdo->lastInsertId();
            }

            $stmt = $pdo->prepare("INSERT INTO task_tags (task_id, tag_id) VALUES (?, ?)");
            $stmt->execute([$card_id, $tag_id]);
        }
    }

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>