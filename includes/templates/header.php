<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="./layout/css/main.css">
  <link rel="stylesheet" href="./layout/fontawesome/all.min.css">
  <title><?php echo pageTitle() ?></title>
</head>

<body data-user-id="<?php isset($_SESSION["user_session_id"]) && print $_SESSION["user_session_id"] ?>">

  <header>
    <div class="special">Lorem ipsum dolor sit amet consectetur adipisicing elit. Odio, doloremque?</div>
    <nav>
      <div class="brand">
        <a href="index.php">
          <span>B</span>
          <span>e</span>
          <span>d</span>
          <span>d</span>
          <span>i</span>
          <span>.</span>
          <span>.</span>
        </a>
      </div>
      <ul class="links">
        <li><a href="index.php" aria-current="false">Home</a></li>
        <li>
          <a href="index.php#categories-container" id="show-categories" aria-current="false">Categories</a>
          <ul class="categories" id="categories">
            <?php
            foreach ($categories as $cat) {
              echo "<li>";
              echo "<a href='categories.php?id=" . $cat["id"] . "&catname=" . $cat["cat_name"]  . "'>" . $cat["cat_name"] . "</a>";
              echo "</li>";
            }
            ?>
          </ul>
        </li>
        <li>
          <a href="#" id="show-items" aria-current="false">Items</a>
          <ul class="items" id="items">
            <?php
            foreach ($items as $item) {
              echo "<li>";
              echo "<a href='items.php?id=" . $item["item_id"] . "&itemname=" . strtolower(str_replace(" ", "_", $item["item_name"])) . "'>" . $item["item_name"] . "</a>";
              echo "</li>";
            }
            ?>
          </ul>
        </li>
        <li><a href="#specials" aria-current="false">Special</a></li>
      </ul>
      <ul class="icons">
        <li>
          <a href="#" title="Search" id="search-btn">
            <i class="fa-solid fa-magnifying-glass"></i>
          </a>
        </li>
        <?php if (isset($_SESSION["user"])) : ?>
          <li>
            <a href="profile.php" title="<?php echo 'Profile ' . $_SESSION['user'] ?>">
              <i class="fa-regular fa-circle-user"></i>
            </a>
          </li>
          <li>
            <a href="logout.php" title="<?php echo 'Logout ' . $_SESSION['user'] ?>">
              <i class="fa-solid fa-arrow-right-from-bracket"></i>
            </a>
          </li>
        <?php else : ?>
          <li>
            <a href="login.php" title="Login">
              <i class="fa-regular fa-circle-user"></i>
            </a>
          </li>
        <?php endif ?>
        <li>
          <a href="#" id="mode-btn" data-theme-dark="true" style="text-decoration:none;" title="light\dark mode">
            <i class="fa-regular fa-lightbulb" style="color:gold;"></i>
            <i class="fa-regular fa-moon"></i>
          </a>
        </li>
      </ul>

      <div class="burger"></div>

      <?php if (isset($_SESSION["user"])) : ?>
        <span href="#" id="shopping-cart-btn" title="Your Cart">
          <span id="shopping-cart-count">0</span>
          <i class="fa-solid fa-cart-shopping"></i>
        </span>
      <?php endif ?>

    </nav>
  </header>

  <div class="blur" id="blur"></div>

  <?php if (isset($_SESSION["user"])) : ?>
    <aside class="shopping-cart" id="shopping-cart">
      <div class="heading">
        <h1>Your Cart</h1>
        <span>×</span>
      </div>
      <div class="body">
      </div>
      <div class="footer flow">
        <div class="subtotal">
          <span>Subtotal</span>
          <span id="shopping-cart-total"></span>
        </div>
        <p>Shipping and taxes calculated at checkout.</p>
        <a href="#" class="btn btn-primary">Checkout</a>
        <img src="layout\images\payment.png" alt="" />
      </div>
    </aside>
  <?php endif ?>

  <div class="search-container">
    <span>×</span>
    <input type="search" name="main-search" id="" placeholder="Search" tabindex="50">
    <ul class="list">
      <?php

      $current_page = explode("/", $_SERVER["PHP_SELF"]);
      $current_page = end($current_page);

      $stmt = $conn->prepare("SELECT * FROM items WHERE acceptable = 1 AND available = 1");
      $stmt->execute();
      $data = $stmt->fetchAll();

      foreach ($data as $item) :

        $stmt = $conn->prepare("SELECT img FROM items_images WHERE item_id = ? LIMIT 1");
        $stmt->execute(array($item["item_id"]));
        $img = $stmt->fetch();
        echo "<li>";
        echo    "<a href='items.php?id=" . $item['item_id'] . "&itemname=" . str_replace(" ", "_", $item["item_name"]) .  "' tabindex='51'>";
        echo        "<img src='" . substr($img["img"], 1) . "' alt='' >";
        echo        "<p>" . $item['item_name'] . "</p>";
        echo    "</a>";
        echo "</li>";

      endforeach;

      ?>

    </ul>
  </div>

  <main>