<?php

function pageTitle()
{
    global $page_title;
    echo isset($page_title) ? $page_title : "Default";
}

function redirect($msg, $page, $sec = 3, $type = "danger")
{
    $msg .= ", You will be redirected to " . $page . " after $sec seconds";
    echo "<div id='info-msg-container'><div id='info-msg' class='" . $type . "'>" . $msg . "</div></div>";
    header("refresh:$sec;url=$page");
    exit();
}
