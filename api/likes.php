<?php

ob_start();
session_start();

include "../admin/connect.php";

if (isset($_SESSION["user_session_id"])) {

    $do = isset($_GET["do"]) ? $_GET["do"] : "Manage";
    $user_id = $_SESSION["user_session_id"];

    switch ($do) {

        case "Manage":
            header("Location: index.php");
            exit();
            break;

        case "Get":
            $item_id = (int) $_GET["itemid"];

            $stmt = $conn->prepare("SELECT 
                                        users.user_id,
                                        users.full_name
                                    FROM
                                        items_likes
                                    LEFT JOIN
                                        users
                                    ON
                                        items_likes.user_id = users.user_id
                                    WHERE
                                        items_likes.item_id = ?
            ");
            $stmt->execute(array($item_id));
            $data = $stmt->fetchAll();
            $likers = array_map(fn ($arr) => $arr["user_id"] === $user_id ? "You" : $arr["full_name"], $data);

            header("Content-Type: application/json");
            echo json_encode($likers);

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
} else {
    header("Location: index.php");
    exit();
}

ob_end_flush();
