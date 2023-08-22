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
                            <input type="text" name="country" id="add-items-country" placeholder="Country" autocomplete="off" tabindex="4">
                            <label for="add-items-country">Country</label>
                        </div>
                        <div class="form-input">
                            <input type="text" name="status" id="add-items-status" placeholder="status" autocomplete="off" tabindex="5">
                            <label for="add-items-status">Status</label>
                        </div>
                        <div class="form-input">
                            <input type="number" name="rating" id="add-items-rating" placeholder="Rating" autocomplete="off" tabindex="6" min="1" max="5">
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
                    "add-items-form",
                    "add-items-form-messages",
                    "add-items-submit"
                );
                addForm.submitForm();
            </script> -->

<?php
            break;
        case "Insert";

            if ($_SERVER["REQUEST_METHOD"] === "POST") {

                extract($_POST);

                $errorArr = array();

                $stmt = $conn->prepare("SELECT item_name FROM items WHERE item_name = ? LIMIT 1");
                $stmt->execute(array($name));
                $count = $stmt->rowCount();

                $count && $errorArr[] = "the name is already exists";
                strlen($name) < 4 && $errorArr[] = "name must be more than 4 characters";
                strlen($name) > 20 && $errorArr[] = "name must be less than 20 characters";
                strlen($status) < 4 && $errorArr[] = "status must be more than 4 characters";
                strlen($status) > 20 && $errorArr[] = "status must be less than 20 characters";
                strlen($description) < 4 && $errorArr[] = "name must be more than 4 characters";
                strlen($description) > 50 && $errorArr[] = "name must be less than 50 characters";
                !is_numeric($price) && $errorArr[] = "price must be numeric";
                strlen($country) < 4 && $errorArr[] = "country must be more than 4 characters";
                strlen($country) > 20 && $errorArr[] = "country must be less than 20 characters";
                !is_numeric($rating) && $errorArr[] = "price must be numeric";

                if (count($errorArr)) {
                    echo "<ul style='padding-left:1em;'>";
                    foreach ($errorArr as $err) {
                        echo "<li>" . $err . "</li>";
                    }
                    echo "</ul>";
                } else {

                    $stmt = $conn->prepare("SELECT item_id FROM items ORDER BY item_id DESC LIMIT 1");
                    $stmt->execute();
                    $item_id = $stmt->fetch();
                    $item_id = is_array($item_id) ? $item_id["item_id"] + 1 : 1;
                    $stmt = $conn->prepare("INSERT INTO 
                        items(
                            item_name,
                            item_desc,
                            item_price,
                            add_date,
                            country_made,
                            item_status,
                            rating,
                            cat_id,
                            user_id
                        )
                        VALUES (?,?,?,NOW(),?,?,?,?,?)
                    ");
                    $stmt->execute(array($name, $description, $price, $country, $status, $rating, $category, $user));

                    $fileCount = count($_FILES['images']['name']);
                    for ($i = 0; $i < $fileCount; $i++) {
                        $fileName = $_FILES['images']['name'][$i];
                        $fileTmpName = $_FILES['images']['tmp_name'][$i];
                        $fileSize = $_FILES['images']['size'][$i];
                        $fileType = $_FILES['images']['type'][$i];
                        // Specify the upload destination folder
                        $uploadFolder = '../data/uploads/';
                        // Generate a unique filename to avoid conflicts
                        $uniqueFilename = uniqid() . '_' . $fileName;
                        $stmt = $conn->prepare("INSERT INTO items_images(item_id,img) VALUES (?,?)");
                        $stmt->execute(array($item_id, $uploadFolder . $uniqueFilename));
                        // Move the uploaded file to the destination folder
                        $destination = $uploadFolder . $uniqueFilename;
                        move_uploaded_file($fileTmpName, $destination);
                        // Perform additional processing or database operations if needed
                    }
                }
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
