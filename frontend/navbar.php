<div id="navbar">
    <div>
        
        <?php
        // CWE-209: Generation of Error Message Containing Sensitive Information
        error_reporting(E_ERROR | E_PARSE);
        ini_set('display_errors', 0);
        if (!isset($_SESSION["privilege"])) {
            // user not logged in
            echo '<a href="./login/index.php"><button class="navbarbutton">Log in</button></a>';
        } else {
            // user is logged in
            echo '<a href="/logout"><button class="navbarbutton">Log out</button></a>';
            echo '<a href="/profile"><button class="navbarbutton"><img src="./images/profile.png" class="navbaricon"></button></a>';
            echo '<a href="/comment"><button class="navbarbutton"><img src="./images/comment.png" class="navbaricon"></button></a>';
            if ($_SESSION["privilege"] == "admin") {
                // User is an admin
                echo '<a href="/admin/"><button class="navbarbutton">Admin</button></a>';
            } 
            if ($_SESSION["privilege"] == "user") {
                // user is a standard user
                echo '<a href="/cart"><button class="navbarbutton"><img src="./images/cart.png" class="navbaricon"></button></a>';
            }
        }
        ?>
        
        <form action="/" method="post">
            <button class="navbarbutton" type="submit">Search</button>
            <input type="text" id="navbar_search" name="search_query" placeholder="Search for products">
        </form>
        <a href="/"><img src="/images/LOGO.png" alt="" style="height:20%; width: 20%;"></a>
    </div>
</div>