<?php
session_start();

$tasksFile = 'tasks.json';
$tasks = file_exists($tasksFile) ? json_decode(file_get_contents($tasksFile), true) : [];

if (!is_array($tasks)) {
    $tasks = [];
}

function generateId() {
    return bin2hex(random_bytes(6)); // 12-character unique ID
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $tasks = array_values(array_filter($tasks, fn($task) => isset($task['id']) && $task['id'] != $id));
    file_put_contents($tasksFile, json_encode($tasks, JSON_PRETTY_PRINT));
    $_SESSION['message'] = "Task deleted successfully!";
    header("Location: index.php");
    exit;
}

// Handle Add Task
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['task']) && !isset($_POST['edit_id'])) {
    $newTask = [
        'id' => generateId(),
        'task' => htmlspecialchars($_POST['task'])
    ];
    $tasks[] = $newTask;
    file_put_contents($tasksFile, json_encode($tasks, JSON_PRETTY_PRINT));
    $_SESSION['message'] = "Task added successfully!";
    header("Location: index.php");
    exit;
}

// Handle Edit Task
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['task']) && isset($_POST['edit_id'])) {
    foreach ($tasks as &$t) {
        if (isset($t['id']) && $t['id'] == $_POST['edit_id']) {
            $t['task'] = htmlspecialchars($_POST['task']);
            break;
        }
    }
    file_put_contents($tasksFile, json_encode($tasks, JSON_PRETTY_PRINT));
    $_SESSION['message'] = "Task updated successfully!";
    header("Location: index.php");
    exit;
}

// Get Task for Editing
$editTask = null;
if (isset($_GET['edit'])) {
    foreach ($tasks as $t) {
        if (isset($t['id']) && $t['id'] == $_GET['edit']) {
            $editTask = $t;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Task Manager (JSON)</title>
    <style>
        body { font-family: Arial, sans-serif; background: #222; color: white; text-align: center; }
        .container { width: 50%; margin: auto; background: #333; padding: 20px; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; background: #444; margin-top: 10px; }
        th, td { padding: 10px; border: 1px solid #666; text-align: left; }
        th { background: #555; }
        .btn { padding: 5px 10px; text-decoration: none; border-radius: 4px; font-size: 14px; cursor: pointer; border: none; }
        .btn-delete { background: red; color: white; }
        .btn-edit { background: blue; color: white; }
        .btn-submit { background: green; color: white; }
        input[type="text"] { padding: 5px; margin-bottom: 10px; width: 80%; }
        .message { background: green; color: white; padding: 10px; margin-bottom: 10px; display: none; }
    </style>
</head>
<body>

<div class="container">
    <h2>Task Manager (JSON)</h2>

    <div id="messageBox" class="message"><?= $_SESSION['message'] ?? '' ?></div>
    <?php unset($_SESSION['message']); ?>

    <form method="POST">
        <input type="text" name="task" placeholder="Enter Task" value="<?= $editTask['task'] ?? '' ?>" required>
        <?php if ($editTask): ?>
            <input type="hidden" name="edit_id" value="<?= $editTask['id'] ?>">
            <button type="submit" class="btn btn-submit">Update Task</button>
            <a href="index.php" class="btn">Cancel</a>
        <?php else: ?>
            <button type="submit" class="btn btn-submit">Add Task</button>
        <?php endif; ?>
    </form>

    <table>
        <tr>
            <th>ID</th><th>Task</th><th>Edit</th><th>Delete</th>
        </tr>
        <?php foreach ($tasks as $task): ?>
            <?php if (isset($task['id']) && isset($task['task'])): ?>
                <tr>
                    <td><?= htmlspecialchars($task['id']) ?></td>
                    <td><?= htmlspecialchars($task['task']) ?></td>
                    <td><a href="index.php?edit=<?= $task['id'] ?>" class="btn btn-edit">Edit</a></td>
                    <td><a href="index.php?delete=<?= $task['id'] ?>" class="btn btn-delete" onclick="return confirmDelete()">Delete</a></td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
    </table>
</div>

<script>
    // Display message if exists
    window.onload = function() {
        let messageBox = document.getElementById("messageBox");
        if (messageBox.innerText.trim() !== "") {
            messageBox.style.display = "block";
            setTimeout(() => {
                messageBox.style.display = "none";
            }, 3000);
        }
    };

    // Confirm before deleting
    function confirmDelete() {
        return confirm("Are you sure you want to delete this task?");
    }
</script>

</body>
</html>
