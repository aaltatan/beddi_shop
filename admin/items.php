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
            echo "Welcome to items Page";
            break;
        case "Add";

            $stmt = $conn->prepare("SELECT id,cat_name FROM categories");
            $stmt->execute();
            $categories_options = $stmt->fetchAll();

            $stmt = $conn->prepare("SELECT user_id,full_name FROM users WHERE reg_status = 1");
            $stmt->execute();
            $users_options = $stmt->fetchAll();

?>

            <div class="container">
                <h1>Add New Item</h1>
                <form action="?do=Insert" method="POST" class="form flow" id="add-items-form" enctype="multipart/form-data">
                    <ul class="msgs" id="add-items-form-messages">
                    </ul>
                    <div class="inputs fields">
                        <div class="form-input">
                            <input type="text" name="name" id="add-items-name" placeholder="name" autocomplete="off" tabindex="1">
                            <label for="add-items-name">Name</label>
                        </div>
                        <div class="form-input">
                            <input type="text" name="description" id="add-items-description" placeholder="Full Name" autocomplete="off" tabindex="2">
                            <label for="add-items-description">Description</label>
                        </div>
                        <div class="form-input">
                            <input type="number" name="price" id="add-items-price" placeholder="Price" autocomplete="off" tabindex="3" min="1">
                            <label for="add-items-price">Price</label>
                        </div>
                        <div class="form-input">
                            <input type="text" name="price" id="add-items-country" placeholder="Country" autocomplete="off" tabindex="4">
                            <label for="add-items-country">Country</label>
                        </div>
                        <div class="form-input">
                            <input type="text" name="price" id="add-items-status" placeholder="status" autocomplete="off" tabindex="5">
                            <label for="add-items-status">Status</label>
                        </div>
                        <div class="form-input">
                            <input type="number" name="rating" id="add-items-rating" placeholder="Rating" autocomplete="off" tabindex="6" min="1">
                            <label for="add-items-rating">Rating</label>
                        </div>
                        <div class="form-input-select">
                            <select name="category" id="add-items-category" tabindex="7">
                                <?php
                                foreach ($categories_options as $option) {
                                    echo "<option value='" . $option["id"] . "'>" . $option["cat_name"] . "</option>";
                                }
                                ?>
                            </select>
                            <label for="add-items-category">Category</label>
                        </div>
                        <div class="form-input-select">
                            <select name="user" id="add-items-user" tabindex="8">
                                <?php
                                foreach ($users_options as $option) {
                                    echo "<option value='" . $option["user_id"] . "'>" . $option["full_name"] . "</option>";
                                }
                                ?>
                            </select>
                            <label for="add-items-user">Username</label>
                        </div>
                        <div class="form-input-file">
                            <input type="file" name="images[]" id="add-items-images" multiple="multiple" accept="image/*" tabindex="9">
                            <label for="add-items-images">Images</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" id="add-items-submit" tabindex="10">Add Item</button>
                </form>
            </div>

            <!-- <script type="module">
                import {
                    Validate
                } from "../layout/js/formsValidation.js";
                let addForm = new Validate(
                    "add-categories-form",
                    "add-categories-form-messages",
                    "add-categories-submit"
                );
                addForm.submitForm();
            </script> -->

<?php
            break;
        case "Insert";

            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                echo "<pre>";
                print_r($_POST);
                print_r($_FILES["images"]["name"]);
                echo "</pre>";
            } else {
                redirect("You can't browse this page directly", "items.php?do=Add", 2);
            }
            break;
        case "Edit";
            break;
        case "Delete";
            break;
        case "Update";
            break;
        case "Activate";
            break;
        case "Deactivate";
            break;
    }

    include  $tpl . "footer.php";
} else {
    header("Location: index.php");
    exit;
}

ob_end_flush();
