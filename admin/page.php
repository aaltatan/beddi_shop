<?php


$do = isset($_GET['do']) ? $_GET['do'] : "Manage";

switch ($do) {
    case "Manage":
        echo "Welcome to Manage main Page <br>";
        echo "<a href='page.php?do=Add'>Add Page</a>";
        break;
    case "Add":
        echo "Welcome to Add Page";
        break;
    case "Insert":
        echo "Welcome to Insert Page";
        break;
    default:
        echo "there is no page like $do";
}
