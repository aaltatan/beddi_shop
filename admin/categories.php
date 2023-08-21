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

            $stmt = $conn->prepare("SELECT * FROM categories");
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
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($rows as $row) {
                                echo "<tr>";
                                echo "<td>" . $row["id"] . "</td>";
                                echo "<td>" . $row["cat_name"] . "</td>";
                                echo "<td>" . $row["cat_desc"] . "</td>";
                                echo "<td>" . $row["ordering"] . "</td>";
                                echo "<td>" . ($row["visibility"] ? "On" : "Off") . "</td>";
                                echo "<td>" . ($row["allow_comment"] ? "On" : "Off") . "</td>";
                                echo "<td>" . ($row["allow_ads"] ? "On" : "Off") . "</td>";
                                echo "<td class='dots'>
                                        <div class='list'>
                                            <a class='btn btn-secondary' href='?do=Edit&id=" . $row["id"] . "'>Edit</a>
                                            <a class='btn btn-secondary confirm' href='?do=Delete&id=" . $row["id"] . "'>Delete</a>
                                        </div>
                                     </td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <p style="font-size: var(--fs-sm);"><?php echo $count . " categories was found." ?></p>
                <a class="btn btn-primary" href='?do=Add'>Add New Category</a>
            </div>

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
                            <input type="text" name="name" id="add-categories-name" placeholder="name" autocomplete="off" tabindex="1">
                            <label for="add-categories-name">Name</label>
                        </div>
                        <div class="form-input">
                            <input type="text" name="description" id="add-categories-description" placeholder="Full Name" autocomplete="off" tabindex="2">
                            <label for="add-categories-description">Description</label>
                        </div>
                        <div class="form-input">
                            <input type="number" name="order" id="add-categories-order" placeholder="Order" autocomplete="off" tabindex="3" min="1">
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
                } from "../layout/js/formsValidation.js";
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

                $order_exists && $errorArr[] = "this order has been already found";
                $count && $errorArr[] = "the category has already created";
                strlen($name) < 4 &&  $errorArr[] = "name must be more than 4 characters";
                strlen($name) > 20 && $errorArr[] = "name must be less than 20 characters";
                strlen($description) < 4 && $errorArr[] = "description must be more than 4 characters";
                strlen($description) > 50 && $errorArr[] = "description must be less than 50 characters";
                !is_numeric($order) && $errorArr[] =  "order must be numeric";

                echo "<ul style='padding-left:1em;'>";
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
                    redirect("1 category has been added", "categories.php", 2, "success");
                }
            } else {
                redirect("You can't browse this page directly", "categories.php?do=Add", 2);
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
                redirect($msg, "back", 2, "success");
            } else {
                $msg = "there's no Category like this";
                redirect($msg, "back", 2, "danger");
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
                                <input type="text" name="name" id="edit-categories-name" placeholder="name" autocomplete="off" tabindex="1" value="<?php echo $name ?>">
                                <label for="edit-categories-name">Name</label>
                            </div>
                            <div class="form-input">
                                <input type="text" name="description" id="edit-categories-description" placeholder="Full Name" autocomplete="off" tabindex="2" value="<?php echo $description ?>">
                                <label for="edit-categories-description">Description</label>
                            </div>
                            <div class="form-input">
                                <input type="number" name="order" id="edit-categories-order" placeholder="Order" autocomplete="off" tabindex="3" min="1" value="<?php echo $order ?>">
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
                    } from "../layout/js/formsValidation.js";
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
                redirect($msg, "back", 2, "danger");
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

                $stmt = $conn->prepare("SELECT ordering FROM categories WHERE ordering != ?");
                $stmt->execute(array($order));
                $order_arr = $stmt->fetchAll();
                $order_arr = array_map(fn ($arr) => $arr["ordering"], $order_arr);
                $order_exists = array_search((int) $_POST["order"], $order_arr);

                $errorArr = array();

                $order_exists && $errorArr[] = "this order has been already found";
                strlen($name) < 4 &&  $errorArr[] = "name must be more than 4 characters";
                strlen($name) > 20 && $errorArr[] = "name must be less than 20 characters";
                strlen($description) < 4 && $errorArr[] = "description must be more than 4 characters";
                strlen($description) > 50 && $errorArr[] = "description must be less than 50 characters";
                !is_numeric($order) && $errorArr[] =  "order must be numeric";

                echo "<ul style='padding-left:1em;'>";
                foreach ($errorArr as $err) {
                    echo "<li>$err</li>";
                }
                echo "</ul>";

                echo var_dump(!count($errorArr));

                if (!count($errorArr)) {
                    echo "Hello World!";
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
                    redirect($msg, "categories.php", 2, "success");
                }
            }
            break;
        default:
            $msg = "there is no page like $do";
            redirect($msg, "categories.php", 2, "danger");
    }

    include  $tpl . "footer.php";
} else {
    header("Location: index.php");
    exit;
}

ob_end_flush();
