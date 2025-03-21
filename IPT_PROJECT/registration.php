<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirm_password"];

    if ($password !== $confirmPassword) {
        $error = "Passwords do not match!";
    } else {
        $file = "users.json";

        if (file_exists($file)) {
            $users = json_decode(file_get_contents($file), true);
        } else {
            $users = [];
        }

        // Check if email already exists
        $emailExists = false;
        foreach ($users as $user) {
            if ($user["email"] == $email) {
                $emailExists = true;
                break;
            }
        }

        if ($emailExists) {
            $error = "Email already registered!";
        } else {
            // Store new user
            $newUser = [
                "name" => $name,
                "email" => $email,
                "password" => password_hash($password, PASSWORD_DEFAULT)
            ];

            $users[] = $newUser;
            file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT));

            $_SESSION["message"] = "Registration successful!";
            $_SESSION["message_type"] = "success"; // Success type
            header("Location: login.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <form method="POST" action="">
        <h2>Registration</h2>

        <?php if (isset($error)) { ?>
            <p class="error"><?php echo $error; ?></p>
        <?php } ?>

        <?php if (isset($_SESSION["message"])) { ?>
            <p class="<?php echo $_SESSION['message_type']; ?>"><?php echo $_SESSION["message"]; ?></p>
            <?php unset($_SESSION["message"]);
            unset($_SESSION["message_type"]); ?>
        <?php } ?>

        <?php
        $fields = [
            "name" => "Full Name",
            "email" => "Email",
            "password" => "Password",
            "confirm_password" => "Re-enter Password"
        ];
        foreach ($fields as $name => $label) { ?>
            <div class="wave-group">
                <input required type="<?php echo ($name == 'email' || $name == 'name') ? 'text' : 'password'; ?>" name="<?php echo $name; ?>" class="input">
                <span class="bar"></span>
                <label class="label">
                    <?php foreach (str_split($label) as $index => $char) { ?>
                        <span class="label-char" style="--index: <?php echo $index; ?>;"> <?php echo $char; ?> </span>
                    <?php } ?>
                </label>
            </div>
        <?php } ?>

        <button type="submit">Create</button>
        <p>Already have an account? <a href="login.php">Login here</a></p>
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