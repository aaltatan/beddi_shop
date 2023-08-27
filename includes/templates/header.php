<?php

$stmt = $conn->prepare("SELECT id,cat_name FROM categories");
$stmt->execute();
$categories = $stmt->fetchAll();

$stmt = $conn->prepare("SELECT item_id,item_name FROM items WHERE acceptable = 1");
$stmt->execute();
$items = $stmt->fetchAll();

?>

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
  <link rel="stylesheet" href="./layout/fontawesome/all.css">
  <title>Beddi Shop</title>
</head>

<body>

  <header>
    <div class="special">Lorem ipsum dolor sit amet consectetur adipisicing elit. Odio, doloremque?</div>
    <nav>
      <div class="brand">
        <a href="#">
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
        <li><a href="#">Home</a></li>
        <li>
          <a href="#" id="show-categories">Categories</a>
          <ul class="categories" id="categories">
            <?php
            foreach ($categories as $cat) {
              echo "<li>";
              echo "<a href='category.php&id=" . $cat["id"] . "'>" . $cat["cat_name"] . "</a>";
              echo "</li>";
            }
            ?>
          </ul>
        </li>
        <li>
          <a href="#" id="show-items">Items</a>
          <ul class="items" id="items">
            <?php
            foreach ($items as $item) {
              echo "<li>";
              echo "<a href='item.php&id=" . $item["item_id"] . "'>" . $item["item_name"] . "</a>";
              echo "</li>";
            }
            ?>
          </ul>
        </li>
        <li><a href="#">Special</a></li>
      </ul>
      <ul class="icons">
        <li>
          <a href="#" title="Search">
            <i class="fa-solid fa-magnifying-glass"></i>
          </a>
        </li>
        <li>
          <a href="#" title="User Profile">
            <i class="fa-regular fa-circle-user"></i>
          </a>
        </li>
        <li>
          <a href="#" id="mode-btn" data-theme-dark="true" style="text-decoration:none;" title="light\dark mode">
            <i class="fa-regular fa-lightbulb" style="color:gold;"></i>
            <i class="fa-regular fa-moon"></i>
          </a>
        </li>
      </ul>
      <a href="#" id="shopping-cart-btn" title="Your Cart">
        <i class="fa-solid fa-cart-shopping"></i>
      </a>
      <div class="burger"></div>
    </nav>
  </header>

  <div class="blur" id="blur"></div>

  <aside class="shopping-cart" id="shopping-cart">
    <div class="heading">
      <h1>Your Cart</h1>
      <span>×</span>
    </div>
  </aside>