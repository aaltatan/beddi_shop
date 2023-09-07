<?php

ob_start();

session_start();

$page_title = "Items";

if (isset($_SESSION["admin"])) {

    include "init.php";
    include $tpl . "aside.php";

    $do = isset($_GET["do"]) ? $_GET["do"] : "Manage";

    switch ($do) {

        case "Cover":
            $id = isset($_GET["id"]) && is_numeric($_GET["id"]) ? $_GET["id"] : 0;
            $stmt = $conn->prepare("SELECT item_id,item_name FROM items WHERE item_id = ? LIMIT 1");
            $stmt->execute(array($id));
            $item_name = $stmt->fetch()["item_name"];
            $count = $stmt->rowCount();

            if ($count) {
                $stmt = $conn->prepare("UPDATE items SET is_cover = 0");
                $stmt->execute();
                $stmt = $conn->prepare("UPDATE items SET is_cover = 1 WHERE item_id = ?");
                $stmt->execute(array($id));
                redirect("$item_name has been cover for main site", "back", 1, "success");
            } else {
                redirect("There is no item like this", "back", 2,);
            }

            break;

        case "Special":
            $id = isset($_GET["id"]) && is_numeric($_GET["id"]) ? $_GET["id"] : 0;
            $stmt = $conn->prepare("SELECT item_id,item_name FROM items WHERE item_id = ? LIMIT 1");
            $stmt->execute(array($id));
            $item_name = $stmt->fetch()["item_name"];
            $count = $stmt->rowCount();

            if ($count) {
                $stmt = $conn->prepare("UPDATE items SET is_special = 1 WHERE item_id = ?");
                $stmt->execute(array($id));
                redirect("$item_name has been added to special list", "back", 1, "success");
            } else {
                redirect("There is no item like this", "back", 2,);
            }

            break;

        case "Unspecial":

            $id = isset($_GET["id"]) && is_numeric($_GET["id"]) ? $_GET["id"] : 0;
            $stmt = $conn->prepare("SELECT item_id,item_name FROM items WHERE item_id = ?");
            $stmt->execute(array($id));
            $item_name = $stmt->fetch()["item_name"];
            $count = $stmt->rowCount();

            if ($count) {
                $stmt = $conn->prepare("UPDATE items SET is_special = 0 WHERE item_id = ?");
                $stmt->execute(array($id));
                redirect("$item_name has been removed from special list", "back", 2, "success");
            } else {
                redirect("There is no item like this", "back", 2,);
            }

            break;

        case "Manage";

            $stmt = $conn->prepare("SELECT 
                                        items.item_id,
                                        items.item_name,
                                        items.item_desc,
                                        items.item_price,
                                        items.offer_price,
                                        items.available,
                                        items.acceptable,
                                        categories.cat_name,
                                        users.full_name,
                                        items.user_id,
                                        items.add_date,
                                        items.country_made,
                                        items.is_cover,
                                        items.is_special
                                    FROM 
                                        items
                                    LEFT JOIN
                                        categories
                                    ON
                                        categories.id = items.cat_id
                                    LEFT JOIN 
                                        users
                                    ON 
                                        users.user_id = items.user_id
                                    WHERE
                                        categories.visibility = 1
                                    ORDER BY
                                        items.add_date 
                                    DESC
                ");
            $stmt->execute();
            $items = $stmt->fetchAll();
            $count = $stmt->rowCount();

?>

            <div class="container flow">
                <h1>Items</h1>
                <div class="table-container flow">
                    <table class="table" id="categories-table" cellpadding="0px" cellspacing="0px">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Price</th>
                                <th>Offer Price</th>
                                <th>Availability</th>
                                <th>Acceptable</th>
                                <th>Category</th>
                                <th>Owner</th>
                                <th>Image</th>
                                <th>Likes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($items as $item) {
                                $status = $item["acceptable"] ? "Activated" : "Not Activated";
                                $dimmed_class = $item["acceptable"] ? "" : "dimmed";
                                $is_special = $item["is_special"] ? "Special Item" : "";
                                $is_cover = $item["is_cover"] ? "The Cover Item" : "";

                                $title = "Add Date: " . $item["add_date"] .
                                    "\nDescription: " . $item["item_desc"] .
                                    "\nMade in " . $item["country_made"] .
                                    "\n" . $is_special .
                                    "\n" . $is_cover;

                                echo "<tr class='" . $dimmed_class . "' title='" . $title . "'>";
                                echo "<td data-special='" . $item["is_special"] . "' data-cover='" . $item["is_cover"] . "'>" . $item["item_id"] . "</td>";
                                echo "<td>" . $item["item_name"] . "</td>";
                                echo "<td>" . number_format($item["item_price"]) . "</td>";
                                echo "<td>" . number_format($item["offer_price"]) . "</td>";
                                echo "<td>" . ($item["available"] === 1 ? "On" : "Off") . "</td>";
                                echo "<td>" . ($item["acceptable"] === 1 ? "On" : "Off") . "</td>";
                                echo "<td>" . $item["cat_name"] . "</td>";
                                echo "<td>" . $item["full_name"] . "</td>";
                                echo "<td class='td-images'>";
                                $stmt = $conn->prepare("SELECT 
                                items_images.img,
                                            items.item_id
                                        FROM 
                                            items_images
                                        LEFT JOIN
                                            items
                                        ON
                                            items_images.item_id = items.item_id
                                        WHERE
                                            items.item_id = ?
                                            ");
                                $stmt->execute(array($item["item_id"]));
                                $item_images = $stmt->fetchAll();

                                $stmt = $conn->prepare("SELECT * FROM items_likes WHERE item_id = ?");
                                $stmt->execute(array($item["item_id"]));
                                $item_likes = $stmt->rowCount();
                                $src = "";
                                foreach ($item_images as $img) {
                                    $src .= "\n" . $img["img"];
                                }

                                if (!empty($item_images)) {
                                    echo "<img class='show-images' src='" . $item_images[0]["img"] . "' data-images-src='" . $src . "' style='cursor:pointer;'>";
                                } else {
                                    echo "<img class='show-images' src='./layout/images/no-item.png' data-images-src='" . $src . "' style='cursor:pointer;'>";
                                }

                                echo "<td>" . $item_likes . "</td>";
                                echo "</td>";

                                echo "<td class='dots'>
                                        <div class='list'>
                                            <a class='btn btn-secondary' href='?do=Edit&id=" . $item["item_id"] . "'>Edit</a>
                                            <a class='btn btn-secondary confirm' href='?do=Delete&id=" . $item["item_id"] . "'>Delete</a>";
                                echo $item["acceptable"] === 0
                                    ? "<a class='btn btn-secondary confirm' href='?do=Activate&id=" . $item["item_id"] . "'>Activate</a>"
                                    : "<a class='btn btn-secondary confirm' href='?do=Deactivate&id=" . $item["item_id"] . "'>Deactivate</a>";

                                !$item["is_cover"] && print "<a class='btn btn-secondary confirm' href='?do=Cover&id=" . $item["item_id"] . "'>Make it Cover</a>";

                                echo $item["is_special"] === 0
                                    ? "<a class='btn btn-secondary confirm' href='?do=Special&id=" . $item["item_id"] . "'>Add to Special</a>"
                                    : "<a class='btn btn-secondary confirm' href='?do=Unspecial&id=" . $item["item_id"] . "'>Remove from special</a>";

                                echo  "</div>";
                                echo  "</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <p style="font-size: var(--fs-sm);"><?php echo $count . " items was found." ?></p>
                <a class="add-new-btn btn btn-primary" href='?do=Add' title="Add new Item"></a>
            </div>

            <div id="images-show-container"></div>

            <script src="./layout/js/itemsImages.js"></script>

            <script>
                let btns2 = document.querySelectorAll(".confirm");
                btns2.forEach(confirmBtn => {
                    confirmBtn.addEventListener("click", (e) => {
                        const method = e.target.innerHTML;
                        const input = confirm(`Do you want actually to ${method.toUpperCase()} ${confirmBtn.parentElement.parentElement.parentElement.querySelector("td:nth-of-type(2)").innerHTML}?`);
                        !input && e.preventDefault();
                    });
                })
            </script>

        <?php
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
                            <input type="text" name="title" id="add-items-name" placeholder="Title" autocomplete="off" tabindex="1" required>
                            <label for="add-items-name">Title</label>
                        </div>
                        <div class="form-input">
                            <input type="text" name="description" id="add-items-description" placeholder="Full Name" autocomplete="off" tabindex="2" required>
                            <label for="add-items-description">Description</label>
                        </div>
                        <div class="form-input">
                            <input type="number" name="price" id="add-items-price" placeholder="Price" autocomplete="off" tabindex="3" min="1" required>
                            <label for="add-items-price">Price</label>
                        </div>
                        <div class="form-input">
                            <input type="text" name="country" id="add-items-country" placeholder="Country" autocomplete="off" tabindex="4" required>
                            <label for="add-items-country">Country</label>
                        </div>
                        <div class="form-input-select">
                            <select name="category" id="add-items-category" tabindex="7" required>
                                <?php
                                foreach ($categories_options as $option) {
                                    echo "<option value='" . $option["id"] . "'>" . $option["cat_name"] . "</option>";
                                }
                                ?>
                            </select>
                            <label for="add-items-category">Category</label>
                        </div>
                        <div class="form-input-select">
                            <select name="user" id="add-items-user" tabindex="8" required>
                                <?php
                                foreach ($users_options as $option) {
                                    echo "<option value='" . $option["user_id"] . "'>" . $option["full_name"] . "</option>";
                                }
                                ?>
                            </select>
                            <label for="add-items-user">Username</label>
                        </div>
                        <div class="form-input-file">
                            <input type="file" name="images[]" id="add-items-images" multiple="multiple" tabindex="9" required>
                            <label for="add-items-images">Images</label>
                        </div>
                    </div>
                    <div class="inputs checks">
                        <div class="form-input-check">
                            <input type="checkbox" name="available" value="1" id="add-categories-available" checked>
                            <label for="add-categories-available" tabindex="10">Available</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" id="add-items-submit" tabindex="11">Add Item</button>
                </form>
            </div>

            <script type="module">
                import {
                    Validate
                } from "./layout/js/formsValidation.js";
                let addForm = new Validate(
                    "add-items-form",
                    "add-items-form-messages",
                    "add-items-submit"
                );
                addForm.submitForm();
            </script>

            <?php
            break;
        case "Insert";

            if ($_SERVER["REQUEST_METHOD"] === "POST") {

                extract($_POST);
                $available === "1" ? 1 : 0;

                $errorArr = array();

                $stmt = $conn->prepare("SELECT item_name FROM items WHERE item_name = ? LIMIT 1");
                $stmt->execute(array($title));
                $count = $stmt->rowCount();

                $allowedExtensions = array("jpeg", "png", "jpg");

                !is_dir("../data") && mkdir("../data");
                !is_dir("../data/uploads") && mkdir("../data/uploads");

                if ($_FILES['images']['name'][0]) {
                    $fileCount = count($_FILES['images']['name']);
                    for ($i = 0; $i < $fileCount; $i++) {
                        $fileName = $_FILES['images']['name'][$i];
                        $fileTmpName = $_FILES['images']['tmp_name'][$i];
                        $fileSize = $_FILES['images']['size'][$i];
                        $fileType = $_FILES['images']['type'][$i];
                        $ext = explode(".", $fileName);
                        $ext = strtolower(end($ext));
                        !in_array($ext, $allowedExtensions) && $errorArr[] = "<strong>$ext</strong> is not allowed as extension";
                    }
                }

                $count && $errorArr[] = "the <strong>Name</strong> is already exists";

                !preg_match($description_title_re, $title) && $errorArr[] = "<strong>Title</strong> must be between 4 and 50 characters";
                !preg_match($description_title_re, $description) && $errorArr[] = "<strong>Description</strong> must be between 4 and 50 characters";
                !preg_match($price_order_re, $price) && $errorArr[] = "<strong>Price</strong> must be positive number";
                !preg_match($name_country_re, $country) && $errorArr[] = "<strong>Country</strong> must be one Capitalized Word between 4 and 20 characters";

                if (count($errorArr)) {
                    echo "<ul class='error-msgs'>";
                    foreach ($errorArr as $err) {
                        echo "<li>" . $err . "</li>";
                    }
                    echo "</ul>";
                } else {

                    $stmt = $conn->prepare("SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'beddishop' AND TABLE_NAME = 'items';");
                    $stmt->execute();
                    $item_id = (int) $stmt->fetch()[0];
                    $stmt = $conn->prepare("INSERT INTO 
                        items(
                            item_name,
                            item_desc,
                            item_price,
                            add_date,
                            country_made,
                            cat_id,
                            user_id,
                            available
                        )
                        VALUES (?,?,?,NOW(),?,?,?,?)
                    ");
                    $stmt->execute(array($title, $description, $price, $country, $category, $user, $available));

                    if ($_FILES['images']['name'][0]) {
                        $fileCount = count($_FILES['images']['name']);
                        for ($i = 0; $i < $fileCount; $i++) {
                            $fileName = $_FILES['images']['name'][$i];
                            $fileTmpName = $_FILES['images']['tmp_name'][$i];
                            $fileSize = $_FILES['images']['size'][$i];
                            $fileType = $_FILES['images']['type'][$i];
                            // Specify the upload destination folder
                            $uploadFolder = '../data/uploads//';
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
                    redirect("1 Item has been added", "items.php", 1, "success");
                }
            } else {
                redirect("You can't browse this page directly", "items.php?do=Add", 1);
            }
            break;

        case "Edit";

            $id = isset($_GET["id"]) && is_numeric($_GET["id"]) ? $_GET["id"] : 0;

            $stmt = $conn->prepare("SELECT * FROM items WHERE item_id = ? LIMIT 1");
            $stmt->execute(array($id));
            $c = $stmt->rowCount();

            if ($c > 0) {

                $stmt = $conn->prepare("SELECT id,cat_name FROM categories");
                $stmt->execute();
                $categories_options = $stmt->fetchAll();

                $stmt = $conn->prepare("SELECT user_id,full_name FROM users WHERE reg_status = 1");
                $stmt->execute();
                $users_options = $stmt->fetchAll();

                $stmt = $conn->prepare("SELECT * FROM items WHERE item_id = ? LIMIT 1");
                $stmt->execute(array($id));
                $item = $stmt->fetch();

                $stmt = $conn->prepare("SELECT * FROM items_images WHERE item_id = ?");
                $stmt->execute(array($id));
                $images = $stmt->fetchAll();

            ?>

                <div class="container">
                    <h1>Edit New Item</h1>
                    <form action="?do=Update" method="POST" class="form flow" id="edit-items-form" enctype="multipart/form-data">
                        <ul class="msgs" id="edit-items-form-messages">
                        </ul>
                        <div class="inputs fields">
                            <input type="hidden" name="item_id" value="<?php echo $id ?>">
                            <div class="form-input">
                                <input type="text" name="title" id="edit-items-name" placeholder="Title" autocomplete="off" tabindex="1" value="<?php echo $item["item_name"] ?>" required>
                                <label for="edit-items-name">Title</label>
                            </div>
                            <div class="form-input">
                                <input type="text" name="description" id="edit-items-description" placeholder="Full Name" autocomplete="off" tabindex="2" value="<?php echo $item["item_desc"] ?>" required>
                                <label for="edit-items-description">Description</label>
                            </div>
                            <div class="form-input">
                                <input type="number" name="price" id="edit-items-price" placeholder="Price" autocomplete="off" tabindex="3" min="1" value="<?php echo $item["item_price"] ?>" required>
                                <label for="edit-items-price">Price</label>
                            </div>
                            <div class="form-input">
                                <input type="number" name="offerprice" id="edit-items-offer-price" placeholder="Offer Price" autocomplete="off" tabindex="3" min="0" value="<?php echo $item["offer_price"] ?>">
                                <label for="edit-items-offer-price">Offer Price</label>
                            </div>
                            <div class="form-input">
                                <input type="text" name="country" id="edit-items-country" placeholder="Country" autocomplete="off" tabindex="4" value="<?php echo $item["country_made"] ?>" required>
                                <label for="edit-items-country">Country</label>
                            </div>
                            <div class="form-input-select">
                                <select name="category" id="edit-items-category" tabindex="7" required>
                                    <?php
                                    foreach ($categories_options as $option) {
                                        if ($item["cat_id"] === $option["id"]) {
                                            echo "<option value='" . $option["id"] . "' selected='selected'>" . $option["cat_name"] . "</option>";
                                        } else {
                                            echo "<option value='" . $option["id"] . "'>" . $option["cat_name"] . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="edit-items-category">Category</label>
                            </div>
                            <div class="form-input-select">
                                <select name="user" id="edit-items-user" tabindex="8" required>
                                    <?php
                                    foreach ($users_options as $option) {
                                        if ($item["user_id"] === $option["user_id"]) {
                                            echo "<option value='" . $option["user_id"] . "' selected='selected'>" . $option["full_name"] . "</option>";
                                        } else {
                                            echo "<option value='" . $option["user_id"] . "'>" . $option["full_name"] . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="edit-items-user">Username</label>
                            </div>
                            <div class="form-input-file">
                                <input type="file" name="images[]" id="edit-items-images" multiple="multiple" tabindex="9">
                                <label for="edit-items-images">New Images</label>
                            </div>
                        </div>
                        <div class="inputs checks">
                            <div class="form-input-check">
                                <input type="checkbox" name="available" value="1" id="edit-categories-available" <?php echo $item["available"] === 1 ? "checked" : "" ?>>
                                <label for="edit-categories-available" tabindex="10">Available</label>
                            </div>
                        </div>
                        <div class="images-edit-container">
                            <?php
                            $counter = 1;
                            foreach ($images as $image) :
                                $js_id = uniqid()
                            ?>
                                <input type="hidden" id=<?php echo $js_id ?> name=<?php echo "img-" . $counter ?> data-src=<?php echo $image["img"] ?> value="0">
                                <div class="image">
                                    <img src=<?php echo $image["img"] ?> alt="">
                                    <a class="delete-img-btn btn btn-danger confirm" data-js-id=<?php echo $js_id ?> href="#" title="DELETE THE IMAGE">×</a>
                                </div>
                            <?php
                                $counter++;
                            endforeach
                            ?>
                        </div>
                        <button type="submit" class="btn btn-primary" id="edit-items-submit" tabindex="11">Edit Item</button>
                    </form>
                    <div class="btn-group">
                        <a class="btn btn-success" href="items.php?do=Cover&id=<?php echo $id ?>" tabindex="12">Main Site Cover</a>
                        <?php
                        $stmt = $conn->prepare("SELECT is_special FROM items WHERE item_id = ? LIMIT 1");
                        $stmt->execute(array($id));
                        $is_special = $stmt->fetch()["is_special"];
                        if ($is_special) :
                        ?>
                            <a class="btn btn-danger" href="items.php?do=Unspecial&id=<?php echo $id ?>" tabindex="13">Remove Special</a>
                        <?php else : ?>
                            <a class="btn btn-success" href="items.php?do=Special&id=<?php echo $id ?>" tabindex="13">Add to Special</a>
                        <?php endif ?>
                    </div>
                </div>

                <script>
                    const deleteBtns = document.querySelectorAll(".delete-img-btn");
                    deleteBtns.forEach((btn) => {
                        btn.addEventListener("click", () => {
                            let selector = btn.getAttribute("data-js-id");
                            let dataSrc = document.getElementById(selector).getAttribute("data-src");
                            document.getElementById(selector).setAttribute("value", dataSrc);
                            btn.parentElement.remove();
                        });
                    });
                </script>

                <script>
                    let btns3 = document.querySelectorAll(".confirm");
                    btns3.forEach(confirmBtn => {
                        confirmBtn.addEventListener("click", (e) => {
                            const method = e.target.innerHTML;
                            const input = confirm(`Do you want actually to Delete the image ?`);
                            !input && e.preventDefault();
                        });
                    })
                </script>

                <script type="module">
                    import {
                        Validate
                    } from "./layout/js/formsValidation.js";
                    let editForm = new Validate(
                        "edit-items-form",
                        "edit-items-form-messages",
                        "edit-items-submit"
                    );
                    editForm.submitForm();
                </script>

            <?php
            } /* end of if statement */ else {
                $msg = "There is not item like this";
                redirect($msg, "items.php", 1, "danger");
            }
            break;
        case "Update";

            if ($_SERVER["REQUEST_METHOD"] === "POST") {

                extract($_POST);
                $available === "1" ? 1 : 0;

                $errorArr = array();

                $stmt = $conn->prepare("SELECT item_name FROM items WHERE item_id != ? AND item_name = ? LIMIT 1");
                $stmt->execute(array($item_id, $title));
                $count = $stmt->rowCount();

                $allowedExtensions = array("jpeg", "png", "jpg");

                !is_dir("../data") && mkdir("../data");
                !is_dir("../data/uploads") && mkdir("../data/uploads");

                $no_photos = false;

                if ($_FILES['images']['name'][0]) {
                    $fileCount = count($_FILES['images']['name']);
                    for ($i = 0; $i < $fileCount; $i++) {
                        $fileName = $_FILES['images']['name'][$i];
                        $fileTmpName = $_FILES['images']['tmp_name'][$i];
                        $fileSize = $_FILES['images']['size'][$i];
                        $fileType = $_FILES['images']['type'][$i];
                        $ext = explode(".", $fileName);
                        $ext = strtolower(end($ext));
                        !in_array($ext, $allowedExtensions) && $errorArr[] = "<strong>$ext</strong> is not allowed as extension";
                    }
                    $no_photos = true;
                } else {
                    $stmt = $conn->prepare("SELECT img FROM items_images WHERE item_id = ?");
                    $stmt->execute(array($item_id));
                    $images_count = $stmt->rowCount();

                    for ($i = 1; $i <= $images_count; $i++) {
                        $no_photos = $no_photos || $_POST["img-" . $i] === "0";
                    }
                }

                !$no_photos && $errorArr[] = "you must keep one <strong>Image</strong> at least";
                $count && $errorArr[] = "the <strong>Name</strong> is already exists";
                !preg_match($description_title_re, $title) && $errorArr[] = "<strong>Title</strong> must be between 4 and 50 characters";
                !preg_match($description_title_re, $description) && $errorArr[] = "<strong>Description</strong> must be between 4 and 50 characters";
                !preg_match($price_order_re, $price) && $errorArr[] = "<strong>Price</strong> must be positive number";
                !preg_match($price_order_re, $offerprice) && $errorArr[] = "<strong>Offer Price</strong> must be positive number";
                !preg_match($name_country_re, $country) && $errorArr[] = "<strong>Country</strong> must be one Capitalized Word between 4 and 20 characters";
                $offerprice >= $price && $errorArr[] = "<strong>Offer Price</strong> must be less than <strong>Price</strong>";

                if (count($errorArr)) {
                    echo "<ul class='error-msgs'>";
                    foreach ($errorArr as $err) {
                        echo "<li>" . $err . "</li>";
                    }
                    echo "</ul>";
                } else {

                    $stmt = $conn->prepare("SELECT img FROM items_images WHERE item_id = ?");
                    $stmt->execute(array($item_id));
                    $images_count = $stmt->rowCount();

                    for ($i = 1; $i <= $images_count; $i++) {
                        $stmt = $conn->prepare("SELECT img FROM items_images WHERE item_id = ? AND img = ? LIMIT 1");
                        $stmt->execute(array($item_id, $_POST["img-" . $i]));
                        $images_cnt = $stmt->rowCount();
                        if ($images_cnt) {
                            $stmt = $conn->prepare("DELETE FROM items_images WHERE item_id = ? AND img = ?");
                            $stmt->execute(array($item_id, $_POST["img-" . $i]));
                            unlink($_POST["img-" . $i]);
                        }
                    }

                    $stmt = $conn->prepare("UPDATE 
                                                items
                                            SET
                                                item_name = ?,
                                                item_desc = ?,
                                                item_price = ?,
                                                offer_price = ?,
                                                country_made = ?,
                                                cat_id = ?,
                                                user_id = ?,
                                                available = ?
                                            WHERE
                                                item_id = ?
                                            ");
                    $stmt->execute(array($title, $description, $price, $offerprice, $country, $category, $user, $available, $item_id));

                    if ($_FILES['images']['name'][0]) {
                        $fileCount = count($_FILES['images']['name']);
                        for ($i = 0; $i < $fileCount; $i++) {
                            $fileName = $_FILES['images']['name'][$i];
                            $fileTmpName = $_FILES['images']['tmp_name'][$i];
                            $fileSize = $_FILES['images']['size'][$i];
                            $fileType = $_FILES['images']['type'][$i];
                            // Specify the upload destination folder
                            $uploadFolder = '../data/uploads//';
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
                    redirect("1 Item has been Updated", "items.php", 1, "success");
                }
            } else {
                redirect("You can't browse this page directly", "items.php?do=Add", 1);
            }

            break;
        case "Delete";

            $id = isset($_GET["id"]) && is_numeric($_GET["id"]) ? $_GET["id"] : 0;

            $stmt = $conn->prepare("SELECT * FROM items WHERE item_id = ? LIMIT 1");
            $stmt->execute(array($id));
            $c = $stmt->rowCount();

            if ($c > 0) {
                if (hasDependencies("items_images", "item_id = " . $id)) {
                    $stmt = $conn->prepare("SELECT img FROM items_images WHERE item_id = ?");
                    $stmt->execute(array($id));
                    $images = $stmt->fetchAll();
                    foreach ($images as $image) {
                        unlink($image["img"]);
                    }
                    $stmt = $conn->prepare("DELETE FROM items_images WHERE item_id = ?");
                    $stmt->execute(array($id));
                }
                if (hasDependencies("items_likes", "item_id = " . $id)) {
                    $stmt = $conn->prepare("DELETE FROM items_likes WHERE item_id = ?");
                    $stmt->execute(array($id));
                }
                if (hasDependencies("comments", "item_id = " . $id)) {
                    $stmt = $conn->prepare("DELETE FROM comments WHERE item_id = ?");
                    $stmt->execute(array($id));
                }
                $stmt = $conn->prepare("DELETE FROM items WHERE item_id = ?");
                $stmt->execute(array($id));
                redirect("1 Item has been deleted", "items.php", 1, "success");
            } else {
                redirect("there is no item like that", "items.php", 1);
            }
            break;

        case "Pending";
            $stmt = $conn->prepare("SELECT 
                                        items.item_id,
                                        items.item_name,
                                        items.item_desc,
                                        items.item_price,
                                        items.offer_price,
                                        items.available,
                                        items.acceptable,
                                        categories.cat_name,
                                        users.full_name,
                                        items.user_id
                                    FROM 
                                        items
                                    LEFT JOIN
                                        categories
                                    ON
                                        categories.id = items.cat_id
                                    LEFT JOIN 
                                        users
                                    ON 
                                        users.user_id = items.user_id
                                    WHERE
                                        categories.visibility = 1
                                    AND
                                        items.acceptable = 0
                                    ");
            $stmt->execute();
            $items = $stmt->fetchAll();
            $count = $stmt->rowCount();

            ?>

            <div class="container flow">
                <h1>Items</h1>
                <div class="table-container flow">
                    <table class="table" id="categories-table" cellpadding="0px" cellspacing="0px">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Price</th>
                                <th>Offer Price</th>
                                <th>Availability</th>
                                <th>Acceptable</th>
                                <th>Category</th>
                                <th>Owner</th>
                                <th>Image</th>
                                <th>Likes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($items as $item) {
                                $status = $item["acceptable"] ? "Activated" : "Not Activated";
                                $dimmed_class = $item["acceptable"] ? "" : "dimmed";
                                $title = "" . $item["item_desc"];
                                echo "<tr class='" . $dimmed_class . "' title='" . $title . "'>";
                                echo "<td>" . $item["item_id"] . "</td>";
                                echo "<td>" . $item["item_name"] . "</td>";
                                echo "<td>" . number_format($item["item_price"]) . "</td>";
                                echo "<td>" . number_format($item["offer_price"]) . "</td>";
                                echo "<td>" . ($item["available"] === 1 ? "On" : "Off") . "</td>";
                                echo "<td>" . ($item["acceptable"] === 1 ? "On" : "Off") . "</td>";
                                echo "<td>" . $item["cat_name"] . "</td>";
                                echo "<td>" . $item["full_name"] . "</td>";
                                echo "<td class='td-images'>";
                                $stmt = $conn->prepare("SELECT 
                                                            items_images.img,
                                                            items.item_id
                                                        FROM 
                                                            items_images
                                                        LEFT JOIN
                                                            items
                                                        ON
                                                            items_images.item_id = items.item_id
                                                        WHERE
                                                            items.item_id = ?
                                                            ");
                                $stmt->execute(array($item["item_id"]));
                                $item_images = $stmt->fetchAll();
                                $stmt = $conn->prepare("SELECT * FROM items_likes WHERE item_id = ?");
                                $stmt->execute(array($item["item_id"]));
                                $item_likes = $stmt->rowCount();
                                $src = "";
                                foreach ($item_images as $img) {
                                    $src .= "\n" . $img["img"];
                                }
                                echo "<img class='show-images' src='" . $item_images[0]["img"] . "' data-images-src='" . $src . "' style='cursor:pointer;'>";
                                echo "<td>" . $item_likes . "</td>";
                                echo "</td>";

                                echo "<td class='dots'>
                                        <div class='list'>
                                            <a class='btn btn-secondary' href='?do=Edit&id=" . $item["item_id"] . "'>Edit</a>
                                            <a class='btn btn-secondary confirm' href='?do=Delete&id=" . $item["item_id"] . "'>Delete</a>";
                                echo $item["acceptable"] === 0
                                    ? "<a class='btn btn-secondary confirm' href='?do=Activate&id=" . $item["item_id"] . "'>Activate</a>"
                                    : "<a class='btn btn-secondary confirm' href='?do=Deactivate&id=" . $item["item_id"] . "'>Deactivate</a>";
                                echo  "</div>";
                                echo  "</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <p style="font-size: var(--fs-sm);"><?php echo $count . " items was found." ?></p>
                <a class="add-new-btn btn btn-primary" href='?do=Add' title="Add new Item"></a>
            </div>

            <div id="images-show-container"></div>

            <script src="./layout/js/itemsImages.js"></script>

            <script>
                let btns = document.querySelectorAll(".confirm");
                btns.forEach(confirmBtn => {
                    confirmBtn.addEventListener("click", (e) => {
                        const method = e.target.innerHTML;
                        const input = confirm(`Do you want actually to ${method.toUpperCase()} ${confirmBtn.parentElement.parentElement.parentElement.querySelector("td:nth-of-type(2)").innerHTML}?`);
                        !input && e.preventDefault();
                    });
                })
            </script>

<?php
            break;

        case "Activate";
            $item_id = isset($_GET["id"]) && is_numeric($_GET["id"]) ? $_GET["id"] : 0;
            $stmt = $conn->prepare("SELECT item_id FROM items WHERE item_id = ? LIMIT 1");
            $stmt->execute(array($item_id));
            $cnt = $stmt->rowCount();
            if ($count) {
                $stmt = $conn->prepare("UPDATE items SET acceptable = 1 WHERE item_id = ?");
                $stmt->execute(array($item_id));
                redirect("1 Item has been Activated", "back", 1, "success");
            } else {
                redirect("There is no id like that", "back", 1);
            }
            break;

        case "Deactivate";
            $item_id = isset($_GET["id"]) && is_numeric($_GET["id"]) ? $_GET["id"] : 0;
            $stmt = $conn->prepare("SELECT item_id FROM items WHERE item_id = ? LIMIT 1");
            $stmt->execute(array($item_id));
            $cnt = $stmt->rowCount();
            if ($count) {
                $stmt = $conn->prepare("UPDATE items SET acceptable = 0 WHERE item_id = ?");
                $stmt->execute(array($item_id));
                redirect("1 Item has been Deactivated", "back", 1, "success");
            } else {
                redirect("There is no id like that", "back", 1);
            }
            break;
    }

    include  $tpl . "footer.php";
} else {
    header("Location: index.php");
    exit;
}

ob_end_flush();
