<?php

ob_start();

include "init.php";

$do = isset($_GET["do"]) ? $_GET["do"] : "Manage";

switch ($do) {
    case "Manage":
        header("Location: index.php");
        exit();
        break;

    case "Like":
        $user_id = (int) $_GET["userid"];
        $item_id = (int) $_GET["itemid"];

        $stmt = $conn->prepare("SELECT user_id FROM users WHERE user_id = ? LIMIT 1");
        $stmt->execute(array($user_id));
        $user_exists = $stmt->rowCount();

        $stmt = $conn->prepare("SELECT item_id FROM items WHERE item_id = ? LIMIT 1");
        $stmt->execute(array($item_id));
        $item_exists = $stmt->rowCount();

        if ($user_exists && $item_exists) {
            $stmt = $conn->prepare("INSERT INTO items_likes VALUES (?,?)");
            $stmt->execute(array($item_id, $user_id));
        } else {
            header("Location: index.php");
            exit();
        }

        break;

    case "Unlike":
        $user_id = (int) $_GET["userid"];
        $item_id = (int) $_GET["itemid"];

        $stmt = $conn->prepare("SELECT user_id FROM users WHERE user_id = ? LIMIT 1");
        $stmt->execute(array($user_id));
        $user_exists = $stmt->rowCount();

        $stmt = $conn->prepare("SELECT item_id FROM items WHERE item_id = ? LIMIT 1");
        $stmt->execute(array($item_id));
        $item_exists = $stmt->rowCount();

        if ($user_exists && $item_exists) {
            $stmt = $conn->prepare("DELETE FROM items_likes WHERE user_id = ? AND item_id = ?");
            $stmt->execute(array($user_id, $item_id));
        } else {
            header("Location: index.php");
            exit();
        }

        break;
}

ob_end_flush();
