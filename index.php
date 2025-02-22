<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
if (!isset($_SESSION['userName']) || !isset($_SESSION['userId'])) {
  header('Location: login.php');
  exit;
}
/***
 * todo
 * sprint board
 * backlog entry
 * add filter to the board to see ex. tasks that are due for today, tasks that are due for this week, tasks that are due for this month, tasks that are due for this year
 * add filter to the board to see ex. tasks that are assigned to me, tasks that are assigned to other users
 * login - done
 * logout - done
 * user assignment - done
 * add tags/labels for tasks - done
 * add due date for tasks
 * add comments for tasks
 * add attachments for tasks
 * add task description
 * add task priority
 * add task estimation
 * add task points
 * add task progress
 * add task start date
 * add task end date
 * add task color
 * add task type
 * add task status
 * add task resolution
 * add task fix version
 * add task affected version
 * add task components
 * add task epic
 * add task story points
 * add task sprint
 * add task release
 * add task environment
 * add task assignee
 * add task reporter
 * add task watchers
 * add task votes
 * add task linked issues
 * add task sub-tasks
 * add task parent task
 * this application is a kanban board. i want o add this feature. while adding a new card, i want to assign an user for the task. so need to add an users table and use
*/
require_once 'db/dbconn.php';
include_once 'db/fetch_data.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZenBoard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="./css/style.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=ADLaM+Display&family=Aclonica&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/4e0b417112.js" crossorigin="anonymous"></script>
    <script>
      const users = <?= json_encode($users) ?>;
    </script>

</head>
<body class="bg-gray-100">
    <div class="max-w-8xl mx-auto">
        <div class="mb-2">
            <div class="inline-block"><img src="./images/logo.png" style="width: 50px; height: 50px;"/></div>
            <div class="inline-block heading antialiased">ZenBoard</div>
            <!-- <div class="subtitle text-blue-500">A calm and organized approach to task management.</div> -->
        </div>
        <div class="flex gap-4">
            <?php foreach ($columns as $column): ?>
            <div class="w-80 bg-white rounded-lg shadow">
                <?php
                $bgColor = 'bg-gray-50'; // Default color
                switch (strtolower($column['name'])) {
                  case 'backlog':
                    $bgColor = 'bg-gray-200';
                    break;
                  case 'done':
                    $bgColor = 'bg-green-600';
                    break;
                  case 'verified':
                    $bgColor = 'bg-green-200';
                    break;
                  case 'ready to test':
                    $bgColor = 'bg-pink-200';
                    break;
                  case 'in progress':
                    $bgColor = 'bg-yellow-300';
                    break;
                  case 'selected for development':
                    $bgColor = 'bg-blue-200';
                    break;
                }
                ?>
                <div class="p-4 <?= $bgColor; ?> border-b border-gray-200 rounded-t-lg">
                  <h2 class="font-semibold text-lg"><?= htmlspecialchars($column['name']) ?></h2>
                </div>

                <div class="column-drop-zone p-4 min-h-[200px]"
                     data-column-id="<?= $column['id'] ?>"
                     ondragover="handleDragOver(event)"
                     ondrop="handleDrop(event)">

                    <?php if (isset($tasks_by_column[$column['id']])): ?>
                        <?php foreach ($tasks_by_column[$column['id']] as $card): ?>
                        <div class="card bg-white border rounded-lg p-3 mb-2 shadow cursor-move"
                             draggable="true"
                             data-card-id="<?= $card['id'] ?>"
                             ondragstart="handleDragStart(event)"
                             ondragend="handleDragEnd(event)">
                            <div class="flex justify-between">
                                <p class="items-start"><?= htmlspecialchars($card['title']) ?></p>
                                <p class="items-end">
                                <!-- <i class="fa fa-edit fa-sm text-gray-400 hover:text-gray-600"></i> -->
                                <button onclick="editTask(<?= $card['id'] ?>)"
                                        class="text-gray-400 hover:text-gray-600"><i class="fa fa-edit fa-xs"></i></button>
                                <button onclick="deleteTask(<?= $card['id'] ?>)"
                                        class="text-gray-400 hover:text-gray-600"><i class="fa fa-times fa-xs"></i></button>
                            </p>

                            </div>
                            <div class="text-sm text-gray-500 mt-2">
                            <p><?= htmlspecialchars($card['description']) ?></p>
                                Assigned To: <?= htmlspecialchars($users[array_search($card['user_id'], array_column($users, 'id'))]['username']) ?>
                            </div>
                            <div class="tags mt-2">
                                <?php
                                $stmt = $pdo->prepare("SELECT tags.name FROM tags
                                                    JOIN task_tags ON tags.id = task_tags.tag_id
                                                    WHERE task_tags.task_id = ?");
                                $stmt->execute([$card['id']]);
                                $tags = $stmt->fetchAll(PDO::FETCH_COLUMN);
                                foreach ($tags as $tag): ?>
                                    <span class="tag"><?= htmlspecialchars($tag) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <?php if (strtolower($column['name']) == 'backlog'): ?>
                <div class="p-4 border-t border-gray-200">
                    <form onsubmit="addTask(event, <?= $column['id'] ?>)" class="space-y-2">
                        <input type="text"
                               name="title"
                               placeholder="Enter task..."
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <textarea name="description"
                                  placeholder="Enter description..."
                                  class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        <select name="user_id" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Assign user...</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit"
                                class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                            Add Task
                        </button>
                    </form>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
<!-- right side bar -->
<div id="sidebar" class="fixed right-0 top-0 h-full w-80 bg-white shadow-lg transform translate-x-full transition-transform">
    <div class="flex  justify-between p-4 border-b">
        <h2 class="text-xl font-semibold items-start">Edit Task</h2>
        <button onclick="closeSidebar()" class="text-gray-400 hover:text-gray-600 float-right items-end">
            <i class="fa fa-times"></i></button>
    </div>
    <div class="p-4">
        <form id="editTaskForm" onsubmit="saveTask(event)">
            <input type="hidden" name="card_id" id="editCardId">
            <div class="mb-4">
                <label for="editTitle" class="block text-sm font-medium text-gray-700">Title</label>
                <input type="text" name="title" id="editTitle" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-4">
                <label for="editDescription" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="editDescription" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            <div class="mb-4">
                <label for="editUserId" class="block text-sm font-medium text-gray-700">Assign User</label>
                <select name="user_id" id="editUserId" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Assign user...</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-4">
                <label for="editDueDate" class="block text-sm font-medium text-gray-700">Due Date</label>
                <input type="date" name="due_date" id="editDueDate" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-4">
                <label for="editTags" class="block text-sm font-medium text-gray-700">Tags</label>
                <input type="text" name="tags" id="editTags" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Comma-separated tags">
            </div>

            <button type="submit" class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Save Task</button>
        </form>
    </div>
</div>

<!-- right side bar -->
<script src="./scripts/utils.js"></script>
<script src="./scripts/board.js"></script>
<script src="./scripts/tasks.js"></script>
<div id="snackbar" class="bg-blue-200"></div>
</body>
</html>