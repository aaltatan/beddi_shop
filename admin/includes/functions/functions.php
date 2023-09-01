<?php

function pageTitle()
{
    global $page_title;
    echo isset($page_title) ? $page_title : "Default";
}

function redirect($msg, $page, $sec = 3, $type = "danger")
{
    $page = $page === "back" ? $_SERVER["HTTP_REFERER"] : $page;
    $msg .= ", You will be redirected to " . $page . " after $sec seconds";
    echo "<div id='info-msg-container'><div id='info-msg' class='" . $type . "'>" . $msg . "</div></div>";
    header("refresh:$sec;url=$page");
    exit();
}

function getCount($tbl, $where = "")
{
    global $conn;
    if ($where === "") {
        $query = "SELECT COUNT(*) FROM " . $tbl;
    } else {
        $query = "SELECT COUNT(*) FROM " . $tbl . " WHERE " . $where;
    }
    $stmt = $conn->prepare($query);
    $stmt->execute();
    return number_format($stmt->fetch()[0]);
}

function hasDependencies($tbl, $where)
{
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) FROM " . $tbl . " WHERE " . $where);
    $stmt->execute();
    return $stmt->fetch()[0] > 0;
}
