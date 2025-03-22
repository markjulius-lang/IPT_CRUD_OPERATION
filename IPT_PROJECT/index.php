<?php
session_start();

$tasksFile = 'tasks.json';
$tasks = file_exists($tasksFile) ? json_decode(file_get_contents($tasksFile), true) : [];

if (!is_array($tasks)) {
    $tasks = [];
}

function generateId()
{
    global $tasks;
    return count($tasks) + 1; // Sequential numbering starting from 1
}

// Handle Delete Task
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $tasks = array_values(array_filter($tasks, fn($task) => isset($task['id']) && $task['id'] != $id));

    // Renumber remaining tasks to ensure sequential IDs
    foreach ($tasks as $index => &$task) {
        $task['id'] = $index + 1;
    }

    file_put_contents($tasksFile, json_encode($tasks, JSON_PRETTY_PRINT));
    $_SESSION['message'] = "Task deleted successfully!";
    header("Location: index.php");
    exit;
}

// Handle Add Task
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['task']) && !isset($_POST['edit_id'])) {
    // Ensure the 'due_date' field is properly set
    $due_date = isset($_POST['due_date']) ? $_POST['due_date'] : null;

    $newTask = [
        'id' => generateId(),
        'task' => htmlspecialchars($_POST['task']),
        'created_at' => date('Y-m-d H:i:s'), // Automatically set the creation date
        'due_date' => $due_date // Use the user-defined due date
    ];

    // Add the new task to the task list
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
            $t['due_date'] = $_POST['due_date'];
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager (JSON)</title>
    <link rel="stylesheet" href="index.css">
</head>

<body>

    <div class="container">
        <h2>Task Manager</h2>

        <!-- Message Box (Pop-up style) -->
        <div id="messageBox" class="message"><?= $_SESSION['message'] ?? '' ?></div>
        <?php unset($_SESSION['message']); ?>

        <!-- Add or Edit Task Form -->
        <form method="POST">
            <input type="text" name="task" class="input" placeholder="Enter Task" value="<?= $editTask['task'] ?? '' ?>" required>

            <!-- Due Date Field -->
            <div class="wave-group">
                <input type="date" name="due_date" class="input" value="<?= $editTask['due_date'] ?? '' ?>" required>
                <label class="label"></label>
            </div>

            <?php if ($editTask): ?>
                <input type="hidden" name="edit_id" value="<?= $editTask['id'] ?>">
                <button type="submit" class="btn btn-submit"><span>&#9998;</span> Update Task</button>
                <a href="index.php" class="btn btn-cancel">Cancel</a>
            <?php else: ?>
                <button type="submit" class="btn btn-submit"><span>&#10133;</span> Add Task</button>
            <?php endif; ?>
        </form>

        <!-- Task List -->
        <!-- Task List -->
        <table>
            <tr>
                <th>ID</th>
                <th>Task</th>
                <th>Created At</th>
                <th>Due Date</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($tasks as $task): ?>
                <?php if (isset($task['id']) && isset($task['task'])): ?>
                    <tr>
                        <td><?= htmlspecialchars($task['id']) ?></td>
                        <td><?= htmlspecialchars($task['task']) ?></td>
                        <td><?= isset($task['created_at']) ? htmlspecialchars($task['created_at']) : 'N/A' ?></td>
                        <td><?= isset($task['due_date']) ? htmlspecialchars($task['due_date']) : 'N/A' ?></td>
                        <td>
                            <a href="index.php?edit=<?= $task['id'] ?>" class="btn btn-edit"><span>&#9998;</span></a>
                            <a href="index.php?delete=<?= $task['id'] ?>" class="btn btn-delete" onclick="return confirmDelete()"><span>&#128465;</span></a>
                            <a href="javascript:void(0);" class="btn btn-view" data-task-id="<?= $task['id'] ?>"><span>&#128065;</span></a>
                        </td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </table>

    </div>

    <!-- Modal for Viewing Task Details -->
    <div id="viewModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Task Details</h2>
            <p id="taskDetails">Loading...</p>
        </div>
    </div>


    <script>
        // Display message box with animation when it's set
        window.onload = function() {
            let messageBox = document.getElementById("messageBox");
            if (messageBox.innerText.trim() !== "") {
                messageBox.style.display = "block";
                messageBox.classList.add("show");
                setTimeout(() => {
                    messageBox.classList.remove("show");
                    messageBox.style.display = "none";
                }, 3000); // Hide after 3 seconds
            }
        };

        // Confirm before deleting a task
        function confirmDelete() {
            return confirm("Are you sure you want to delete this task?");
        }

        // Open the modal to view task details
        function openModal(taskId) {
            const task = tasks.find(t => t.id == taskId);
            if (task) {
                // Update modal content to show task details, including dates
                document.getElementById("taskDetails").innerHTML = `
            <strong>Task:</strong> ${task.task}<br>
            <strong>Created At:</strong> ${task.created_at}<br>
            <strong>Due Date:</strong> ${task.due_date}`;

                // Show the modal
                document.getElementById("viewModal").style.display = "block";
            }
        }


        // Close the modal
        function closeModal() {
            document.getElementById("viewModal").style.display = "none";
        }

        // Attach the openModal function to View buttons
        document.querySelectorAll('.btn-view').forEach(button => {
            button.addEventListener('click', (event) => {
                const taskId = event.target.getAttribute('data-task-id');
                openModal(taskId);
            });
        });

        // Tasks array passed from PHP to JavaScript
        let tasks = <?php echo json_encode($tasks); ?>;
    </script>

</body>

</html>