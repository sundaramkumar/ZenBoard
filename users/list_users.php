<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
if (!isset($_SESSION['userName']) || !isset($_SESSION['userId'])) {
  header('Location: login.php');
  exit;
}
require_once '../db/dbconn.php';
include_once '../db/fetch_data.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZenBoard : User Management</title>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
      <script src="../scripts/utils.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">

    <link href="../css/style.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=ADLaM+Display&family=Aclonica&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/4e0b417112.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

</head>
<body class="bg-gray-100">
		<div class="max-w-8xl mx-auto">
        <div class="mb-2 inline-block">
            <div class="inline-block"><img src="../images/logo.png" style="width: 50px; height: 50px;"/></div>
            <div class="inline-block heading antialiased">ZenBoard</div>
            <!-- <div class="subtitle text-blue-500">A calm and organized approach to task management.</div> -->
        </div>

				<div class="inline-block float-right text-right">
					<span class="mr-4">Welcome, <?= htmlspecialchars($_SESSION['userName']) ?></span>
					<a href="../logout.php" class="text-blue-500 hover:text-blue-700 mr-4">Logout</a>
					<div>
						<a href="../index.php" class="text-blue-500 hover:text-blue-700 mr-4">Home</a>
					</div>
				</div>


        <div class="text-gray-600 mb-6" style="border-bottom: 1px solid #ccc;"><img src="./images/spacer.gif" alt="" width="1px" height="1px"></div>

			<div class="max-w-7xl mx-auto">
					<h1 class="text-3xl font-bold mb-6">User Management</h1>

					<div id="addUserFormContainer" class="bg-white p-6 rounded-lg shadow">
							<h2 class="text-2xl font-semibold mb-4">Add User</h2>
							<form id="addUserForm" class="space-y-4">
									<input type="text" name="username" placeholder="Username *" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required aria-required="true">
									<input type="text" name="password" placeholder="Password *" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required aria-required="true">
									<input type="email" name="email" placeholder="Email *" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required aria-required="true">
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
									<input type="text" name="username" placeholder="Username *" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" readonly>
									<input type="email" name="email" placeholder="Email *" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
									<button type="submit" class="w-half px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Update User</button>
									<button type="button" class="w-half px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-blue-600" onclick="closeEditForm()">Cancel</button>
							</form>
					</div>
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
            }).done(function (response) {
                var data = JSON.parse(response);
                if (data["success"]) {
                    showToast('User Created Successfully');
                    setTimeout(() => {
                        location.reload();
                    }, 3000);
                } else {
                    console.error('Error Occurred: ',data.message);
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.error('Error:', errorThrown);
                showToast('Failed to add user. Please try again.', 'error');
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

        function closeEditForm() {
            document.querySelector('#addUserFormContainer').classList.remove('hidden');
            document.querySelector('#editUserFormContainer').classList.add('hidden');
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
            }).done(function (response) {
                var data = JSON.parse(response);
                if (data["success"]) {
                    showToast('User Updated Successfully');
                    setTimeout(() => {
                        location.reload();
                    }, 3000);
                } else {
                    console.error('Error Occurred: ',data.message);
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.error('Error:', errorThrown);
                showToast('Failed to update user. Please try again.', 'error');
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
                }).done(function (response) {
                    var data = JSON.parse(response);
                    if (data["success"]) {
                        showToast('User Deleted Successfully');
                        setTimeout(() => {
                            location.reload();
                        }, 3000);
                    } else {
                        console.error('Error Occurred: ',data.message);
                    }
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    console.error('Error:', errorThrown);
                    showToast('Failed to delete user. Please try again.', 'error');
                });
            }
        }
    </script>
    <div id="snackbar" class="bg-blue-200"></div>
</body>
</html>
