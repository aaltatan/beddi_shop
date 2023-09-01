<?php

$page_title = "Categories";
include "init.php";

// Content
echo "Welcome World!  <br>" . $_GET["id"] . " " . $_GET["catname"];

include $tpl . "footer.php";
