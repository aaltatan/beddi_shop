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
        reg_status int(11) DEFAULT 0
    );");

    $stmt = $conn->prepare("SELECT username FROM users;");
    $stmt->execute();
    $users = $stmt->fetchAll();
    $users = array_map(fn ($x) => $x[0], $users);

    if (array_search("admin", $users) === false) {
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
