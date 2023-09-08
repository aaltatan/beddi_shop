<?php
ob_start();

session_start();

$category_name = $_GET["catname"];
$page_title = $category_name;
include "init.php";

$category_id = is_numeric($_GET["id"]) ? $_GET["id"] : 0;

$stmt = $conn->prepare("SELECT id FROM categories WHERE id = ? AND visibility = 1 LIMIT 1");
$stmt->execute(array($category_id));
$count = $stmt->rowCount();

if (!$count) {
  redirect("there is no categories like this", "index.php", 1);
}

$stmt = $conn->prepare("SELECT 
                            items.item_id,
                            items_images.img,
                            items.item_name,
                            items.item_price,
                            items.offer_price
                        FROM 
                            items_images 
                        LEFT JOIN 
                            items 
                        ON 
                            items.item_id = items_images.item_id 
                        WHERE 
                            cat_id = ?
                        AND
                            items.acceptable = 1
                        AND
                            items.available = 1
                        AND
                            items.item_id = (SELECT item_id FROM items WHERE cat_id = ? AND items.acceptable = 1 AND items.available = 1 LIMIT 1)
                        ");
$stmt->execute(array($category_id, $category_id));
$images = $stmt->fetchAll();
$images_count = $stmt->rowCount();

$stmt = $conn->prepare("SELECT 
                            item_id,
                            item_name,
                            item_price,
                            offer_price
                        FROM
                            items
                        WHERE
                            is_special = 1
                        AND 
                            items.acceptable = 1 
                        AND 
                            items.available = 1
                        AND
                            items.cat_id = ?
                        ");
$stmt->execute(array($category_id));
$specials = $stmt->fetchAll();
$specials_count = $stmt->rowCount();

?>

<?php if ($images_count) : ?>
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
        <a href="items.php?id=<?php echo $image["item_id"] . "&itemname=" . strtolower(str_replace(" ", "_", $image["item_name"])) ?>">
          <span><?php echo $image["item_name"] ?></span>
          <span><?php echo $image["offer_price"] ? number_format($image["offer_price"]) : number_format($image["item_price"]) ?></span>
        </a>
      </div>
      <?php if (isset($_SESSION["user_session_id"])) : ?>
        <button data-role="add-to-cart" data-item-id="<?php echo $image["item_id"] ?>" class="btn btn-secondary" title="add to cart"><i class="fa-solid fa-shopping-cart"></i></button>
      <?php else : ?>
        <a href="login.php" class="btn btn-secondary" title="add to cart"><i class="fa-solid fa-shopping-cart"></i></a>
      <?php endif ?>
    </div>
  </section>
<?php endif ?>

<?php if ($specials_count) : ?>
  <section class="specials" id="specials">
    <div class="heading">
      <h1 class="observe">Specials</h1>
      <p>Some of our favorite picks this week.</p>
    </div>
    <span id="go-right"><i class="fa-solid fa-arrow-right"></i></span>
    <span id="go-left"><i class="fa-solid fa-arrow-left"></i></span>
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
              <span><?php echo $special["offer_price"] ? number_format($special["offer_price"]) : number_format($special["item_price"]) ?></span>
            </div>
            <div class="btns-group">
              <?php if (isset($_SESSION["user_session_id"])) : ?>
                <button data-role="add-to-cart" data-item-id="<?php echo $special["item_id"] ?>" class="btn btn-primary">Add to Cart</button>
              <?php else : ?>
                <a href="login.php" class="btn btn-primary">Add to Cart</a>
              <?php endif ?>
              <a href="items.php?id=<?php echo $special["item_id"] . "&itemname=" . strtolower(str_replace(" ", "_", $special["item_name"])) ?>" class="btn btn-secondary">Details</a>
            </div>
          </div>
        </div>
      <?php endforeach ?>
    </div>
  </section>
<?php endif ?>

<?php
$stmt = $conn->prepare("SELECT 
                            items.item_id,
                            items.item_name,
                            items.item_price,
                            items.offer_price,
                            categories.id as cat_id,
                            categories.cat_name,
                            (SELECT img FROM items_images WHERE items_images.item_id = items.item_id LIMIT 1) as img
                        FROM 
                            items
                        LEFT JOIN
                            categories
                        ON
                            categories.id = items.cat_id
                        WHERE
                            items.acceptable = 1
                        AND
                            items.available = 1
                        AND
                            items.offer_price != 0
                        AND
                            items.cat_id = ?
                        ");
$stmt->execute(array($category_id));
$offers = $stmt->fetchAll();
$offers_count = $stmt->rowCount();
if ($offers_count) :
?>
  <section class="offers" id="offers">
    <div class="heading">
      <h1 class="observe">Offers</h1>
      <p>Be the Winner.</p>
    </div>
    <div class="offers-container">
      <?php
      foreach ($offers as $offer) :
      ?>
        <div class="offer-card observe">
          <span><?php echo round((($offer["item_price"] - $offer["offer_price"]) / $offer["item_price"]) * 100, 0) ?>%</span>
          <img src="<?php echo substr($offer["img"], 1) ?>" alt="">
          <div class="text">
            <a href="items.php?id=<?php echo $offer["item_id"] . "&itemname=" . strtolower(str_replace(" ", "_", $offer["item_name"])) ?>"><?php echo $offer["item_name"] ?></a>
            <a href="<?php echo 'categories.php?id=' . $offer["cat_id"] . "&catname=" . $offer["cat_name"] ?>"><?php echo $offer["cat_name"] ?></a>
            <div class="price">
              <span><?php echo number_format($offer["offer_price"]) ?></span>
              <span><?php echo number_format($offer["item_price"]) ?></span>
            </div>
          </div>
        </div>
      <?php endforeach ?>
    </div>
  </section>
<?php endif ?>

<?php
$stmt = $conn->prepare("SELECT 
                            item_id,
                            item_name,
                            item_price,
                            offer_price
                        FROM
                            items
                        WHERE
                            acceptable = 1
                        AND
                            available = 1
                        AND
                            cat_id = ?
                        ");
$stmt->execute(array($category_id));
$items = $stmt->fetchAll();
$items_count = $stmt->rowCount();
if ($items_count) :
?>
  <section class="all-items" id="all-items">
    <div class="heading">
      <h1 class="observe"><?php echo $category_name ?></h1>
      <p>Our Collection From <?php echo $category_name ?></p>
    </div>
    <div class="wrapper">
      <div class="all-items-container">
        <?php
        foreach ($items as $item) :
        ?>
          <div class="item-card observe">
            <div class="images">
              <?php
              $stmt = $conn->prepare("SELECT img FROM items_images WHERE item_id = ? LIMIT 2");
              $stmt->execute(array($item["item_id"]));
              $images = $stmt->fetchAll();
              foreach ($images as $image) :
              ?>
                <img src="<?php echo substr($image["img"], 1) ?>" alt="dasd">
              <?php endforeach ?>
              <?php if (isset($_SESSION["user_session_id"])) : ?>
                <button data-role="add-to-cart" data-item-id="<?php echo $item["item_id"] ?>" class="btn btn-primary">Add to Cart</button>
              <?php else : ?>
                <a href="login.php" class="btn btn-primary">Add to Cart</a>
              <?php endif ?>
            </div>
            <a href="items.php?id=<?php echo $item["item_id"] . "&itemname=" . strtolower(str_replace(" ", "_", $item["item_name"])) ?>">
              <span><?php echo $item["item_name"] ?></span>
              <span><?php echo $item["offer_price"] ? number_format($item["offer_price"]) : number_format($item["item_price"]) ?></span>
            </a>
          </div>
        <?php endforeach ?>
      </div>
    </div>
  </section>
<?php endif ?>

<script src="layout/js/index.js"></script>
<script src="layout/js/shoppingCart.js"></script>

<?php
include $tpl . "footer.php";
ob_end_flush();
?>