<?php

ob_start();

session_start();

$page_title = "Categories";

if (isset($_SESSION["username"])) {

    include "init.php";
    include $tpl . "aside.php";

    $do = isset($_GET["do"]) ? $_GET["do"] : "Manage";

    switch ($do) {
        case "Manage";
            break;
        case "Add";
            break;
        case "Insert";
            break;
        case "Edit";
            break;
        case "Delete";
            break;
        case "Update";
            break;
        case "Activate";
            break;
        case "Deactivate";
            break;
    }

    include  $tpl . "footer.php";
} else {
    header("Location: index.php");
    exit;
}

ob_end_flush();
