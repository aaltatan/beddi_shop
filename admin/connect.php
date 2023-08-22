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
        add_date DATETIME NOT NULL,
        country_made VARCHAR(255) NOT NULL,
        item_status VARCHAR(255),
        rating INT(11),
        cat_id INT(11) NOT NULL,
        user_id INT(11) NOT NULL,
        images VARCHAR(255),
        CONSTRAINT fk_cat_id FOREIGN KEY (cat_id) REFERENCES categories(id),
        CONSTRAINT fk_user_id FOREIGN KEY (user_id) REFERENCES users(user_id)
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
            reg_status
        )
        VALUES (?,?,?,?,?,?,?);");
        $admin_stmt->execute($values);
    }
} catch (PDOException $e) {
    echo "Failed " . $e->getMessage();
}
