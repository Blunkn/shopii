<?php
include "../init-timeout.php";
include "../init-error.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    include "../sql_con.php";

    $username = $_POST["username"];
    $password = $_POST["password"];
    $email = $_POST["email"];
    $privilege = "user"; // Default privilege for a registered user

    // CWE-20 Improper input validation
    // CWE-521: Weak Password Requirements
    $usernameCheck = preg_match('/^[a-z0-9_]+$/i', $username);
    $passwordCheck = preg_match('/^(?=.*[a-z])(?=.*[A-Z]).{8,}$/', $password);
    $emailCheck = preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/', $email);

    $hashedPassword = hash("md5", $password, FALSE);

    if ($usernameCheck) {
        if ($passwordCheck) {
            if ($emailCheck) {

                // Check if username or email already exists in the database
                $checkUsernameQuery = $con->prepare("SELECT * FROM `users` WHERE `username` = ?");
                $checkUsernameQuery->bind_param('s', $username);
                $checkUsernameQuery->execute();
                $usernameResult = $checkUsernameQuery->get_result();

                $checkEmailQuery = $con->prepare("SELECT * FROM `users` WHERE `email` = ?");
                $checkEmailQuery->bind_param('s', $email);
                $checkEmailQuery->execute();
                $emailResult = $checkEmailQuery->get_result();

                if ($usernameResult->num_rows > 0) {
                    $error = "Username already exists.";
                } elseif ($emailResult->num_rows > 0) {
                    $error .= "Email already exists.";
                } else {
                    $query = $con->prepare("INSERT INTO `users` (`username`, `password`, `email`, `privilege`) VALUES (?, ?, ?, ?)");
                    $query->bind_param('ssss', $username, $hashedPassword, $email, $privilege);

                    if ($query->execute()) {
                        header("Location: /login");
                        exit();
                    } else {
                        $error = "Error registering user.";
                    }
                }
            } elseif (!empty($email)) {
                $error .= "Ensure that your email is properly formatted";
            }
        } elseif (!empty($password)) {
            $error .= "Ensure that your password contains at least 8 characters, a capital letter, and a small letter";
        }
    } elseif (!empty($username)) {
        $error .= "Your username may only contain alphabets and underscores";
    }
    $con->close();
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" type="text/css" href="/design.css">
</head>

<body>
    <?php include "../navbar.php"; ?>

    <div class="container">

        <div class="login_card">
            <h1>REGISTER</h1>
            <form method="POST" action=".">
                <input type="email" placeholder="Email" name="email" required>
                <input type="text" placeholder="Username" name="username" required>
                <input type="password" placeholder="Password" name="password" required>
                <input type="submit" value="Register" class="button" name="form_submit">
            </form>

            <?php
            if (!empty($error)) {
                echo '<p class="error">' . $error . '</p>';
            }
            ?>

            <div class="forgor">
                <span>Have an account? <a href="/login">Login Here</a></span>
            </div>
        </div>
    </div>


</body>

</html>