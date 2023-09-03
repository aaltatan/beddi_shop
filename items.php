<?php

ob_start();

session_start();

$item_id = is_numeric($_GET["id"]) ? $_GET["id"] : 0;

$page_title = join(" ", array_map(fn ($ele) => ucfirst($ele), explode("_",  $_GET["itemname"])));
include "init.php";

$stmt = $conn->prepare("SELECT * FROM items WHERE item_id = ? AND acceptable = 1 AND available = 1");
$stmt->execute(array($item_id));
$count = $stmt->rowCount();

if ($count) :

    $stmt = $conn->prepare("SELECT 
                                items.item_id as main_id,
                                items.item_name,
                                items.cat_id,
                                items.item_desc,
                                items.item_price,
                                items.offer_price,
                                items.add_date,
                                items.country_made,
                                items.is_special,
                                users.full_name,
                                (SELECT COUNT(*) FROM items_likes WHERE items_likes.item_id = main_id) AS likes,
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

    print_r($data);

?>
    <div class="item-container">
        <div class="images">
            <?php
            $stmt = $conn->prepare("SELECT img FROM items_images WHERE item_id = ?");
            $stmt->execute(array($item_id));
            $images = $stmt->fetchAll();
            $images_count = $stmt->rowCount();
            ?>
            <div class="sub-images">
                <?php for ($i = 1; $i < $images_count; $i++) : ?>
                    <img src="<?php echo substr($images[$i]["img"], 1) ?>" alt="dasd">
                <?php endfor ?>
            </div>
            <img src="<?php echo substr($images[0]["img"], 1) ?>" alt="">
        </div>
        <div class="info">

        </div>
    </div>

    <script src="layout/js/shoppingCart.js"></script>

<?php

    include $tpl . "footer.php";

else :
    header("Location: index.php");
    exit();

endif;
ob_end_flush();
?>