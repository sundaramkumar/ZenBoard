<?php
# board.php
?>
<div class="flex gap-4 p-4">
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
                          <p class="items-start mr-2"><?= htmlspecialchars($card['title']) ?></p>
                          <p class="items-end whitespace-nowrap">
                            <button onclick="editTask(<?= $card['id'] ?>)"
                                    class="text-gray-400 hover:text-gray-600"><i class="fa fa-edit fa-xs"></i></button>
                            <button onclick="deleteTask(<?= $card['id'] ?>)"
                                    class="text-gray-400 hover:text-gray-600"><i class="fa fa-times fa-xs"></i></button>
                          </p>

                      </div>
                      <div class="text-sm text-gray-500 mt-2">
                      <p><?= htmlspecialchars($card['description']) ?></p>
                  </div>
                  <div class="text-sm text-gray-500 mt-2"><i class="fa fa-user fa-xs"></i>
                      <span class="assignedTo">
                        <?= htmlspecialchars($users[array_search($card['user_id'], array_column($users, 'id'))]['username']) ?>
                  </span>
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
