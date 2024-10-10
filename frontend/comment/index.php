<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message Wall</title>
    <link rel="stylesheet" type="text/css" href="/design.css">
</head>

<body>
    <?php include "../navbar.php"; ?>

    <section class="message-wall-container">
        <div class="feedback-cell">
            <h1>Leave a message for us!</h1>
            <form action="post_comment.php" method="post" id="comment_form">
                <textarea name="comment_content" rows="15" placeholder="Write your comment here..." form="comment_form"></textarea>
                <input type="submit" value="Post comment" class="button" name="form_submit">
            </form>
        </div>

        <?php
        
        include "../sql_con.php";

        $query = $con->prepare('SELECT `c`.*, `u`.`username`, `u`.`profilepic` FROM `users` u INNER JOIN `comments` c ON `c`.`user_id` = `u`.`user_id` ORDER BY c.comment_id DESC;');

        if ($query->execute()) {

            $query->bind_result($comment_id, $user_id, $comment, $post_date, $username, $profilepic);
            $query->store_result();

        echo '<div class="commentsection">';

        // comments section
        while ($query->fetch()) {
            echo '<div class="comment">';
            echo '<h4>' . htmlspecialchars($username) . '</h4>';
            echo '<p>' . htmlspecialchars($comment_text) . '</p>';
            echo '<time>' . date('F j, Y, g:i a', strtotime($created_at)) . '</time>';
            echo '</div>';
        }

        echo '<div class="commentscontainer">';
            echo '<div class="column-container"><h1>Previous Comments<hr></h1>';

            while ($query->fetch()) {
                echo '<div class="comment_cell"><div class="comment_cell_left">';
                
                if (empty($profilepic)) {
                    echo '<img src="./images/profile-user.png" alt="">';
                } else {
                    echo '<img src="./images/user_profiles/'.$profilepic.'" alt="">';
                }
                
                
                echo '<h4>'.$username.'</h4></div><div class="comment_cell_right"><p>'.$comment.'</p><div class="date_posted"><span>';

                if (isset($_SESSION["user_id"]) && $user_id == $_SESSION["user_id"]) {
                    echo '<a href="delete_comment.php?com_id='.$comment_id.'">Delete this comment</a>';
                }


                echo 'Posted on '.$post_date.'</span></div></div></div>';
                
            }

            echo '</div>';


        } else {
            echo "Error executing query.";
        }

        $con->close();
        

        ?>
    </section>

</body>

</html>