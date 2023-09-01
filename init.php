<?php

include "admin/connect.php";

// !Regular Expression Patterns:

$username_re = '/^[a-z]+[a-z0-9\.]{4,20}$/';
$email_re = '/^[A-Za-z0-9\.\-_]+@[a-z0-9\-]+\.\w{2,4}$/';
$password_re = '/^.{8,}$/';
$full_name_re = '/^[A-Z][A-Za-z\s]{2,48}[a-z]$/';
$name_country_re = '/^[A-Z][a-z]{3,19}$/';
$description_title_re = '/^.{4,50}$/';
$price_order_re = '/^\d+$/';


// !routes:

$tpl = "./includes/templates/";
$func = "./admin/includes/functions/";

// !Includes:

include $func . "functions.php";

$stmt = $conn->prepare("SELECT id,cat_name FROM categories");
$stmt->execute();
$categories = $stmt->fetchAll();

$stmt = $conn->prepare("SELECT item_id,item_name FROM items WHERE acceptable = 1");
$stmt->execute();
$items = $stmt->fetchAll();

include $tpl . "header.php";
