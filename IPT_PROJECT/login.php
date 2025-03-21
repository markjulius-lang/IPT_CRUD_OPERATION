<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $file = "users.json";
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
        foreach ($data as $user) {
            if ($user["email"] === $email && password_verify($password, $user["password"])) {
                $_SESSION["user"] = $user["name"];
                header("Location: dashboard.php");
                exit();
            }
        }
        $error = "Invalid email or password!";
    } else {
        $error = "No users registered!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <form method="POST" action="">
        <h2>Login</h2>
        <?php if (isset($error)) {
            echo "<p class='error'>$error</p>";
        } ?>
        <div class="wave-group">
            <input required type="email" name="email" class="input">
            <span class="bar"></span>
            <label class="label">
                <span class="label-char">E</span>
                <span class="label-char">m</span>
                <span class="label-char">a</span>
                <span class="label-char">i</span>
                <span class="label-char">l</span>
            </label>
        </div>
        <div class="wave-group">
            <input required type="password" name="password" class="input">
            <span class="bar"></span>
            <label class="label">
                <span class="label-char">P</span>
                <span class="label-char">a</span>
                <span class="label-char">s</span>
                <span class="label-char">s</span>
                <span class="label-char">w</span>
                <span class="label-char">o</span>
                <span class="label-char">r</span>
                <span class="label-char">d</span>
            </label>
        </div>
        <button type="submit">Login</button>
        <p>Don't have an account? <a href="registration.php">Register here</a></p>
    </form>
</body>

</html>