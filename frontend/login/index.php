<?php
include "../init-timeout.php";
include "../init-error.php";
include "../sql_con.php";

$error = ""; // Variable to store the error message

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST["username"];
    $password = hash("MD5", $_POST["password"], FALSE);
    $query = $con->prepare("SELECT * FROM `users` WHERE `username` = ?");
    $query->bind_param('s', $username);
    
    if ($query->execute()) {
        $result = $query->get_result();
        $user = $result->fetch_assoc();
        
        if (strcmp($password, $user["password"]) == 0) {

            //CWE-312 secure
            $ciphering = "AES-256-GCM";
            $iv_length = openssl_cipher_iv_length($ciphering);
            $encryption_key = random_bytes($iv_length); // Use random_bytes for a secure key
            $encryption_iv = openssl_random_pseudo_bytes($iv_length); // Use openssl_random_pseudo_bytes for a secure IV
            $options = OPENSSL_RAW_DATA;
            
            // encrypting the UID value
            $encryption = openssl_encrypt($user["user_id"], $ciphering, $encryption_key, $options, $encryption_iv, $tagID); // Include the $tag variable to store the authentication tag
            $encryptedID = base64_encode($encryption);
            
            // encrypting the privilege value
            $encryption_priv = openssl_encrypt($user["privilege"], $ciphering, $encryption_key, $options, $encryption_iv, $tagPRIV); 
            $encrypted_priv = base64_encode($encryption_priv);

            $_SESSION['encryptionKey'] = $encryption_key;
            $_SESSION['encryptionIv'] = $encryption_iv;
            $_SESSION['authenticationTagID'] = $tagID;
            $_SESSION['authenticationTagPRIV'] = $tagPRIV;
            $_SESSION["user_id"] = $user["user_id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["privilege"] = $user["privilege"];
            setcookie("privilege", $encrypted_priv, 0, "/");

            $_SESSION["iwantdie"] = $encryptedID;
            
            echo '<script>sessionStorage.setItem("UID", "' . $encryptedID . '");</script>'; //stores encrypted UID of the user in the sessionstorage
            
            date_default_timezone_set('Singapore');
            $date = date('y-m-d h:i:s');
            $logName = $_SESSION['username'];
            $auditRole = $_SESSION['privilege'];
            $auditActivity = 'Account logged in';
            $auditquery = $con->prepare("INSERT INTO audit_trail (audit_username, audit_role, audit_datetime, audit_activity) VALUES (?,?,?,?)"); //audit logs of users logging in
            $auditquery->bind_param('ssss', $logName, $auditRole, $date, $auditActivity);
            if ($auditquery->execute()) {
                $con->close();
                // echo "Audit log has been captured";
                // Redirect to the homepage or any other authenticated page
                echo "<script>window.location.href='/index.php'</script>";
            }
        } else {
            $error = "Invalid username or password.";
            date_default_timezone_set('Singapore'); //set date and time of log
            $date = date('d-m-y h:i:s');
            $logName = "NIL";
            $auditRole = "NIL";
            $auditActivity = "Failed to Login with username: $username";

            $auditquery = $con->prepare("INSERT INTO audit_trail (audit_username, audit_role, audit_datetime, audit_activity) VALUES (?,?,?,?)"); //audit logs of users logging in
            $auditquery->bind_param('ssss', $logName, $auditRole, $date, $auditActivity);
            $auditquery->execute();
        }
    } else {
        $error = "Error executing query.";
        $con->close();
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="/design.css">
</head>

<body>
    <?php include "../navbar.php"; ?>

    <div class="container">

        <div class="login_card">
            <h1>LOGIN</h1>
            <form method="POST" action=".">
                <!-- <form method="POST" action="test.php"> -->
                <input type="text" placeholder="Username" name="username" required>
                <input type="password" placeholder="Password" name="password" required>
                <input type="submit" value="Login" class="button" name="form_submit">
            </form>

            <?php
            if (!empty($error)) {
                echo '<p class="error">' . $error . '</p>';
            }
            ?>

            <div class="forgor">
                <span>Not Registered? <a href="/register">Register Here</a></span><br />
                <span><a href="/login/resetpw">Forgot Password?</a></span>
            </div>
        </div>
    </div>
</body>

</html>