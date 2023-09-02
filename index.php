<?php

session_start();

$page_title = "Beddi Shop";
include "init.php";

$stmt = $conn->prepare("SELECT 
                            items.item_id,
                            items_images.img,
                            items.item_name,
                            items.item_price
                        FROM 
                            items_images 
                        LEFT JOIN 
                            items 
                        ON 
                            items.item_id = items_images.item_id 
                        WHERE 
                            items.is_cover = 1
                        ");
$stmt->execute();
$images = $stmt->fetchAll();
$images_count = $stmt->rowCount();

$stmt = $conn->prepare("SELECT 
                            item_id,
                            item_name,
                            item_price
                        FROM
                            items
                        WHERE
                            is_special = 1
                        ");

$stmt->execute();
$specials = $stmt->fetchAll();

?>

<section class="image-slider">
    <div class="images">
        <?php foreach ($images as $image) : ?>
            <img src="<?php echo substr($image["img"], 1) ?>" alt="">
        <?php endforeach ?>
    </div>
    <ul class="index">
        <?php for ($i = 1; $i <= $images_count; $i++) : ?>
            <li></li>
        <?php endfor ?>
    </ul>
    <div class="control">
        <span id="prev"><i class="fa-solid fa-arrow-left"></i></span>
        <span id="next"><i class="fa-solid fa-arrow-right"></i></span>
    </div>
    <div class="title">
        <div class="info">
            <a href="items.php?id=<?php echo $image["item_id"] ?>">
                <span><?php echo $image["item_name"] ?></span>
                <span><?php echo number_format($image["item_price"]) ?></span>
            </a>
        </div>
        <?php if (isset($_SESSION["user_session_id"])) : ?>
            <button data-role="add-to-cart" data-item-id="<?php echo $image["item_id"] ?>" class="btn btn-secondary" title="add to cart"><i class="fa-solid fa-shopping-cart"></i></button>
        <?php else : ?>
            <a href="login.php" class="btn btn-secondary" title="add to cart"><i class="fa-solid fa-shopping-cart"></i></a>
        <?php endif ?>
    </div>
</section>

<section class="quote">
    <div class="wrapper observe">
        <p>&quot; Lorem ipsum dolor, sit amet consectetur adipisicing elit. Temporibus aut maxime autem obcaecati tempora, sed perferendis nisi maiores molestiae aliquam? &quot;</p>
    </div>
</section>

<section class="specials" id="specials">
    <div class="heading">
        <h1 class="observe">Specials</h1>
        <p>Some of our favorite picks this week.</p>
    </div>
    <span id="go-right"><i class="fa-solid fa-arrow-right"></i></span>
    <span id="go-left"><i class="fa-solid fa-arrow-left"></i></span>
    <div class="wrapper">
        <div class="specials-container">
            <?php foreach ($specials as $special) : ?>
                <div class="item-card observe">
                    <div class="images">
                        <?php
                        $stmt = $conn->prepare("SELECT img FROM items_images WHERE item_id = ?");
                        $stmt->execute(array($special["item_id"]));
                        $images = $stmt->fetchAll();
                        foreach ($images as $image) : ?>
                            <img src="<?php echo substr($image["img"], 1) ?>" alt="">
                        <?php endforeach ?>
                    </div>
                    <div class="info">
                        <?php
                        if (isset($_SESSION["user_session_id"])) :
                            $stmt = $conn->prepare("SELECT * FROM items_likes WHERE item_id = ? AND user_id = ? LIMIT 1");
                            $stmt->execute(array($special["item_id"], $_SESSION["user_session_id"]));
                            $is_liked = $stmt->rowCount();
                        ?>
                            <div class="like" data-is-liked="<?php echo $is_liked ?>" data-item-id="<?php echo $special['item_id'] ?>" data-user-id="<?php echo $_SESSION['user_session_id'] ?>">
                                <i class="fa-regular fa-heart like-btn" title="Like"></i>
                                <i class="fa-solid fa-heart like-btn" title="Unlike"></i>
                            </div>
                        <?php endif ?>
                        <div class="price">
                            <span><?php echo $special["item_name"] ?></span>
                            <span><?php echo number_format($special["item_price"]) ?></span>
                        </div>
                        <div class="btns-group">
                            <?php if (isset($_SESSION["user_session_id"])) : ?>
                                <button data-role="add-to-cart" data-item-id="<?php echo $special["item_id"] ?>" class="btn btn-primary">Add to Cart</button>
                            <?php else : ?>
                                <a href="login.php" class="btn btn-primary">Add to Cart</a>
                            <?php endif ?>
                            <a href="items.php?id=<?php echo $special["item_id"] ?>" class="btn btn-secondary">Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    </div>
</section>

<section class="categories" id="categories">
    <div class="heading">
        <h1 class="observe">Categories</h1>
        <p>Some of our Collections.</p>
    </div>
    <div class="wrapper">
        <div class="categories-container">
            <?php
            $stmt = $conn->prepare("SELECT * FROM ");

            ?>
            <article>
                <div class="cat-card">
                    <img src="" alt="">
                    <div class="title"></div>
                </div>
            </article>
            <article>
                <div class="cat-card">
                    <img src="" alt="">
                    <div class="title"></div>
                </div>
                <div class="cat-card">
                    <img src="" alt="">
                    <div class="title"></div>
                </div>
            </article>
        </div>
    </div>
</section>

<script src="layout/js/index.js"></script>

<?php
include $tpl . "footer.php";
?>