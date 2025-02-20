<?php
/***
 * todo
 * sprint board
 * backlog entry
 * user assignment
 * add tags/labels for cards
 * add due date for cards
 * add comments for cards
 * add attachments for cards
 * add card description
 * add card priority
 * add card estimation
 * add card points
 * add card progress
 * add card start date
 * add card end date
 * add card color
 * add card type
 * add card status
 * add card resolution
 * add card fix version
 * add card affected version
 * add card components
 * add card epic
 * add card story points
 * add card sprint
 * add card release
 * add card environment
 * add card assignee
 * add card reporter
 * add card watchers
 * add card votes
 * add card linked issues
 * add card sub-tasks
 * add card parent task
 * this application is a kanban board. i want o add this feature. while adding a new card, i want to assign an user for the task. so need to add an users table and use
*/
require_once 'config.php';

// Fetch all columns
$stmt = $pdo->query("SELECT * FROM columns ORDER BY `order`");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all cards
$stmt = $pdo->query("SELECT * FROM cards ORDER BY `order`");
$cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all users
$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group cards by column
$cards_by_column = [];
foreach ($cards as $card) {
    $cards_by_column[$card['column_id']][] = $card;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kanban Board</title>


    <!-- <link href="../css/bootstrap.min.css" rel="stylesheet"> -->
    <!-- <link href="../font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" /> -->
    <style>
      @import url("https://fonts.googleapis.com/css2?family=Roboto&display=swap");
        .card.dragging {
            opacity: 0.5;
        }
        .column-drop-zone.drag-over {
            background-color: #f3f4f6;
        }
        body, html {
          margin:15px;
          padding: 0;
          font-family: 'Roboto', sans-serif;
        }
    </style>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="max-w-8xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Kanban Board</h1>

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

                    <?php if (isset($cards_by_column[$column['id']])): ?>
                        <?php foreach ($cards_by_column[$column['id']] as $card): ?>
                        <div class="card bg-white border rounded-lg p-3 mb-2 shadow cursor-move"
                             draggable="true"
                             data-card-id="<?= $card['id'] ?>"
                             ondragstart="handleDragStart(event)"
                             ondragend="handleDragEnd(event)">
                            <div class="flex justify-between items-start">
                                <p><?= htmlspecialchars($card['title']) ?></p>
                                <button onclick="deleteCard(<?= $card['id'] ?>)"
                                        class="text-gray-400 hover:text-gray-600">×</button>
                            </div>
                            <div class="text-sm text-gray-500 mt-2">
                                Assigned To: <?= htmlspecialchars($users[array_search($card['user_id'], array_column($users, 'id'))]['username']) ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <?php if (strtolower($column['name']) == 'backlog'): ?>
                <div class="p-4 border-t border-gray-200">
                    <form onsubmit="addCard(event, <?= $column['id'] ?>)" class="space-y-2">
                        <input type="text"
                               name="title"
                               placeholder="Enter task..."
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <select name="user_id" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Assign user...</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit"
                                class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                            Add Card
                        </button>
                    </form>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script>
        let draggedCard = null;
        const users = <?= json_encode($users) ?>;

        function handleDragStart(event) {
            draggedCard = event.target;
            event.target.classList.add('dragging');
        }

        function handleDragEnd(event) {
            event.target.classList.remove('dragging');
            draggedCard = null;
            document.querySelectorAll('.column-drop-zone').forEach(zone => {
                zone.classList.remove('drag-over');
            });
        }

        function handleDragOver(event) {
            event.preventDefault();
            event.currentTarget.classList.add('drag-over');
        }

        function handleDrop(event) {
            event.preventDefault();
            const targetColumn = event.currentTarget;
            const sourceColumn = draggedCard.parentElement;
            const columnId = targetColumn.dataset.columnId;
            const cardId = draggedCard.dataset.cardId;

            // Optimistically update UI
            targetColumn.appendChild(draggedCard);

            // Update card's column in the database
            fetch('update_card.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `card_id=${cardId}&column_id=${columnId}`
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    // If update fails, revert the UI change
                    sourceColumn.appendChild(draggedCard);
                    alert('Failed to move card. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Revert UI on error
                sourceColumn.appendChild(draggedCard);
                alert('Failed to move card. Please try again.');
            });

            targetColumn.classList.remove('drag-over');
        }

        function addCard(event, columnId) {
          event.preventDefault();
          const form = event.target;
          const title = form.title.value;
          const userId = form.user_id.value;
          const dropZone = document.querySelector(`[data-column-id="${columnId}"]`);

          if (!title.trim()) return;

          // Create card element with loading state
          const tempCard = document.createElement('div');
          tempCard.className = 'card bg-white border rounded-lg p-3 mb-2 shadow cursor-move opacity-50';
          tempCard.innerHTML = `
              <div class="flex justify-between items-start">
                  <p>${escapeHtml(title)}</p>
                  <span class="text-gray-400">Adding...</span>
              </div>
          `;
          dropZone.appendChild(tempCard);

          fetch('add_card.php', {
              method: 'POST',
              headers: {
                  'Content-Type': 'application/x-www-form-urlencoded',
              },
              body: `title=${encodeURIComponent(title)}&column_id=${columnId}&user_id=${userId}`
          })
          .then(response => response.json())
          .then(data => {
              if (data.success) {
                  // Find the assigned user's name
                  const assignedUser = users.find(user => user.id == userId);

                  // Replace temporary card with actual card
                  const newCard = document.createElement('div');
                  newCard.className = 'card bg-white border rounded-lg p-3 mb-2 shadow cursor-move';
                  newCard.draggable = true;
                  newCard.dataset.cardId = data.card_id;
                  newCard.innerHTML = `
                      <div class="flex justify-between items-start">
                          <p>${escapeHtml(title)}</p>
                          <button onclick="deleteCard(${data.card_id})"
                                  class="text-gray-400 hover:text-gray-600">×</button>
                      </div>
                      <div class="text-sm text-gray-500 mt-2">
                          Assigned To: ${escapeHtml(assignedUser ? assignedUser.username : 'Unassigned')}
                      </div>
                  `;
                  newCard.addEventListener('dragstart', handleDragStart);
                  newCard.addEventListener('dragend', handleDragEnd);
                  dropZone.replaceChild(newCard, tempCard);
              } else {
                  dropZone.removeChild(tempCard);
                  alert('Failed to add card. Please try again.');
              }
          })
          .catch(error => {
              console.error('Error:', error);
              dropZone.removeChild(tempCard);
              alert('Failed to add card. Please try again.');
          });

          form.reset();
      }


        function deleteCard(cardId) {
            if (!confirm('Are you sure you want to delete this card?')) return;

            const card = document.querySelector(`[data-card-id="${cardId}"]`);
            // Add loading state
            card.classList.add('opacity-50');

            fetch('delete_card.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `card_id=${cardId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    card.remove();
                } else {
                    card.classList.remove('opacity-50');
                    alert('Failed to delete card. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                card.classList.remove('opacity-50');
                alert('Failed to delete card. Please try again.');
            });
        }

        // Helper function to escape HTML special characters
        function escapeHtml(unsafe) {
            return unsafe
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
    </script>
</body>
</html>