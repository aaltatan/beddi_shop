<?php

ob_start();

session_start();

$page_title = "Categories";

if (isset($_SESSION["admin"])) {

    include "init.php";
    include $tpl . "aside.php";

    $do = isset($_GET["do"]) ? $_GET["do"] : "Manage";

    switch ($do) {
        case "Manage";

            $stmt = $conn->prepare("SELECT * FROM categories ORDER BY ordering ASC");
            $stmt->execute();
            $rows = $stmt->fetchAll();
            $count = $stmt->rowCount();

?>

            <div class="container flow">
                <h1>Categories</h1>
                <div class="table-container flow">
                    <table class="table" id="categories-table" cellpadding="0px" cellspacing="0px">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Order</th>
                                <th>Visibility</th>
                                <th>Comments</th>
                                <th>Ads</th>
                                <th>Likes</th>
                                <th>Items</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($rows as $row) {
                                $title = "";
                                $stmt = $conn->prepare("SELECT item_name FROM items WHERE cat_id = ?");
                                $stmt->execute(array($row["id"]));
                                $items_cnt = $stmt->rowCount();
                                $items = $stmt->fetchAll();
                                foreach ($items as $item) {
                                    $title .= $item["item_name"] . "\n";
                                }
                                echo "<tr title='" . $title . "'>";
                                echo "<td>" . $row["id"] . "</td>";
                                echo "<td>" . $row["cat_name"] . "</td>";
                                echo "<td>" . $row["cat_desc"] . "</td>";
                                echo "<td>" . $row["ordering"] . "</td>";
                                echo "<td>" . ($row["visibility"] ? "Enabled" : "Disabled") . "</td>";
                                echo "<td>" . ($row["allow_comment"] ? "Enabled" : "Disabled") . "</td>";
                                echo "<td>" . ($row["allow_ads"] ? "Enabled" : "Disabled") . "</td>";
                                $stmt = $conn->prepare("SELECT * FROM categories_likes WHERE id = ?");
                                $stmt->execute(array($row["id"]));
                                $cnt = $stmt->rowCount();
                                echo "<td>" . $cnt . "</td>";
                                echo "<td>" . $items_cnt . "</td>";
                                echo "<td class='dots'>
                                        <div class='list'>
                                            <a class='btn btn-secondary' href='?do=Edit&id=" . $row["id"] . "'>Edit</a>";
                                if (!hasDependencies("items", "cat_id = " . $row["id"])) echo "<a class='btn btn-secondary confirm' href='?do=Delete&id=" . $row["id"] . "'>Delete</a>";
                                echo "</div>
                                     </td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <p style="font-size: var(--fs-sm);"><?php echo $count . " categories was found." ?></p>
                <a class="add-new-btn btn btn-primary" href='?do=Add' title="Add new Category"></a>
            </div>

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
            // Add Page Content
        ?>

            <div class="container">
                <h1>Add New Category</h1>
                <form action="?do=Insert" method="POST" class="form flow" id="add-categories-form">
                    <ul class="msgs" id="add-categories-form-messages">
                    </ul>
                    <div class="inputs fields">
                        <div class="form-input">
                            <input type="text" name="name" id="add-categories-name" placeholder="name" autocomplete="off" tabindex="1" required>
                            <label for="add-categories-name">Name</label>
                        </div>
                        <div class="form-input">
                            <input type="text" name="description" id="add-categories-description" placeholder="Full Name" autocomplete="off" tabindex="2" required>
                            <label for="add-categories-description">Description</label>
                        </div>
                        <div class="form-input">
                            <input type="number" name="order" id="add-categories-order" placeholder="Order" autocomplete="off" tabindex="3" min="1" required>
                            <label for="add-categories-order">Order</label>
                        </div>
                    </div>
                    <div class="inputs checks">
                        <div class="form-input-check">
                            <input type="checkbox" name="visibility" value="1" id="add-categories-visibility" checked>
                            <label for="add-categories-visibility" tabindex="4">Visibility</label>
                        </div>
                        <div class="form-input-check">
                            <input type="checkbox" name="comments" value="1" id="add-categories-comments" checked>
                            <label for="add-categories-comments" tabindex="5">Comments</label>
                        </div>
                        <div class="form-input-check">
                            <input type="checkbox" name="ads" value="1" id="add-categories-ads" checked>
                            <label for="add-categories-ads" tabindex="6">Ads</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" id="add-categories-submit" tabindex="7">Add Category</button>
                </form>
            </div>

            <script type="module">
                import {
                    Validate
                } from "./layout/js/formsValidation.js";
                let addForm = new Validate(
                    "add-categories-form",
                    "add-categories-form-messages",
                    "add-categories-submit"
                );
                addForm.submitForm();
            </script>

            <?php
            break;
        case "Insert";

            if ($_SERVER["REQUEST_METHOD"] === "POST") {

                $stmt = $conn->prepare("SELECT cat_name FROM categories WHERE cat_name = ?");
                $stmt->execute(array($_POST["name"]));
                $count = $stmt->rowCount();
                $errorArr = array();

                $stmt = $conn->prepare("SELECT ordering FROM categories");
                $stmt->execute();
                $order_arr = $stmt->fetchAll();
                $order_arr = array_map(fn ($arr) => $arr["ordering"], $order_arr);
                $order_exists = array_search((int) $_POST["order"], $order_arr);

                $errorArr = array();

                extract($_POST);

                $order_exists && $errorArr[] = "this <strong>Order</strong> has been already found";
                $count && $errorArr[] = "the <strong>Category</strong> has already created";

                !preg_match($name_country_re, $name) && $errorArr[] = "<strong>Name</strong> must be one Capitalized Word  between 4 and 20 characters";
                !preg_match($description_title_re, $description) && $errorArr[] = "<strong>Description</strong> must be between 4 and 50 characters";
                !preg_match($price_order_re, $order) && $errorArr[] = "<strong>Order</strong> must be positive number";

                echo "<ul class='error-msgs'>";
                foreach ($errorArr as $err) {
                    echo "<li>" . $err . "</li>";
                }
                echo "</ul>";

                if (!count($errorArr)) {
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
                    redirect("1 category has been added", "categories.php", 1, "success");
                }
            } else {
                redirect("You can't browse this page directly", "categories.php?do=Add", 1);
            }

            break;

        case "Delete";
            // Delete page content
            $catid = isset($_GET["id"]) && is_numeric($_GET["id"]) ? $_GET["id"] : 0;
            $stmt = $conn->prepare("SELECT id FROM categories WHERE id = ?");
            $stmt->execute(array($catid));
            $count = $stmt->rowCount();

            if ($count > 0) {
                $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
                $stmt->execute(array($catid));
                $msg = "1 Category has been deleted";
                redirect($msg, "back", 1, "success");
            } else {
                $msg = "there's no Category like this";
                redirect($msg, "back", 1, "danger");
            }
            break;

        case "Edit";
            // Edit page content
            $catid = isset($_GET["id"]) && is_numeric($_GET["id"]) ? $_GET["id"] : 0;

            $stmt = $conn->prepare("SELECT * FROM categories WHERE id = ? LIMIT 1");
            $stmt->execute(array($catid));
            $data = $stmt->fetch();
            $count = $stmt->rowCount();

            if ($count > 0) {

                $name = $data["cat_name"];
                $description = $data["cat_desc"];
                $order = $data["ordering"];
                $visibility = $data["visibility"];
                $comments = $data["allow_comment"];
                $ads = $data["allow_ads"];

            ?>

                <div class="container">
                    <h1>Edit <?php echo $name ?> Category</h1>
                    <form action="?do=Update" method="POST" class="form flow" id="edit-categories-form">
                        <ul class="msgs" id="edit-categories-form-messages">
                        </ul>
                        <div class="inputs fields">
                            <input type="hidden" name="catid" value="<?php echo $catid ?>">
                            <div class="form-input">
                                <input type="text" name="name" id="edit-categories-name" placeholder="name" autocomplete="off" tabindex="1" value="<?php echo $name ?>" required>
                                <label for="edit-categories-name">Name</label>
                            </div>
                            <div class="form-input">
                                <input type="text" name="description" id="edit-categories-description" placeholder="Full Name" autocomplete="off" tabindex="2" value="<?php echo $description ?>" required>
                                <label for="edit-categories-description">Description</label>
                            </div>
                            <div class="form-input">
                                <input type="number" name="order" id="edit-categories-order" placeholder="Order" autocomplete="off" tabindex="3" min="1" value="<?php echo $order ?>" required>
                                <label for="edit-categories-order">Order</label>
                            </div>
                        </div>
                        <div class="inputs checks">
                            <div class="form-input-check">
                                <input type="checkbox" name="visibility" value="1" id="edit-categories-visibility" <?php echo $visibility ? "checked" : "" ?>>
                                <label for="edit-categories-visibility" tabindex="4">Visibility</label>
                            </div>
                            <div class="form-input-check">
                                <input type="checkbox" name="comments" value="1" id="edit-categories-comments" <?php echo $comments ? "checked" : "" ?>>
                                <label for="edit-categories-comments" tabindex="5">Comments</label>
                            </div>
                            <div class="form-input-check">
                                <input type="checkbox" name="ads" value="1" id="edit-categories-ads" <?php echo $ads ? "checked" : "" ?>>
                                <label for="edit-categories-ads" tabindex="6">Ads</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary" id="edit-categories-submit" tabindex="7">Edit Category</button>
                    </form>
                </div>

                <script type="module">
                    import {
                        Validate
                    } from "./layout/js/formsValidation.js";
                    let editForm = new Validate(
                        "edit-categories-form",
                        "edit-categories-form-messages",
                        "edit-categories-submit"
                    );
                    editForm.submitForm();
                </script>

<?php

            } /* End of if statement */ else {
                $msg = "there's no Category like this";
                redirect($msg, "categories.php", 1, "danger");
            }

            break;
        case "Update";
            if ($_SERVER["REQUEST_METHOD"] === "POST") {

                $catid = $_POST["catid"];
                $name = $_POST["name"];
                $description = $_POST["description"];
                $order = $_POST["order"];
                $visibility = isset($_POST["visibility"]) ? "1" : "0";
                $comments = isset($_POST["comments"]) ? "1" : "0";
                $ads = isset($_POST["ads"]) ? "1" : "0";

                $stmt = $conn->prepare("SELECT cat_name FROM categories WHERE id != ? AND cat_name = ? LIMIT 1");
                $stmt->execute(array($catid, $name));
                $cat_exists = $stmt->rowCount();

                $stmt = $conn->prepare("SELECT ordering FROM categories WHERE ordering != ?");
                $stmt->execute(array($order));
                $order_arr = $stmt->fetchAll();
                $order_arr = array_map(fn ($arr) => $arr["ordering"], $order_arr);
                $order_exists = array_search((int) $_POST["order"], $order_arr);

                $errorArr = array();

                $cat_exists && $errorArr[] = "this <strong>Name</strong> has been already found";
                $order_exists && $errorArr[] = "this <strong>Order</strong> has been already found";

                !preg_match($name_country_re, $name) && $errorArr[] = "<strong>Name</strong> must be one Capitalized Word  between 4 and 20 characters";
                !preg_match($description_re, $description) && $errorArr[] = "<strong>Description</strong> must be between 4 and 50 characters";
                !preg_match($price_order_re, $order) && $errorArr[] = "<strong>Order</strong> must be positive number";

                echo "<ul class='error-msgs'>";
                foreach ($errorArr as $err) {
                    echo "<li>$err</li>";
                }
                echo "</ul>";

                if (!count($errorArr)) {
                    $stmt = $conn->prepare("UPDATE 
                                                categories 
                                            SET 
                                                cat_name = ?, 
                                                cat_desc = ?, 
                                                ordering = ?, 
                                                visibility = ?,
                                                allow_comment = ?, 
                                                allow_ads = ?
                                            WHERE 
                                                id = ?
                                            ");
                    $stmt->execute(array($name, $description, $order, $visibility, $comments, $ads, $catid));
                    $msg =  "1 Category has been Updated";
                    redirect($msg, "categories.php", 1, "success");
                }
            }
            break;
        default:
            $msg = "there is no page like $do";
            redirect($msg, "categories.php", 1, "danger");
    }

    include  $tpl . "footer.php";
} else {
    header("Location: index.php");
    exit;
}

ob_end_flush();
