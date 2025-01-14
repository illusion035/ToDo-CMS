<?php
require 'database.php'; // Include PDO configuration

// Add main task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['todo_title'])) {
    $title = $_POST['todo_title'];
    $stmt = $pdo->prepare("INSERT INTO todos (title) VALUES (:title)");
    $stmt->execute([':title' => $title]);
    exit;
}

// Add subtask
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subtask']) && isset($_POST['todo_id'])) {
    $subtask = $_POST['subtask'];
    $todo_id = $_POST['todo_id'];
    $stmt = $pdo->prepare("INSERT INTO subtasks (todo_id, subtask) VALUES (:todo_id, :subtask)");
    $stmt->execute([':todo_id' => $todo_id, ':subtask' => $subtask]);
    exit;
}

// Fetch all tasks with subtasks
$stmt = $pdo->query("
    SELECT todos.id AS todo_id, todos.title, subtasks.id AS subtask_id, subtasks.subtask, subtasks.status
    FROM todos
    LEFT JOIN subtasks ON todos.id = subtasks.todo_id
    ORDER BY todos.id DESC, subtasks.id ASC
");
$todos = $stmt->fetchAll(PDO::FETCH_GROUP);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-900 text-gray-100 min-h-screen flex flex-col items-center">

    <!-- Alerts -->
    <div id="alertContainer" class="fixed top-4 right-4 space-y-4 z-50"></div>

    <!-- Main Container -->
    <div class="w-full max-w-5xl p-8 bg-gray-800 rounded-xl shadow-lg mt-8">
        <h1 class="text-3xl font-extrabold text-gray-100 mb-6 border-b-2 border-gray-700 pb-3">📋 To-Do List</h1>

        <!-- Form to Add Main Task -->
        <form id="addTodoForm" class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <input type="text" name="todo_title" id="todo_title"
                    class="flex-1 p-3 text-lg border border-gray-700 bg-gray-700 text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 shadow-sm placeholder-gray-400"
                    placeholder="Add a main task" required>
                <button type="submit"
                    class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 shadow-md flex items-center gap-2 justify-center">
                    <span class="material-icons">add</span> Add
                </button>
            </div>
        </form>

        <!-- Alert if No Tasks -->
        <?php if (empty($todos)): ?>
            <div class="bg-yellow-500 text-black text-center p-4 rounded-lg mb-8 shadow-md">
                <span class="material-icons mr-2">warning</span>
                No tasks added yet. Start by adding a new task!
            </div>
        <?php endif; ?>

        <!-- List of Tasks -->
        <?php foreach ($todos as $todo_id => $tasks): ?>
            <div class="mb-8 bg-gray-700 p-5 rounded-lg shadow-md border border-gray-600">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-4">
                    <h2 class="text-xl font-semibold text-gray-200"><?= htmlspecialchars($tasks[0]['title']) ?></h2>
                    <button
                        class="delete-todo bg-red-500 text-white px-3 py-2 rounded-lg hover:bg-red-600 shadow-md flex items-center gap-2 justify-center"
                        data-id="<?= $todo_id ?>">
                        <span class="material-icons">delete</span> Delete
                    </button>
                </div>

                <!-- Form to Add Subtask -->
                <form class="mb-4 addSubtaskForm" data-todo-id="<?= $todo_id ?>">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                        <input type="text" name="subtask"
                            class="flex-1 p-3 text-md border border-gray-600 bg-gray-700 text-gray-100 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-400 shadow-sm placeholder-gray-400"
                            placeholder="Add a subtask" required>
                        <button type="submit"
                            class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 shadow-md flex items-center gap-2 justify-center">
                            <span class="material-icons">add_task</span> Add
                        </button>
                    </div>
                </form>

                <!-- Subtasks -->
                <ul class="space-y-3">
                    <?php foreach ($tasks as $task): ?>
                        <?php if ($task['subtask']): ?>
                            <li class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-4 bg-gray-800 border border-gray-600 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 
                        <?php echo $task['status'] === 'Completed' ? 'line-through text-gray-500 bg-gray-700' : ''; ?> "
                                data-subtask-id="<?= $task['subtask_id'] ?>">
                                <span class="text-lg"><?= htmlspecialchars($task['subtask']) ?></span>
                                <div class="flex flex-row gap-2 mt-3 sm:mt-0">
                                    <button
                                        class="status-toggle p-3 flex items-center justify-center text-white rounded-lg shadow-md 
                                    <?php echo $task['status'] === 'Completed' ? 'bg-red-500 hover:bg-red-600' : 'bg-green-500 hover:bg-green-600'; ?>">
                                        <span
                                            class="material-icons text-lg"><?= $task['status'] === 'Completed' ? 'close' : 'check' ?></span>
                                    </button>
                                    <button
                                        class="delete-subtask bg-red-500 text-white px-3 py-2 rounded-lg hover:bg-red-600 shadow-md flex items-center gap-2 justify-center"
                                        data-id="<?= $task['subtask_id'] ?>">
                                        <span class="material-icons">delete</span>
                                    </button>
                                </div>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
        <footer class="mt-8 text-center text-gray-400">
            <p class="text-sm">
                Created by
                <span class="font-semibold text-white">illusion</span> @
                <a href="https://craftvision.net" target="_blank" class="text-blue-400 hover:text-blue-500 transition">
                    CraftVision.NET
                    <span class="material-icons align-middle text-sm">open_in_new</span>
                </a>
            </p>
        </footer>

    </div>

    <script>
        // Function to Show Alerts
        function showAlert(message, type) {
            const alertContainer = document.getElementById('alertContainer');
            const alert = document.createElement('div');
            alert.className = `flex items-center gap-3 p-4 rounded-lg shadow-md text-white ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
            alert.innerHTML = `
            <span class="material-icons">${type === 'success' ? 'check_circle' : 'error'}</span>
            <span>${message}</span>
        `;
            alertContainer.appendChild(alert);

            // Remove alert after 3 seconds
            setTimeout(() => {
                alert.remove();
            }, 3000);
        }

        // Add Main Task
        document.getElementById('addTodoForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);

            const response = await fetch('index.php', {
                method: 'POST',
                body: formData
            });
            if (response.ok) {
                showAlert('Main task was successfully added!', 'success');
                setTimeout(() => location.reload(), 1000);
            }
        });

        // Add Subtask
        document.querySelectorAll('.addSubtaskForm').forEach(form => {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);
                formData.append('todo_id', form.getAttribute('data-todo-id'));

                const response = await fetch('index.php', {
                    method: 'POST',
                    body: formData
                });
                if (response.ok) {
                    showAlert('Subtask was successfully added!', 'success');
                    setTimeout(() => location.reload(), 1000);
                }
            });
        });

        // Delete Main Task
        document.querySelectorAll('.delete-todo').forEach(button => {
            button.addEventListener('click', async (e) => {
                const todoId = button.getAttribute('data-id');

                const response = await fetch('delete_task.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: todoId, type: 'todo' })
                });

                if (response.ok) {
                    showAlert('Main task was successfully deleted!', 'success');
                    setTimeout(() => location.reload(), 1000);
                }
            });
        });

        // Delete Subtask
        document.querySelectorAll('.delete-subtask').forEach(button => {
            button.addEventListener('click', async (e) => {
                const subtaskId = button.getAttribute('data-id');

                const response = await fetch('delete_task.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: subtaskId, type: 'subtask' })
                });

                if (response.ok) {
                    showAlert('Subtask was successfully deleted!', 'success');
                    setTimeout(() => location.reload(), 1000);
                }
            });
        });

        // Toggle Subtask Status
        document.querySelectorAll('.status-toggle').forEach(button => {
            button.addEventListener('click', async (e) => {
                const subtaskId = button.closest('li').getAttribute('data-subtask-id');
                const response = await fetch('update_task.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: subtaskId })
                });

                if (response.ok) {
                    const result = await response.json();
                    if (result.success) {
                        const message = result.newStatus === 'Completed'
                            ? 'Subtask marked as completed!'
                            : 'Subtask set back to pending.';
                        showAlert(message, 'success');

                        // Reload the page to reflect changes
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showAlert('Failed to update subtask status.', 'error');
                    }
                } else {
                    showAlert('Error updating subtask status.', 'error');
                }
            });
        });
    </script>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</body>

</html>