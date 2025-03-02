<?php
# sidebar.php
?>
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
                <input type="text" name="title" id="editTitle" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="editDescription" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="editDescription" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required></textarea>
            </div>
            <div class="mb-4">
                <label for="editUserId" class="block text-sm font-medium text-gray-700">Assign User</label>
                <select name="user_id" id="editUserId" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="">Assign user...</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-4">
                <label for="editDueDate" class="block text-sm font-medium text-gray-700">Due Date</label>
                <input type="date" name="due_date" id="editDueDate" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="editTags" class="block text-sm font-medium text-gray-700">Tags</label>
                <input type="text" name="tags" id="editTags" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Comma-separated tags">
            </div>

            <button type="submit" class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Save Task</button>
        </form>
    </div>
</div>
