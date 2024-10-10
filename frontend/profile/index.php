<?php
include "../init-timeout.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../");
}

include "../init-error.php";

include "../sql_con.php";

$errorMsg = "";
$profileUpdated = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_SESSION["user_id"];
    $newUsername = $_POST["username"];
    $newPassword = $_POST["password"];
    $newEmail = $_POST["email"];
    $oldPassword = $_POST["oldPassword"];


    $hashedPassword = hash("MD5", $newPassword, FALSE);
   
    $verificationQuery = $con->prepare("SELECT password FROM `users` WHERE user_id = ?");
    $verificationQuery->bind_param('i', $userId);
    $verificationQuery->execute();
    $verificationQuery->bind_result($setPassword);
    $verificationQuery->fetch();
    $verificationQuery->close();

    $oldpasswordhash = hash("MD5", $oldPassword, FALSE);

    if (strcmp($oldpasswordhash, $setPassword) == 0) {
        // Check if the new username is unique
        $checkUsernameQuery = $con->prepare("SELECT * FROM `users` WHERE `username` = ? AND `user_id` != ?");
        $checkUsernameQuery->bind_param('si', $newUsername, $userId);
        $checkUsernameQuery->execute();
        $usernameResult = $checkUsernameQuery->get_result();

        // Check if the new email is unique
        $checkEmailQuery = $con->prepare("SELECT * FROM `users` WHERE `email` = ? AND `user_id` != ?");
        $checkEmailQuery->bind_param('si', $newEmail, $userId);
        $checkEmailQuery->execute();
        $emailResult = $checkEmailQuery->get_result();

        if ($usernameResult->num_rows > 0) {
            $errorMsg = "Username already exists.";
        } elseif ($emailResult->num_rows > 0) {
            $errorMsg = "Email already exists.";
        } else {
            // CWE-521: Weak Password Requirements
            $usernameCheck = preg_match('/^[a-z0-9_]+$/i', $newUsername);
            $passwordCheck = preg_match('/^(?=.*[a-z])(?=.*[A-Z]).{8,}$/', $newPassword);
            $emailCheck = preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/', $newEmail);

            if ($newUsername !== "") {
                if ($usernameCheck) {
                    $updateNameQuery = $con->prepare("UPDATE `users` SET `username` = ? WHERE `user_id` = ?");
                    $updateNameQuery->bind_param('si', $newUsername, $userId);
                    $updateNameQuery->execute();
                    $updateNameQuery->close();
                    $updateUsername = "Username has been updated";
                } elseif (!empty($newUsername)) {
                    $updateUsername = "ONLY ALPHABETS, NUMBERS AND UNDERSCORES";
                }
            }
            if ($newPassword !== "") {
                if ($passwordCheck) {
                    $updatePasswordQuery = $con->prepare("UPDATE `users` SET `password` = ? WHERE `user_id` = ?");
                    $updatePasswordQuery->bind_param('si', $hashedPassword, $userId);
                    $updatePasswordQuery->execute();
                    $updatePasswordQuery->close();
                    $updatePassword = "Password has been updated";
                } elseif (!empty($newPassword)) {
                    $updatePassword = "AT LEAST 8 CHARACTERS, CAPITAL LETTER AND SMALL LETTER";
                }
            }
            if ($newEmail !== "") {
                if ($emailCheck) {
                    $updateEmailQuery = $con->prepare("UPDATE `users` SET `email` = ? WHERE `user_id` = ?");
                    $updateEmailQuery->bind_param('si', $newEmail, $userId);
                    $updateEmailQuery->execute();
                    $updateEmailQuery->close();
                    $updateEmail = "Email has been updated";
                } elseif (!empty($newEmail)) {
                    $updateEmail = "ENSURE EMAIL IS PROPERLY FORMATTED";
                }
            }

        }

        // Handle profile picture upload
        // CWE-434: Unrestricted Upload of File with Dangerous Type; this is for 2nd flag
        $profilepic = $_FILES["profile_picture"];
        if (!empty($profilepic)) {
            $fileName = $profilepic["name"];
            $fileTmpName = $profilepic["tmp_name"];
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
            $allowedExtensions = ["jpg", "jpeg", "png"];
            $flagExtension = ["php", "exe", "js", "sh"];

            if (!empty($fileName) && in_array(strtolower($fileExtension), $allowedExtensions)) {
                $newFileName = $newUsername . "_profile_pic." . $fileExtension;
                $fileDestination = "../images/user_profiles/" . $newFileName; // path to directory relative from current position
                            
                if (move_uploaded_file($fileTmpName, $fileDestination)) {
                    $updatePictureQuery = $con->prepare("UPDATE `users` SET `profilepic` = ? WHERE `user_id` = ?");
                    $updatePictureQuery->bind_param('si', $newFileName, $userId);
                    $updatePictureQuery->execute();
                    $updatePictureQuery->close();
                    
                    $updatePic = "Profile Picture has been updated";
                }

            // if file is malicious, give the 2nd flag and upload it regardless
            } elseif (!empty($fileName) && in_array(strtolower($fileExtension), $flagExtension)) {
                $flag2 = "vigenere, with HAUGHT, says: flag2{vbmokbhn}";
                echo "<script type='text/javascript'>alert('$flag2');</script>";

                $newFileName = $newUsername . "_profile_pic." . $fileExtension;
                $fileDestination = "../images/user_profiles/" . $newFileName; // path to directory relative from current position

                    if (move_uploaded_file($fileTmpName, $fileDestination)) {
                        $updatePictureQuery = $con->prepare("UPDATE `users` SET `profilepic` = ? WHERE `user_id` = ?");
                        $updatePictureQuery->bind_param('si', $newFileName, $userId);
                        $updatePictureQuery->execute();
                        $updatePictureQuery->close();
                        
                        $updatePic = "Profile Picture has been updated";
                    }
            } elseif (!empty($fileName)) {
                $updatePic = "Invalid file type. Only JPG, JPEG, and PNG files are allowed.";
            }
        }
    } else {
        $errorMsg = "Profile update failed. Your old password does not match";
    }
}

// Retrieve user data
$userId = $_SESSION['user_id']; 
$userQuery = $con->prepare("SELECT `user_id`, `username`, `password`, `email`, `profilepic` FROM `users` WHERE `user_id` = ?");
$userQuery->bind_param('i', $userId);
$userQuery->execute();
$userQuery->bind_result($userId, $username, $password, $email, $profilepic);
$userQuery->fetch();
$userQuery->close();

$con->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" type="text/css" href="/design.css">
</head>

<body>

    <?php include "../navbar.php"; ?>

    <div class="container">
        <div class="profile-cell">
            <h1>My Profile</h1><hr>
            <div class="horizontal-container">
                <div class="profile-picture">
                    <span>Profile Picture:</span>
                    <?php
                    if (!empty($profilepic)) {
                        // for HTML elements, path can be from document root (/)
                        echo '<img src="/images/user_profiles/' . $profilepic . '" alt="Profile Picture">';
                    } else {
                        echo '<img src="/images/profile-user.png" alt="Default Profile Picture">';
                    }
                    ?>
                </div>
                <form class="profile-form" method="POST" action="." enctype="multipart/form-data">
                    <label for="username">Username:
                        <?php
                        if (!empty($updateUsername)) {
                            echo '<span class="success">' . $updateUsername . '</span>';
                        }
                        ?>
                    </label>
                    <input type="text" id="username" name="username" value="<?php echo $username; ?>">

                    <label for="password"> Change Password:
                        <?php
                        if (!empty($updatePassword)) {
                            echo '<span class="success">' . $updatePassword . '</span>';
                        }
                        ?>
                    </label>
                    <input type="password" id="password" name="password" placeholder="New Password">

                    <label for="email">Email:
                        <?php
                        if (!empty($updateEmail)) {
                            echo '<span class="success">' . $updateEmail . '</span>';
                        }
                        ?>
                    </label>
                    <input type="email" id="email" name="email" value="<?php echo $email; ?>">
                    
                    <label for="profile_picture">Profile Picture:
                        <?php
                        if (!empty($updatePic)) {
                            echo '<span class="success">' . $updatePic . '</span>';
                        }
                        ?>
                    </label>
                    <input type="file" id="profile_picture" name="profile_picture">
                    
                    <label for="oldPassword"> Password verification: </label>
                    <input type="password" id="oldPassword" name="oldPassword" placeholder="Old Password" required>

                    <button class="profile_update" type="submit">Update Profile</button>
                                    
                </form>
            </div>
        </div>
        <?php
        if (!empty($errorMsg)) {
            echo '<p align="center" class="success">' . $errorMsg . '</p>';
        }
        ?>
    </div>
</body>

</html>