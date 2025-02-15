<?php
session_start();
include "../init-error.php";
if (!isset($_SESSION["user_id"])) {
    header("Location: ../");
}

// Check if the user is logged in
if (isset($_SESSION["user_id"]) && isset($_SESSION["username"])) {
    include "../sql_con.php";
    $date = date('d-m-y h:i:s');
    $logName = $_SESSION["username"];
    $logRole = $_SESSION["privilege"];
    $logActivity = "Account logged out";

    $logquery = $con->prepare("INSERT INTO audit_trail (audit_username, audit_role, audit_datetime, audit_activity) VALUES (?,?,?,?)"); //audit logs of users logging in
    $logquery->bind_param('ssss', $logName, $logRole, $date, $logActivity);
    if ($logquery->execute()) {
        $con->close();
        // Unset all of the session variables
        $_SESSION = array();

        // Destroy the session
        session_destroy();
    }
    setcookie("privilege", "", 0, "/");
    echo "<script>sessionStorage.clear()</script>";
    echo "<script>window.location.href='/'</script>";

}
exit();
?>