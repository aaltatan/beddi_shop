<?php

$dsn = "mysql:host=localhost";
$username = "root";
$password = "";
$option = array(
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
);

try {
    $conn = new PDO($dsn, $username, $password, $option);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $conn->exec("CREATE DATABASE IF NOT EXISTS beddishop;
    USE beddishop;
    -- Creating Users Table
    CREATE TABLE IF NOT EXISTS users(
        user_id INT(11) PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        email varchar(255) NOT NULL,
        full_name varchar(255) NOT NULL,
        group_id int(11) DEFAULT 0,
        trusted_status int(11) DEFAULT 0,
        reg_status int(11) DEFAULT 0,
        dt DATETIME
    );
    -- Creating a view to get count of all pending users
    CREATE VIEW IF NOT EXISTS pending_users AS SELECT user_id FROM users WHERE reg_status = 0;
    -- Creating Categories Table
    CREATE TABLE IF NOT EXISTS categories(
        id INT(11) PRIMARY KEY AUTO_INCREMENT,
        cat_name VARCHAR(50) UNIQUE NOT NULL,
        cat_desc TEXT,
        ordering INT(11) NOT NULL,
        visibility BOOLEAN DEFAULT 0,
        allow_comment BOOLEAN DEFAULT 0,
        allow_ads BOOLEAN DEFAULT 0
    );
    -- Creating the Items Table
    CREATE TABLE IF NOT EXISTS items(
        item_id INT(11) PRIMARY KEY AUTO_INCREMENT,
        item_name VARCHAR(255) UNIQUE,
        item_desc TEXT,
        item_price INT(11) NOT NULL,
        offer_price INT(11) NOT NULL DEFAULT 0,
        add_date DATETIME NOT NULL,
        country_made VARCHAR(255) NOT NULL,
        cat_id INT(11) NOT NULL,
        user_id INT(11) NOT NULL,
        available TINYINT DEFAULT 0,
        acceptable TINYINT DEFAULT 0,
        is_special TINYINT DEFAULT 0,
        is_cover TINYINT DEFAULT 0,
        CONSTRAINT fk_cat_id FOREIGN KEY (cat_id) REFERENCES categories(id),
        CONSTRAINT fk_user_id FOREIGN KEY (user_id) REFERENCES users(user_id)
    );
    -- Creating image Table
    CREATE TABLE IF NOT EXISTS items_images(
        item_id INT(11) NOT NULL,
        img TEXT,
        CONSTRAINT fk_item_image FOREIGN KEY (item_id) REFERENCES items(item_id)
    );
    -- Creating Items Likes Table
    CREATE TABLE IF NOT EXISTS items_likes(
        item_id INT(11) NOT NULL,
        user_id INT(11) NOT NULL,
        CONSTRAINT fk_item_id FOREIGN KEY (item_id) REFERENCES items(item_id),
        CONSTRAINT fk_likes_user_id FOREIGN KEY (user_id) REFERENCES users(user_id)
    );
    -- Creating a view to get count of Total Categories Likes
    CREATE VIEW IF NOT EXISTS 
        categories_likes 
    AS 
    SELECT 
        categories.id
    FROM
        items_likes
    LEFT JOIN
        items
    ON
        items.item_id = items_likes.item_id
    LEFT JOIN
        categories
    ON
        categories.id = items.cat_id;
    -- Creating a view to get count of all pending items
    CREATE VIEW IF NOT EXISTS pending_items AS SELECT item_id FROM items WHERE acceptable = 0;
    -- Creating Shopping Cart Table
    CREATE TABLE IF NOT EXISTS cart(
        user_id INT(11) NOT NULL,
        item_id INT(11) NOT NULL,
        quantity INT(11) NOT NULL DEFAULT 1,
        add_date DATETIME NOT NULL DEFAULT NOW(),
        CONSTRAINT fk_cart_user_id FOREIGN KEY (user_id) REFERENCES users(user_id),
        CONSTRAINT fk_cart_item_id FOREIGN KEY (item_id) REFERENCES items(item_id)
    );
    -- Creating Comments Table
    CREATE TABLE IF NOT EXISTS comments(
        comment_id INT(11) PRIMARY KEY AUTO_INCREMENT,
        comment TEXT NOT NULL,
        comment_status TINYINT DEFAULT 0,
        added_date DATETIME NOT NULL DEFAULT NOW(),
        item_id INT(11) NOT NULL,
        user_id INT(11) NOT NULL,
        CONSTRAINT fk_comment_item_id FOREIGN KEY (item_id) REFERENCES items(item_id),
        CONSTRAINT fk_comment_user_id FOREIGN KEY (user_id) REFERENCES users(user_id)
    );
    -- Creating a view to get count of all pending comments
    CREATE VIEW IF NOT EXISTS pending_comments AS SELECT comment_id FROM comments WHERE comment_status = 0;
    ");

    $stmt = $conn->prepare("SELECT username FROM users;");
    $stmt->execute();
    $users = $stmt->fetchAll();
    $count = $stmt->rowCount();

    if ($count <= 0) {
        $values = array("admin", sha1("Qazasd@123"), "a.altatan@gmail.com", "Abdullah Altatan", 1, 0, 1);
        // Creating Super Admin
        $admin_stmt = $conn->prepare("INSERT INTO users(
            username,
            password,
            email,
            full_name,
            group_id,
            trusted_status,
            reg_status
        )
        VALUES (?,?,?,?,?,?,?);");
        $admin_stmt->execute($values);
    }
} catch (PDOException $e) {
    echo "Failed " . $e->getMessage();
}
