<?php
session_start();
$page_title = "Dashboard";
if (isset($_SESSION["username"])) {
    include "init.php";
    include $tpl . "aside.php";
?>

    <div class="container">
        <h1>Dashboard</h1>
    </div>


<?php

    include $tpl . "footer.php";
} else {
    header("Location: index.php");
    exit();
}
