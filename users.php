<?php
require_once 'config.php';

// Fetch all users
$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">User Management</h1>

        <div id="addUserFormContainer" class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-2xl font-semibold mb-4">Add User</h2>
            <form id="addUserForm" class="space-y-4">
                <input type="text" name="username" placeholder="Username" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <input type="email" name="email" placeholder="Email" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Add User</button>
            </form>
        </div>

        <div class="bg-white p-6 rounded-lg shadow mt-6">
            <h2 class="text-2xl font-semibold mb-4">Users</h2>
            <table class="w-full border-collapse">
                <thead>
                    <tr>
                        <th class="border p-2">ID</th>
                        <th class="border p-2">Username</th>
                        <th class="border p-2">Email</th>
                        <th class="border p-2">Actions</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td class="border p-2"><?= htmlspecialchars($user['id']) ?></td>
                        <td class="border p-2"><?= htmlspecialchars($user['username']) ?></td>
                        <td class="border p-2"><?= htmlspecialchars($user['email']) ?></td>
                        <td class="border p-2">
                            <button onclick="showEditForm(<?= $user['id'] ?>, '<?= htmlspecialchars($user['username']) ?>', '<?= htmlspecialchars($user['email']) ?>')" class="text-blue-500 hover:text-blue-700">Edit</button>
                            <button onclick="deleteUser(<?= $user['id'] ?>)" class="text-red-500 hover:text-red-700">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div id="editUserFormContainer" class="hidden bg-white p-6 rounded-lg shadow mt-6">
            <h2 class="text-2xl font-semibold mb-4">Edit User</h2>
            <form id="editUserForm" class="space-y-4">
                <input type="hidden" name="user_id">
                <input type="text" name="username" placeholder="Username" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <input type="email" name="email" placeholder="Email" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Update User</button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('addUserForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            formData.append('action', 'create');
            $.ajax({
                type: "POST",
                url: "user_management.php",
                contentType: false,
                cache: false,
                processData: false,
                data: formData,
            }).done(function (data) {
                if (data.includes("success")) {
                    alert('User Created Successfully');
                    location.reload();
                } else {
                    console.log('Error Occurred');
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.error('Error:', errorThrown);
                alert('Failed to add user. Please try again.');
            });
        });

        function showEditForm(id, username, email) {
            document.querySelector('#addUserFormContainer').classList.add('hidden');
            document.querySelector('#editUserFormContainer').classList.remove('hidden');
            const form = document.querySelector('#editUserForm');
            form.user_id.value = id;
            form.username.value = username;
            form.email.value = email;
        }

        document.getElementById('editUserForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            formData.append('action', 'update');
            $.ajax({
                type: "POST",
                url: "user_management.php",
                contentType: false,
                cache: false,
                processData: false,
                data: formData,
            }).done(function (data) {
                if (data.includes("success")) {
                    alert('User Updated Successfully');
                    location.reload();
                } else {
                    console.log('Error Occurred');
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.error('Error:', errorThrown);
                alert('Failed to update user. Please try again.');
            });
        });

        function deleteUser(id) {
            if (confirm('Are you sure you want to delete this user?')) {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('user_id', id);
                $.ajax({
                    type: "POST",
                    url: "user_management.php",
                    contentType: false,
                    cache: false,
                    processData: false,
                    data: formData,
                }).done(function (data) {
                    if (data.includes("success")) {
                        alert('User Deleted Successfully');
                        location.reload();
                    } else {
                        console.log('Error Occurred');
                    }
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    console.error('Error:', errorThrown);
                    alert('Failed to delete user. Please try again.');
                });
            }
        }
    </script>
</body>
</html>
