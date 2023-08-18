<?php
session_start();
$page_title = "Dashboard";
if (isset($_SESSION["username"])) {
    include "init.php";
    include $tpl . "aside.php";

    // The Content

    include $tpl . "footer.php";
} else {
    header("Location: index.php");
    exit();
}
