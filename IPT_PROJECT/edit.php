<?php
$tasksFile = 'tasks.json';
$tasks = json_decode(file_get_contents($tasksFile), true);
$id = $_GET['id'] ?? null;

if (!$id || !($task = array_filter($tasks, fn($t) => $t['id'] == $id)[0] ?? null)) {
    die("Task not found!");
}

// Handle Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['task'])) {
    foreach ($tasks as &$t) {
        if ($t['id'] == $id) {
            $t['task'] = htmlspecialchars($_POST['task']);
            break;
        }
    }
    file_put_contents($tasksFile, json_encode($tasks, JSON_PRETTY_PRINT));
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Task</title>
    <style>
        body { font-family: Arial, sans-serif; background: #222; color: white; text-align: center; }
        .container { width: 50%; margin: auto; background: #333; padding: 20px; border-radius: 8px; }
        input[type="text"] { padding: 5px; margin-bottom: 10px; width: 80%; }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Task</h2>
    <form method="POST">
        <input type="text" name="task" value="<?= htmlspecialchars($task['task']) ?>" required>
        <button type="submit">Update Task</button>
    </form>
    <a href="index.php">Back</a>
</div>

</body>
</html>
