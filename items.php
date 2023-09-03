<?php

ob_start();

session_start();

$item_id = is_numeric($_GET["id"]) ? $_GET["id"] : 0;

$page_title = "Beddi Shop";
include "init.php";

$stmt = $conn->prepare("SELECT * FROM items WHERE item_id = ? AND acceptable = 1 AND available = 1");
$stmt->execute(array($item_id));
$count = $stmt->rowCount();

if ($count) :

    $stmt = $conn->prepare("SELECT 
                                items.item_name,
                                items.cat_id,
                                items.item_desc,
                                items.item_price,
                                items.offer_price,
                                items.add_date,
                                items.country_made,
                                items.is_special,
                                users.full_name,
                                categories.cat_name
                            FROM
                                items
                            LEFT JOIN
                                categories
                            ON 
                                items.cat_id = categories.id
                            LEFT JOIN
                                users
                            ON
                                users.user_id = items.user_id
                            WHERE 
                                items.acceptable = 1
                            AND
                                items.available = 1
                            AND
                                users.reg_status = 1
                            AND
                                items.item_id = ?
    ");
    $stmt->execute(array($item_id));
    $data = $stmt->fetch();
    echo "<pre>";
    print_r($data);
    echo "</pre>";

?>


<?php
    include $tpl . "footer.php";
else :
    header("Location: index.php");
    exit();
endif;
ob_end_flush();
?>