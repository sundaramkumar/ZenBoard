<?php
require_once 'db/dbconn.php';
session_start();

function params($string, $data) {
    $indexed = $data == array_values($data);
    foreach ($data as $k => $v) {
        if (is_string($v)) $v = "'$v'";
        if ($indexed) $string = preg_replace('/\?/', $v, $string, 1);
        else $string = str_replace(":$k", $v, $string);
    }
    return $string;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    switch ($action) {
        case 'add':
            if (isset($_POST['title'], $_POST['column_id'], $_POST['user_id'], $_POST['description'])) {
                $title = $_POST['title'];
                $column_id = $_POST['column_id'];
                $user_id = $_POST['user_id'];
                $description = $_POST['description'];

                $stmt = $pdo->prepare("SELECT MAX(`order`) as max_order FROM tasks WHERE column_id = ?");
                $stmt->execute([$column_id]);
                $max_order = $stmt->fetch(PDO::FETCH_ASSOC)['max_order'];
                $new_order = $max_order + 1;

                $qry = "INSERT INTO tasks (title, description, column_id, user_id, `order`, created_by) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($qry);
                $data = [$title, $description, $column_id, $user_id, $new_order, $_SESSION['userId']];

                $success = $stmt->execute($data);
                if ($success) {
                    echo json_encode(['success' => true, 'card_id' => $pdo->lastInsertId()]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to add task']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid request']);
            }
            break;
        case 'move':
          if (isset($_POST['card_id'], $_POST['column_id'])) {
            $card_id = $_POST['card_id'];
            $column_id = $_POST['column_id'];

            $qry = "UPDATE tasks SET column_id = ? WHERE id = ?";
            $data = array($column_id,  $card_id);

            try {
              $stmt = $pdo->prepare($qry);
              $stmt->execute($data);

              echo json_encode(['success' => true]);
          } catch(PDOException $e) {
              echo json_encode(['success' => false, 'error' => $e->getMessage()]);
          }

          } else {
              echo json_encode(['success' => false, 'message' => 'Invalid request']);
          }
            break;
        case 'update':
            if (isset($_POST['card_id'], $_POST['title'], $_POST['description'], $_POST['user_id'], $_POST['tags'], $_POST['due_date'])) {
                $card_id = $_POST['card_id'];
                $title = $_POST['title'];
                $description = $_POST['description'];
                $user_id = $_POST['user_id'];
                $tags = explode(',', $_POST['tags']);
                $due_date = $_POST['due_date'] ?? date('Y-m-d');

                $qry = "UPDATE tasks SET title = ?, description = ?, user_id = ?, due_date = ? WHERE id = ?";
                $data = array($title, $description, $user_id, $due_date, $card_id);

                $stmt = $pdo->prepare($qry);
                $stmt->execute($data);

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
            break;

        case 'delete':
            if (isset($_POST['card_id'])) {
                $card_id = $_POST['card_id'];

                try {
                    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
                    $stmt->execute([$card_id]);

                    echo json_encode(['success' => true]);
                } catch(PDOException $e) {
                    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid request']);
            }
            break;

        case 'get':
            if (isset($_POST['card_id'])) {
                $card_id = $_POST['card_id'];

                $qry = "SELECT * FROM tasks WHERE id = ?";
                $data = array($card_id);
                $stmt = $pdo->prepare($qry);
                $stmt->execute($data);
                $card = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($card) {
                    $qry = "SELECT tags.name FROM tags
                                           JOIN task_tags ON tags.id = task_tags.tag_id
                                           WHERE task_tags.task_id = ?";
                    $data = array($card_id);
                    // echo params($qry, $data);
                    $stmt = $pdo->prepare($qry);
                    $stmt->execute($data);
                    $tags = $stmt->fetchAll(PDO::FETCH_COLUMN);

                    echo json_encode(['success' => true, 'card' => $card, 'tags' => $tags]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Card not found']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid request']);
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
}
?>
