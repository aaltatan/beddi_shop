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
                            AND
                                categories.visibility = 1
    ");
    $stmt->execute(array($item_id));
    $data = $stmt->fetch();

?>
    <div class="main-container flow">

        <div class="breadcrumbs">
            <a href="index.php">Beddi Shop</a>
            <span> > </span>
            <a href="categories.php?id=<?php echo $data["cat_id"] . "&catname=" . $data["cat_name"] ?>"><?php echo $data["cat_name"] ?></a>
            <span> > </span>
            <a href="items.php?id=<?php echo $data["main_id"] . "&itemname=" . strtolower(str_replace(" ", "_", $data["item_name"])) ?>"><?php echo $data["item_name"] ?></a>
        </div>

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

                    <?php
                    if (isset($_SESSION["user_session_id"])) :
                        $stmt = $conn->prepare("SELECT 
                                                    users.full_name,
                                                    users.user_id
                                                FROM 
                                                    items_likes 
                                                LEFT JOIN
                                                    users
                                                ON
                                                    users.user_id = items_likes.user_id
                                                WHERE 
                                                    items_likes.item_id = ? 
                                            ");
                        $stmt->execute(array($data["main_id"]));
                        $dta =  $stmt->fetchAll();
                        $likes = join("\n", array_map(fn ($arr) => $arr["user_id"] === $_SESSION['user_session_id'] ? "You" : $arr["full_name"], $dta));
                        $is_liked = array_map(fn ($arr) => $arr["user_id"], $dta);
                        $is_liked = array_search($_SESSION['user_session_id'], $is_liked);
                    ?>
                        <div class="like" title="<?php echo $likes ?>" data-is-liked="<?php echo $is_liked !== false ? "1" : "0" ?>" data-item-id="<?php echo $data['main_id'] ?>" data-user-id="<?php echo $_SESSION['user_session_id'] ?>">
                            <i class="fa-regular fa-heart like-btn"></i>
                            <i class="fa-solid fa-heart like-btn"></i>
                            <small id="likes-count" title="<?php echo $likes ?>">(<?php echo $data["likes"] ?>)</small>
                        </div>
                    <?php endif ?>
                    <?php if ($data["is_special"]) : ?>
                        <span class="special"><i class="fa-solid fa-star" title="Special Item" style="color:gold;"></i></span>
                    <?php endif ?>

                </div>
                <div class="price">
                    <?php if ($data["offer_price"]) : ?>
                        <span><?php echo number_format($data["offer_price"]) ?></span>
                    <?php endif ?>
                    <span><?php echo number_format($data["item_price"]) ?></span>
                </div>
                <ul class="desc">
                    <h3>Description:</h3>
                    <li><?php echo $data["item_desc"] ?></li>
                    <li>Made in <?php echo $data["country_made"] ?></li>
                    <li>Added <?php echo explode(" ", $data["add_date"])[0] ?></li>
                    <li>By <?php echo $data["full_name"] ?></li>
                </ul>
                <?php if (isset($_SESSION["user_session_id"])) : ?>
                    <button data-role="add-to-cart" data-item-id="<?php echo $data["main_id"] ?>" class="btn btn-primary">Add to Cart</button>
                <?php else : ?>
                    <a href="login.php" class="btn btn-primary">Add to Cart</a>
                <?php endif ?>
                <div class="services">
                    <div>
                        <i class="fa-solid fa-location-arrow fa-2x"></i>
                        <span>Free Shipping over 100,000</span>
                    </div>
                    <div>
                        <i class="fa-solid fa-arrow-rotate-right fa-2x"></i>
                        <span>Easy Returns</span>
                    </div>
                    <div>
                        <i class="fa-regular fa-comment fa-2x"></i>
                        <span>Live Chat</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="comment-container flow">

            <h2 id="item-heading-id" data-item-id="<?php echo $data["main_id"] ?>">Comments:</h2>

            <div class="add-comment">
                <textarea id="comment-box" cols="100" rows="5" placeholder="Write your comment ..."></textarea>
                <?php if (isset($_SESSION["user_session_id"])) : ?>
                    <button title="Add a Comment" data-item-id="<?php echo $data["main_id"] ?>" role="add-comment" class="btn btn-primary"><i class="fa-solid fa-plus"></i></button>
                <?php else : ?>
                    <a href="login.php" class="btn btn-primary"><i class="fa-solid fa-plus"></i></a>
                <?php endif ?>
            </div>

            <div class="comments flow"></div>

        </div>

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
        $cat_items_count = $stmt->rowCount();
        if ($cat_items_count) :
        ?>
            <div class="more-cat">
                <h2>More from same category: </h2>
                <div class="more-cat-container">
                    <?php
                    foreach ($cat_items as $cat_item) :
                    ?>
                        <a class="item-card" href="items.php?id=<?php echo $cat_item["main_id"] . "&itemname=" . strtolower(str_replace(" ", "_", $cat_item["item_name"])) ?>">
                            <div class="image">
                                <img src="<?php echo substr($cat_item['img'], 1) ?>" alt="dasd">
                            </div>
                            <span><?php echo $cat_item["item_name"] ?></span>
                        </a>
                    <?php endforeach ?>
                </div>
            </div>
        <?php endif ?>

    </div>

    <script src="layout/js/items.js"></script>
    <script src="layout/js/shoppingCart.js"></script>

<?php

    include $tpl . "footer.php";

else :
    header("Location: index.php");
    exit();

endif;
ob_end_flush();
?>