<?php

ob_start();

session_start();

$page_title = "Categories";

if (isset($_SESSION["username"])) {

    include "init.php";
    include $tpl . "aside.php";

    $do = isset($_GET["do"]) ? $_GET["do"] : "Manage";

    switch ($do) {
        case "Manage";
            break;
        case "Add";
            // Add Page Content
?>

            <div class="container">
                <h1>Add New Category</h1>
                <form action="?do=Insert" method="POST" class="form flow" id="add-categories-form">
                    <ul class="msgs" id="add-categories-form-messages">
                    </ul>
                    <div class="inputs fields">
                        <div class="form-input">
                            <input type="text" name="name" id="add-categories-name" placeholder="name" autocomplete="off" tabindex="1">
                            <label for="add-categories-name">Name</label>
                        </div>
                        <div class="form-input">
                            <input type="text" name="description" id="add-categories-description" placeholder="Full Name" autocomplete="off" tabindex="2">
                            <label for="add-categories-description">Description</label>
                        </div>
                        <div class="form-input">
                            <input type="number" name="order" id="add-categories-order" placeholder="Order" autocomplete="off" tabindex="3">
                            <label for="add-categories-order">Order</label>
                        </div>
                    </div>
                    <div class="inputs checks">
                        <div class="form-input-check">
                            <input type="checkbox" name="visibility" value="1" id="add-categories-visibility">
                            <label for="add-categories-visibility" tabindex="4">Visibility</label>
                        </div>
                        <div class="form-input-check">
                            <input type="checkbox" name="comments" value="1" id="add-categories-comments">
                            <label for="add-categories-comments" tabindex="5">Comments</label>
                        </div>
                        <div class="form-input-check">
                            <input type="checkbox" name="ads" value="1" id="add-categories-ads">
                            <label for="add-categories-ads" tabindex="6">Ads</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" id="add-categories-submit" tabindex="7">Add Category</button>
                </form>
            </div>

<?php
            break;
        case "Insert";

            if ($_SERVER["REQUEST_METHOD"] === "POST") {

                $stmt = $conn->prepare("SELECT cat_name FROM categories WHERE cat_name = ?");
                $stmt->execute(array($_POST["name"]));
                $count = $stmt->rowCount();
                $errorArr = array();

                extract($_POST);

                $msg = $count && "the category has already created";
                $errorArr[] = $msg;
                $msg = !strlen($name) < 4 && "name must be more than 4 characters";
                $errorArr[] = $msg;
                $msg = !strlen($name) > 20 && "name must be less than 20 characters";
                $errorArr[] = $msg;
                $msg = !strlen($description) < 4 && "description must be more than 4 characters";
                $errorArr[] = $msg;
                $msg = !strlen($description) > 50 && "description must be less than 50 characters";
                $errorArr[] = $msg;
                $msg =  !is_numeric($order) && "description must be less than 50 characters";
                $errorArr[] = $msg;

                foreach ($errorArr as $err) {
                    echo "<ul>";
                    echo "<li>" . $err . "</li>";
                    echo "</ul>";
                }

                if (count($errorArr)) {
                    $stmt = $conn->prepare("INSERT INTO categories(
                    cat_name,
                    cat_desc,
                    ordering,
                    visibility,
                    allow_comment,
                    allow_ads
                )
                    VALUES (?,?,?,?,?,?);
                ");
                    $stmt->execute(array($name, $description, $order, $visibility ? 1 : 0, $comments ? 1 : 0, $ads ? 1 : 0));
                    redirect("1 category has been added", "categories.php", 2, "success");
                }
            } else {
                redirect("You can't browse this page directly", "categories.php?do=Add", 2);
            }


            break;
        case "Edit";
            break;
        case "Update";
            break;
    }

    include  $tpl . "footer.php";
} else {
    header("Location: index.php");
    exit;
}

ob_end_flush();
