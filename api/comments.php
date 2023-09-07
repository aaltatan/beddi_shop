<?php

ob_start();
session_start();

include "../admin/connect.php";


$do = isset($_GET["do"]) ? $_GET["do"] : "Manage";
$item_id = isset($_GET["itemid"]) && is_numeric($_GET["itemid"]) ? $_GET["itemid"] : 0;

$stmt = $conn->prepare("SELECT item_id FROM items WHERE acceptable = 1 AND available = 1 AND item_id = ? LIMIT 1");
$stmt->execute(array($item_id));
$count = $stmt->rowCount();

if ($count) {

    switch ($do) {

        case "Manage":
            header("Location: index.php");
            exit();
            break;

        case "Get":
            $stmt = $conn->prepare("SELECT 
                                            users.user_id,
                                            users.full_name,
                                            comments.comment_id,
                                            comments.added_date,
                                            comments.comment
                                        FROM
                                            comments
                                        LEFT JOIN
                                            users
                                        ON
                                            users.user_id = comments.user_id
                                        LEFT JOIN
                                            items
                                        ON
                                            items.item_id = comments.item_id
                                        LEFT JOIN
                                            categories
                                        ON
                                            categories.id = items.cat_id
                                        WHERE
                                            categories.allow_comment = 1
                                        AND
                                            comments.item_id = ?
                                        AND
                                            comments.comment_status = 1
                                        ORDER BY
                                            comments.added_date
                                        DESC
                ");
            $stmt->execute(array($item_id));
            $data = $stmt->fetchAll();
            header("Content-type: application/json");
            echo json_encode($data);
            break;

        case "Add":
            $user_id = $_SESSION["user_session_id"];
            $stmt = $conn->prepare("INSERT INTO comments(comment,item_id,user_id,comment_status) VALUES (?,?,?,1)");
            $stmt->execute(array($_GET["comment"], $item_id, $user_id));
            break;

        case "Delete":
            $stmt = $conn->prepare("DELETE FROM comments WHERE comment_id = ?");
            $stmt->execute(array($_GET["comment"]));
            break;
    }
} else {
    header("Location: index.php");
    exit();
}


ob_end_flush();
