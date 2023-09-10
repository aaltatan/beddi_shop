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
    -- Creating First_Run Table (to help me inserting the test data at first time)
    CREATE TABLE IF NOT EXISTS first_run(
        has_ran TINYINT DEFAULT 0 NOT NULL
    );
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
            reg_status,
            dt
        )
        VALUES (?,?,?,?,?,?,?,NOW());");
        $admin_stmt->execute($values);
    }

    $stmt = $conn->prepare("SELECT * FROM first_run");
    $stmt->execute();
    $has_ran = $stmt->rowCount();

    if (!$has_ran) {
        $stmt = $conn->prepare("INSERT INTO 
                                        `users` (
                                            `user_id`, 
                                            `username`, 
                                            `password`, 
                                            `email`, 
                                            `full_name`, 
                                            `group_id`, 
                                            `trusted_status`, 
                                            `reg_status`, 
                                            `dt`
                                        )
                                VALUES
                                (2, 'anas.hamad', 'a07b80f83c004a7078277b4a3989f0aa483a823a', 'anas@gmail.com', 'Anas Hamad', 0, 0, 1, '2023-09-09 16:07:46'),
                                (3, 'mayyar.haddad', 'a07b80f83c004a7078277b4a3989f0aa483a823a', 'mayyar@mail.com', 'Mayyar Haddad', 0, 0, 1, '2023-09-09 18:28:07'),
                                (4, 'adeeb', 'a07b80f83c004a7078277b4a3989f0aa483a823a', 'adeeb@gmail.com', 'Adeeb Memarie', 0, 0, 0, '2023-09-09 18:31:07'),
                                (5, 'phd.bassel', 'a07b80f83c004a7078277b4a3989f0aa483a823a', 'basel@gmail.com', 'Bassel Alkhateeb', 0, 0, 1, '2023-09-09 18:32:14'),
                                (6, 'rawaa', 'a07b80f83c004a7078277b4a3989f0aa483a823a', 'rawaa@gmail.com', 'Rawaa Jarrouj', 0, 0, 1, '2023-09-09 18:38:01'),
                                (7, 'rama.sar', 'a07b80f83c004a7078277b4a3989f0aa483a823a', 'rama@gmail.com', 'Rama Alsarakbie', 0, 0, 1, '2023-09-09 21:08:04');
                                -- Categories Data:
                                INSERT INTO 
                                    `categories` (
                                        `id`, 
                                        `cat_name`, 
                                        `cat_desc`, 
                                        `ordering`, 
                                        `visibility`, 
                                        `allow_comment`, 
                                        `allow_ads`
                                ) VALUES
                                (1, 'Watches', 'Watches', 1, 1, 1, 1),
                                (2, 'Bags', 'Hand & Back Bags', 2, 1, 1, 1),
                                (3, 'Glasses', 'Sun and medical glasses', 3, 1, 1, 1),
                                (4, 'Gentles', 'Men Accessories', 4, 1, 1, 1),
                                (5, 'Wallets', 'Wallets', 5, 1, 1, 1),
                                (6, 'Lighters', 'Lighters', 6, 1, 1, 1),
                                (7, 'Ladies', 'For Women', 7, 1, 1, 1);
                                -- Items Data:
                                INSERT INTO 
                                        `items` (
                                            `item_id`, 
                                            `item_name`, 
                                            `item_desc`, 
                                            `item_price`, 
                                            `offer_price`, 
                                            `add_date`, 
                                            `country_made`, 
                                            `cat_id`, 
                                            `user_id`, 
                                            `available`, 
                                            `acceptable`, 
                                            `is_special`, 
                                            `is_cover`
                                        ) 
                                VALUES
                                    (6, 'Armani Watch', 'Armani Watch Stainless Steel Copy One', 300000, 0, '2023-09-09 17:31:31', 'China', 1, 1, 1, 1, 0, 0),
                                    (7, 'Bobby Bag', 'Bobby Bag', 400000, 0, '2023-09-09 17:32:32', 'China', 2, 1, 1, 1, 0, 0),
                                    (8, 'Classic RayBan', 'Classic RayBan', 250000, 0, '2023-09-09 17:34:40', 'Syria', 3, 1, 1, 1, 1, 0),
                                    (9, 'Couple Watch', 'Couple Watch', 500000, 425000, '2023-09-09 17:35:44', 'China', 1, 1, 1, 1, 0, 0),
                                    (10, 'Curren Watch', 'Curren Watch', 300000, 0, '2023-09-09 17:42:04', 'Syria', 1, 1, 1, 1, 1, 0),
                                    (11, 'D-Ziner Watch', 'D-Ziner Watch', 450000, 0, '2023-09-09 17:44:07', 'China', 1, 1, 1, 1, 1, 0),
                                    (12, 'Exponi Sport Watch', 'Exponi Sport Watch', 425000, 0, '2023-09-09 17:47:08', 'China', 1, 1, 1, 1, 0, 0),
                                    (13, 'Fendi Glasses', 'Fendi Glasses', 125000, 0, '2023-09-09 17:48:03', 'Syria', 3, 1, 1, 1, 0, 0),
                                    (14, 'Jeep Handbag', 'Jeep Handbag', 175000, 150000, '2023-09-09 17:49:34', 'Syria', 2, 1, 1, 1, 1, 0),
                                    (15, 'Kalvin Clien Watch', 'Kalvin Clien Watch', 600000, 0, '2023-09-09 17:53:58', 'China', 1, 1, 1, 1, 0, 0),
                                    (16, 'Kemei Shaver', 'Kemei Shaver', 650000, 575000, '2023-09-09 17:56:06', 'China', 4, 1, 1, 1, 0, 0),
                                    (17, 'PIGUET Watch', 'PIGUET Watch', 500000, 450000, '2023-09-09 17:57:58', 'China', 1, 1, 1, 1, 0, 0),
                                    (18, 'Ferrari Pocket Wallet', 'Ferrari Pocket Wallet', 100000, 0, '2023-09-09 18:00:04', 'Syria', 5, 1, 1, 1, 1, 0),
                                    (19, 'Playing Card Lighter', 'Playing Card Lighter', 125000, 100000, '2023-09-09 18:04:32', 'Syria', 6, 1, 1, 1, 1, 0),
                                    (20, 'Pocket Wallet Armani', 'Pocket Wallet', 150000, 0, '2023-09-09 18:07:32', 'Syria', 5, 1, 1, 1, 1, 0),
                                    (21, 'Dell Back Bag', 'Dell Back Bag', 300000, 0, '2023-09-09 18:09:54', 'Syria', 2, 1, 1, 1, 0, 0),
                                    (22, 'G-Rex Sport Watch', 'G-Rex Sport Watch', 400000, 0, '2023-09-09 18:11:55', 'Syria', 1, 1, 1, 1, 1, 0),
                                    (23, 'Winter Cotton Scarf', 'Winter Cotton Scarf', 50000, 0, '2023-09-09 18:14:40', 'Syria', 7, 1, 1, 1, 0, 0),
                                    (24, 'Classic Leather Watch', 'Classic Leather Watch', 400000, 350000, '2023-09-09 18:16:34', 'China', 1, 1, 1, 1, 0, 0),
                                    (25, 'RayBan P Glasses', 'RayBan P Glasses', 400000, 0, '2023-09-09 18:18:20', 'Syria', 3, 1, 1, 1, 1, 0),
                                    (26, 'Swiss Army Back Bag', 'Swiss Army Back Bag', 300000, 0, '2023-09-09 18:20:23', 'Syria', 2, 1, 1, 1, 0, 0),
                                    (27, 'Versace Hand Bag', 'Versace Hand Bag', 200000, 0, '2023-09-09 18:21:24', 'Syria', 2, 1, 1, 1, 1, 0),
                                    (28, 'Wood RayBan Glasses', 'Wood RayBan Glasses', 300000, 0, '2023-09-09 18:22:11', 'Syria', 3, 1, 1, 1, 0, 0),
                                    (29, 'Swiss Gear Back Bag', 'Swiss Gear Back Bag', 200000, 0, '2023-09-09 20:49:44', 'Syria', 2, 1, 1, 1, 0, 0),
                                    (30, 'Armani back bag', 'Armani back bag', 300000, 0, '2023-09-09 20:58:00', 'China', 2, 1, 1, 1, 0, 0),
                                    (31, 'Kemie 7 in 1', 'Kemie 7 in 1', 600000, 0, '2023-09-09 20:58:44', 'China', 4, 1, 1, 1, 1, 0),
                                    (32, 'Blinder Watch', 'Blinder Watch', 125000, 0, '2023-09-09 21:00:23', 'China', 1, 1, 1, 1, 0, 1),
                                    (33, 'RayBan Classic Glasses', 'RayBan Classic Glasses', 200000, 0, '2023-09-09 21:01:47', 'Syria', 3, 1, 1, 1, 0, 0);
                                -- Items Images Data:
                                INSERT INTO 
                                    `items_images` (
                                        `item_id`, 
                                        `img`
                                    ) 
                                VALUES
                                    (6, '../data/uploads//armani-watch-landscape-5.jpg'),
                                    (6, '../data/uploads//armani-watch-landscape-4.jpg'),
                                    (6, '../data/uploads//armani-watch-landscape-3.jpg'),
                                    (6, '../data/uploads//armani-watch-landscape-2.jpg'),
                                    (6, '../data/uploads//armani-watch-landscape-1.jpg'),
                                    (7, '../data/uploads//bobby-bag-landscape-2.jpg'),
                                    (7, '../data/uploads//bobby-bag-landscape-1.jpg'),
                                    (15, '../data/uploads//kalvin-clein-landscape-1.jpg'),
                                    (6, '../data/uploads//armani-watch-landscape-6.jpg'),
                                    (7, '../data/uploads//bobby-bag-landscape-3.jpg'),
                                    (7, '../data/uploads//bobby-bag-landscape-4.jpg'),
                                    (7, '../data/uploads//bobby-bag-landscape-5.jpg'),
                                    (8, '../data/uploads//classic-rayban-glass-landscape-1.jpg'),
                                    (8, '../data/uploads//classic-rayban-glass-landscape-2.jpg'),
                                    (8, '../data/uploads//classic-rayban-glass-landscape-3.jpg'),
                                    (8, '../data/uploads//classic-rayban-glass-landscape-4.jpg'),
                                    (9, '../data/uploads//couple-watch-landscape-1.jpg'),
                                    (9, '../data/uploads//couple-watch-landscape-2.jpg'),
                                    (9, '../data/uploads//couple-watch-landscape-3.jpg'),
                                    (9, '../data/uploads//couple-watch-landscape-4.jpg'),
                                    (9, '../data/uploads//couple-watch-landscape-5.jpg'),
                                    (9, '../data/uploads//couple-watch-landscape-6.jpg'),
                                    (9, '../data/uploads//couple-watch-landscape-7.jpg'),
                                    (10, '../data/uploads//curren-watch-landscape-1.jpg'),
                                    (10, '../data/uploads//curren-watch-landscape-2.jpg'),
                                    (10, '../data/uploads//curren-watch-landscape-3.jpg'),
                                    (10, '../data/uploads//curren-watch-landscape-4.jpg'),
                                    (11, '../data/uploads//d-ziner-landscape-1.jpg'),
                                    (11, '../data/uploads//d-ziner-landscape-2.jpg'),
                                    (11, '../data/uploads//d-ziner-landscape-3.jpg'),
                                    (11, '../data/uploads//d-ziner-landscape-4.jpg'),
                                    (11, '../data/uploads//d-ziner-landscape-5.jpg'),
                                    (12, '../data/uploads//expone-watch-landscape-1.jpg'),
                                    (12, '../data/uploads//expone-watch-landscape-2.jpg'),
                                    (12, '../data/uploads//expone-watch-landscape-3.jpg'),
                                    (12, '../data/uploads//expone-watch-landscape-4.jpg'),
                                    (12, '../data/uploads//expone-watch-landscape-5.jpg'),
                                    (13, '../data/uploads//fendi-glass-landscape-1.jpg'),
                                    (13, '../data/uploads//fendi-glass-landscape-2.jpg'),
                                    (13, '../data/uploads//fendi-glass-landscape-3.jpg'),
                                    (13, '../data/uploads//fendi-glass-landscape-4.jpg'),
                                    (13, '../data/uploads//fendi-glass-landscape-5.jpg'),
                                    (13, '../data/uploads//fendi-glass-landscape-6.jpg'),
                                    (14, '../data/uploads//jeep-handbag-landscape-1.jpg'),
                                    (14, '../data/uploads//jeep-handbag-landscape-2.jpg'),
                                    (14, '../data/uploads//jeep-handbag-landscape-3.jpg'),
                                    (14, '../data/uploads//jeep-handbag-landscape-4.jpg'),
                                    (14, '../data/uploads//jeep-handbag-landscape-5.jpg'),
                                    (14, '../data/uploads//jeep-handbag-landscape-6.jpg'),
                                    (15, '../data/uploads//kalvin-clein-landscape-2.jpg'),
                                    (15, '../data/uploads//kalvin-clein-landscape-3.jpg'),
                                    (15, '../data/uploads//kalvin-clein-landscape-4.jpg'),
                                    (15, '../data/uploads//kalvin-clein-landscape-5.jpg'),
                                    (15, '../data/uploads//kalvin-clein-landscape-6.jpg'),
                                    (16, '../data/uploads//kemei-landscape-1.jpg'),
                                    (16, '../data/uploads//kemei-landscape-2.jpg'),
                                    (16, '../data/uploads//kemei-landscape-3.jpg'),
                                    (16, '../data/uploads//kemei-landscape-4.jpg'),
                                    (16, '../data/uploads//kemei-landscape-5.jpg'),
                                    (16, '../data/uploads//kemei-landscape-6.jpg'),
                                    (17, '../data/uploads//PIGUET-watch-landscape-2.jpg'),
                                    (17, '../data/uploads//PIGUET-watch-landscape-3.jpg'),
                                    (17, '../data/uploads//PIGUET-watch-landscape-4.jpg'),
                                    (17, '../data/uploads//PIGUET-watch-landscape-5.jpg'),
                                    (18, '../data/uploads//pocket-wallet-landscape-1.jpg'),
                                    (18, '../data/uploads//pocket-wallet-landscape-2.jpg'),
                                    (18, '../data/uploads//pocket-wallet-landscape-3.jpg'),
                                    (18, '../data/uploads//pocket-wallet-landscape-4.jpg'),
                                    (19, '../data/uploads//product-01-01.jpg'),
                                    (19, '../data/uploads//product-01-02.jpg'),
                                    (19, '../data/uploads//product-01-03.jpg'),
                                    (19, '../data/uploads//product-01-04.jpg'),
                                    (20, '../data/uploads//product-02-01.jpg'),
                                    (20, '../data/uploads//product-02-02.jpg'),
                                    (20, '../data/uploads//product-02-03.jpg'),
                                    (20, '../data/uploads//product-02-04.jpg'),
                                    (21, '../data/uploads//product-03-01.jpg'),
                                    (21, '../data/uploads//product-03-02.jpg'),
                                    (21, '../data/uploads//product-03-03.jpg'),
                                    (21, '../data/uploads//product-03-04.jpg'),
                                    (22, '../data/uploads//product-04-01.jpg'),
                                    (22, '../data/uploads//product-04-02.jpg'),
                                    (22, '../data/uploads//product-04-03.jpg'),
                                    (22, '../data/uploads//product-04-04.jpg'),
                                    (23, '../data/uploads//product-05-01.jpg'),
                                    (23, '../data/uploads//product-05-02.jpg'),
                                    (23, '../data/uploads//product-05-03.jpg'),
                                    (23, '../data/uploads//product-05-04.jpg'),
                                    (24, '../data/uploads//product-06-01.jpg'),
                                    (24, '../data/uploads//product-06-02.jpg'),
                                    (24, '../data/uploads//product-06-03.jpg'),
                                    (24, '../data/uploads//product-06-04.jpg'),
                                    (25, '../data/uploads//rayban-p-landscape-1.jpg'),
                                    (25, '../data/uploads//rayban-p-landscape-2.jpg'),
                                    (25, '../data/uploads//rayban-p-landscape-3.jpg'),
                                    (25, '../data/uploads//rayban-p-landscape-4.jpg'),
                                    (26, '../data/uploads//swiss-backbag-landscape-1.jpg'),
                                    (26, '../data/uploads//swiss-backbag-landscape-2.jpg'),
                                    (26, '../data/uploads//swiss-backbag-landscape-3.jpg'),
                                    (26, '../data/uploads//swiss-backbag-landscape-4.jpg'),
                                    (27, '../data/uploads//versace-hand-bag-landscape-1.jpg'),
                                    (27, '../data/uploads//versace-hand-bag-landscape-2.jpg'),
                                    (27, '../data/uploads//versace-hand-bag-landscape-3.jpg'),
                                    (27, '../data/uploads//versace-hand-bag-landscape-4.jpg'),
                                    (28, '../data/uploads//woor-rayban-glass-landscape-1.jpg'),
                                    (28, '../data/uploads//woor-rayban-glass-landscape-2.jpg'),
                                    (28, '../data/uploads//woor-rayban-glass-landscape-3.jpg'),
                                    (28, '../data/uploads//woor-rayban-glass-landscape-4.jpg'),
                                    (28, '../data/uploads//woor-rayban-glass-landscape-5.jpg'),
                                    (29, '../data/uploads//swiss-gear-landscape-1.jpg'),
                                    (29, '../data/uploads//swiss-gear-landscape-2.jpg'),
                                    (29, '../data/uploads//swiss-gear-landscape-3.jpg'),
                                    (29, '../data/uploads//swiss-gear-landscape-4.jpg'),
                                    (29, '../data/uploads//swiss-gear-landscape-5.jpg'),
                                    (30, '../data/uploads//armani-bag-landscape-1.jpg'),
                                    (30, '../data/uploads//armani-bag-landscape-2.jpg'),
                                    (30, '../data/uploads//armani-bag-landscape-3.jpg'),
                                    (30, '../data/uploads//armani-bag-landscape-4.jpg'),
                                    (30, '../data/uploads//armani-bag-landscape-5.jpg'),
                                    (31, '../data/uploads//kemei-7-in-1-landscape-1.jpg'),
                                    (31, '../data/uploads//kemei-7-in-1-landscape-2.jpg'),
                                    (31, '../data/uploads//kemei-7-in-1-landscape-3.jpg'),
                                    (31, '../data/uploads//kemei-7-in-1-landscape-4.jpg'),
                                    (31, '../data/uploads//kemei-7-in-1-landscape-5.jpg'),
                                    (32, '../data/uploads//blinder-watch-landscape-1.jpg'),
                                    (32, '../data/uploads//blinder-watch-landscape-2.jpg'),
                                    (32, '../data/uploads//blinder-watch-landscape-3.jpg'),
                                    (32, '../data/uploads//blinder-watch-landscape-4.jpg'),
                                    (32, '../data/uploads//blinder-watch-landscape-5.jpg'),
                                    (32, '../data/uploads//blinder-watch-landscape-6.jpg'),
                                    (33, '../data/uploads//raypan-p-glass-landscape-1.jpg'),
                                    (33, '../data/uploads//raypan-p-glass-landscape-2.jpg'),
                                    (33, '../data/uploads//raypan-p-glass-landscape-3.jpg'),
                                    (33, '../data/uploads//raypan-p-glass-landscape-4.jpg');
        ");
        $stmt->execute();
        $stmt = $conn->prepare("INSERT INTO first_run VALUES (1)");
        $stmt->execute();
    }
} catch (PDOException $e) {
    echo "Failed " . $e->getMessage();
}
