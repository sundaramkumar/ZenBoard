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
 * add due date for tasks - done
 * add comments for tasks
 * add attachments for tasks
 * add task description - done
 * add task priority
 * add task estimation
 * add task points
 * add task progress
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
 * add task assignee - done
 * add task reporter
 * add task watchers
 * add task votes
 * add task linked issues
 * add task sub-tasks
 * add task parent task
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script>
      const users = <?= json_encode($users) ?>;
    </script>

</head>
<body class="bg-gray-100">
    <div class="max-w-8xl mx-auto">
        <div class="mb-2 inline-block">
            <div class="inline-block"><img src="./images/logonew.png" style="width: 80px; height: 50px;"/></div>
            <div class="inline-block heading antialiased">ZenBoard</div>
            <!-- <div class="subtitle text-blue-500">A calm and organized approach to task management.</div> -->
        </div>

        <div class="inline-block float-right text-right">
          <span class="mr-4">Welcome, <?= htmlspecialchars($_SESSION['userName']) ?></span>
          <a href="logout.php" class="text-blue-500 hover:text-blue-700 mr-4">Logout</a>
          <?php
          if ($_SESSION['userRole'] === 'Manager'):
          ?>
          <div>
            <a href="users/list_users.php" class="text-blue-500 hover:text-blue-700 mr-4">User Management</a>
          </div>
          <?php
          endif;
          ?>
        </div>


        <div class="text-gray-600 mb-6" style="border-bottom: 1px solid #ccc;"><img src="./images/spacer.gif" alt="" width="1px" height="1px"></div>

        <?php
        include_once('board.php');
        ?>
    </div>
<!-- right side bar -->
<?php
include_once('sidebar.php');
?>
<!-- right side bar -->
<script src="./scripts/utils.js"></script>
<script src="./scripts/board.js"></script>
<script src="./scripts/tasks.js"></script>
<div id="snackbar" class="bg-blue-200"></div>
</body>
</html>