<?php

ob_start();

include "../admin/connect.php";

$do = isset($_GET["do"]) ? $_GET["do"] : "Manage";



switch ($do) {

    case "Manage":
        break;

    case "Get":
        $userid = (int) $_GET["userid"];
        $stmt = $conn->prepare("SELECT 
                                    items.item_id,
                                    items.item_name,
                                    items.item_price,
                                    items.offer_price,
                                    cart.quantity,
                                    (
                                        SELECT img 
                                        FROM items_images 
                                        WHERE items_images.item_id = items.item_id 
                                        LIMIT 1
                                    ) AS img
                                FROM 
                                    cart
                                LEFT JOIN
                                    items
                                ON
                                    items.item_id = cart.item_id
                                WHERE 
                                    cart.user_id = ?
                                ");
        $stmt->execute(array($userid));
        $data = $stmt->fetchAll();
        header("Content-Type: application/json");
        echo json_encode($data);
        break;

    case "Add":
        $userid = (int) $_GET["userid"];
        $itemid = (int) $_GET["itemid"];
        $stmt = $conn->prepare("SELECT item_id FROM cart WHERE user_id = ? AND item_id = ?");
        $stmt->execute(array($userid, $itemid));
        $count = $stmt->rowCount();
        if ($count === 0) {
            $stmt = $conn->prepare("INSERT INTO cart(user_id,item_id) VALUES (?,?)");
            $stmt->execute(array($userid, $itemid));
        }
        break;

    case "Delete":
        $userid = (int) $_GET["userid"];
        $itemid = (int) $_GET["itemid"];
        $stmt = $conn->prepare("SELECT item_id FROM cart WHERE user_id = ? AND item_id = ? LIMIT 1");
        $stmt->execute(array($userid, $itemid));
        $count = $stmt->rowCount();
        if ($count > 0) {
            $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND item_id = ?");
            $stmt->execute(array($userid, $itemid));
        }
        break;

    case "Plus":
        $userid = (int) $_GET["userid"];
        $itemid = (int) $_GET["itemid"];
        $stmt = $conn->prepare("SELECT item_id,quantity FROM cart WHERE user_id = ? AND item_id = ? LIMIT 1");
        $stmt->execute(array($userid, $itemid));
        $count = $stmt->rowCount();
        $quantity = $stmt->fetch()["quantity"];
        if ($count > 0) {
            $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND item_id = ? ");
            $stmt->execute(array(++$quantity, $userid, $itemid));
        }
        break;

    case "Minus":
        $userid = (int) $_GET["userid"];
        $itemid = (int) $_GET["itemid"];
        $stmt = $conn->prepare("SELECT item_id,quantity FROM cart WHERE user_id = ? AND item_id = ? LIMIT 1");
        $stmt->execute(array($userid, $itemid));
        $count = $stmt->rowCount();
        $quantity = $stmt->fetch()["quantity"];
        if ($count > 0 && $quantity >= 2) {
            $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND item_id = ? ");
            $stmt->execute(array(--$quantity, $userid, $itemid));
        }
        break;
}

ob_end_flush();
