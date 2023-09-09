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
$date_re = '/^\d{4}-[0-1]\d-[0-3]\d$/';


// !routes:

$tpl = "./includes/templates/";
$func = "./admin/includes/functions/";

// !Includes:

include $func . "functions.php";

$stmt = $conn->prepare("SELECT 
                            id,
                            cat_name,
                            (SELECT COUNT(*) FROM items WHERE id = items.cat_id) as items_count 
                        FROM 
                            categories 
                        WHERE 
                            visibility = 1
                        HAVING
                            items_count != 0
                        ");
$stmt->execute();
$categories = $stmt->fetchAll();

$stmt = $conn->prepare("SELECT 
                            items.item_id,
                            items.item_name 
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
                            categories.visibility = 1
                        ");
$stmt->execute();
$items = $stmt->fetchAll();

include $tpl . "header.php";
