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
                header("Location: index.php");
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

        <!-- Email Field -->
        <div class="wave-group">
            <input required type="email" name="email" class="input" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            <span class="bar"></span>
            <label class="label">
                <?php
                $emailLabel = str_split("Email");
                foreach ($emailLabel as $index => $char) {
                    echo "<span class='label-char' style='--index: $index;'>$char</span>";
                }
                ?>
            </label>
        </div>

        <!-- Password Field -->
        <div class="wave-group">
            <input required type="password" name="password" class="input">
            <span class="bar"></span>
            <label class="label">
                <?php
                $passwordLabel = str_split("Password");
                foreach ($passwordLabel as $index => $char) {
                    echo "<span class='label-char' style='--index: $index;'>$char</span>";
                }
                ?>
            </label>
        </div>


        <button type="submit">Login</button>
        <p>Don't have an account? <a href="registration.php">Register here</a></p>
    </form>

    <script>
        document.querySelectorAll('.input').forEach(input => {
            input.addEventListener('input', () => {
                if (input.value.trim() !== '') {
                    input.classList.add('filled');
                } else {
                    input.classList.remove('filled');
                }
            });
        });
    </script>
</body>

</html>