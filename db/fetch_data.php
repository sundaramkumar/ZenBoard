<?php
// Fetch all columns
$stmt = $pdo->query("SELECT * FROM columns ORDER BY `order`");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all tasks
$stmt = $pdo->query("SELECT * FROM tasks ORDER BY `order`");
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all users
$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group tasks by column
$tasks_by_column = [];
foreach ($tasks as $card) {
    $tasks_by_column[$card['column_id']][] = $card;
}
?>