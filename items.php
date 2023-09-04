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

?>
    <div class="main-container flow">

        <div class="items-item-container">
            <?php
            $stmt = $conn->prepare("SELECT img FROM items_images WHERE item_id = ?");
            $stmt->execute(array($item_id));
            $images = $stmt->fetchAll();
            $images_count = $stmt->rowCount();
            ?>
            <div class="images">
                <div class="sub-images flow">
                    <?php for ($i = 0; $i < $images_count; $i++) : ?>
                        <img src="<?php echo substr($images[$i]["img"], 1) ?>" alt="dasd">
                    <?php endfor ?>
                </div>
                <img src="<?php echo substr($images[0]["img"], 1) ?>" alt="">
            </div>
            <div class="info">
                <div class="title">
                    <h1><?php echo $data["item_name"] ?></h1>
                    <a href="categories.php?id=<?php echo $data["cat_id"] ?>">(<?php echo $data["cat_name"] ?>)</a>
                    <span class="likes"><i class="fa-solid fa-heart" style="color:var(--clr-danger-base);"></i> <small>(<?php echo $data["likes"] ?>)</small></span>
                    <?php if ($data["is_special"]) : ?>
                        <span class="special"><i class="fa-solid fa-star" title="Special Item" style="color:gold;"></i></span>
                    <?php endif ?>
                </div>
                <div class="price">
                    <span><?php echo number_format($data["item_price"]) ?></span>
                    <?php if ($data["offer_price"]) : ?>
                        <span><?php echo number_format($data["offer_price"]) ?></span>
                    <?php endif ?>
                </div>
                <p class="desc">
                    <?php echo $data["item_desc"] ?>
                </p>
                <span class="country">
                    <?php echo $data["country_made"] ?>
                </span>
                <span class="add-date">
                    <?php echo explode(" ", $data["add_date"])[0] ?>
                </span>
                <span class="user">
                    By <?php echo $data["full_name"] ?>
                </span>
                <?php if (isset($_SESSION["user_session_id"])) : ?>
                    <button data-role="add-to-cart" data-item-id="<?php echo $data["main_id"] ?>" class="btn btn-primary">Add to Cart</button>
                <?php endif ?>
            </div>
        </div>

        <div class="more-cat">
            <h2>More from this category: </h2>
            <div class="more-cat-container">
                <?php
                $stmt = $conn->prepare("SELECT 
                                    items.item_id AS main_id,
                                    items.item_name,
                                    items.cat_id,
                                    (SELECT img FROM items_images WHERE items_images.item_id = main_id LIMIT 1) as img,
                                    categories.cat_name
                                FROM
                                    categories
                                LEFT JOIN
                                    items
                                ON
                                    categories.id = items.cat_id
                                WHERE
                                    categories.id = ?
                                AND
                                    items.acceptable = 1
                                AND
                                    items.available = 1
                                AND
                                    items.item_id != ?
            ");
                $stmt->execute(array($data["cat_id"], $data["main_id"]));
                $cat_items = $stmt->fetchAll();
                foreach ($cat_items as $cat_item) :
                ?>
                    <div class="item-card">
                        <div class="image">
                            <img src="<?php echo substr($cat_item['img'], 1) ?>" alt="dasd">
                        </div>
                        <a href="items.php?id=<?php echo $cat_item["main_id"] . "&itemname=" . strtolower(str_replace(" ", "_", $cat_item["item_name"])) ?>">
                            <?php echo $cat_item["item_name"] ?>
                        </a>
                    </div>
                <?php endforeach ?>
            </div>
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