<?php

include "init.php";

$stmt = $conn->prepare("SELECT * FROM items_images");
$stmt->execute();
$data = $stmt->fetchAll();

foreach ($data as $img) {
  unlink($img["img"]);
}
