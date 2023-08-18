<?php

session_start();
if (isset($_SESSION["username"])) {
    $page_title = 'Members';
    include "init.php";
    include $tpl . "aside.php";

    // the content

    $do = isset($_GET['do']) ? $_GET['do'] : "Manage";

    switch ($do) {
        case "Manage":
            echo "Welcome to Manage main Page <br>";
            break;
        case "Edit":
            // Edit Page Content
?>

            <form action="" method="post" class="form flow">
                <h1>Edit Member Information</h1>
                <ul class="msgs">
                    <li>UserName must be ........</li>
                    <li>UserName must be ........</li>
                    <li>UserName must be ........</li>
                    <li>UserName must be ........</li>
                    <li>UserName must be ........</li>
                </ul>
                <div class="inputs">
                    <div class="form-input">
                        <input type="text" name="username" id="edit-username" placeholder="username" autocomplete="off">
                        <label for="edit-username">Username</label>
                    </div>
                    <div class="form-input">
                        <input type="email" name="email" id="edit-email" placeholder="Email">
                        <label for="edit-email">Email</label>
                    </div>
                    <div class="form-input">
                        <input type="password" name="password" id="edit-password" placeholder="password" autocomplete="new-password">
                        <label for="edit-password">Password</label>
                    </div>
                    <div class="form-input">
                        <input type="password" name="repassword" id="edit-repeat-password" placeholder="repeat you password" autocomplete="new-password">
                        <label for="edit-repeat-password">re-Password</label>
                    </div>
                    <div class="form-input">
                        <input type="text" name="fullname" id="edit-fullname" placeholder="Full Name">
                        <label for="edit-fullname">Full Name</label>
                    </div>
                </div>
                <input type="submit" value="Update" class="btn btn-primary">
            </form>

<?php
            break;
        default:
            echo "there is no page like $do";
    }

    include $tpl . "footer.php";
} else {
    header("Location: index.php");
    exit();
}
