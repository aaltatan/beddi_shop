<?php

ob_start();

session_start();

$page_title = "Comments";

if (isset($_SESSION["admin"])) {

    include "init.php";
    include $tpl . "aside.php";

    $do = isset($_GET["do"]) ? $_GET["do"] : "Manage";

    switch ($do) {
        case "Manage";

            $stmt = $conn->prepare("SELECT 
                                        comments.comment_id,
                                        comments.comment,
                                        comments.comment_status,
                                        comments.added_date,
                                        users.full_name,
                                        items.item_name,
                                        categories.cat_name,
                                        (SELECT img FROM items_images WHERE items_images.item_id = items.item_id LIMIT 1) as img
                                    FROM 
                                        comments
                                    LEFT JOIN
                                        users
                                    ON
                                        users.user_id = comments.user_id
                                    LEFT JOIN
                                        items
                                    ON
                                        items.item_id = comments.item_id
                                    LEFT JOIN
                                        categories
                                    ON
                                        categories.id = items.cat_id
                                    ORDER BY 
                                        comments.added_date 
                                    DESC
            ");
            $stmt->execute();
            $rows = $stmt->fetchAll();
            $count = $stmt->rowCount();

?>

            <div class="container flow">
                <h1>Comments</h1>
                <div class="table-container flow">
                    <table class="table" id="categories-table" cellpadding="0px" cellspacing="0px">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Comment</th>
                                <th>Status</th>
                                <th>Add Date</th>
                                <th>User</th>
                                <th>Item</th>
                                <th>Category</th>
                                <th>Images</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($rows as $row) {
                                $title = $row["comment"];
                                $dimmed_class = $row["comment_status"] ? "" : "dimmed";
                                echo "<tr class='" . $dimmed_class . "' title='" . $title . "'>";
                                echo "<td>" . $row["comment_id"] . "</td>";
                                echo "<td>" . $row["comment"] . "</td>";
                                echo "<td>" . ($row["comment_status"] ? "Enabled" : "Disabled") . "</td>";
                                echo "<td>" . $row["added_date"] . "</td>";
                                echo "<td>" . $row["full_name"] . "</td>";
                                echo "<td>" . $row["item_name"] . "</td>";
                                echo "<td>" . $row["cat_name"] . "</td>";
                                echo "<td class='td-images'>";
                                echo "<img class='show-images' src='" . $row["img"] . "' style='cursor:pointer;'>";
                                echo "</td>";
                                echo "<td class='dots'>
                                        <div class='list'>
                                            <a class='btn btn-secondary' href='?do=Edit&id=" . $row["comment_id"] . "'>Edit</a>";
                                echo $row["comment_status"] === 0
                                    ? "<a class='btn btn-secondary confirm' href='?do=Activate&id=" . $row["comment_id"] . "'>Activate</a>"
                                    : "<a class='btn btn-secondary confirm' href='?do=Deactivate&id=" . $row["comment_id"] . "'>Deactivate</a>";
                                echo       "<a class='btn btn-secondary confirm' href='?do=Delete&id=" . $row["comment_id"] . "'>Delete</a>";
                                echo "</div>
                                     </td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <p style="font-size: var(--fs-sm);"><?php echo $count . " comments was found." ?></p>
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
        case "Edit";

            $comment_id = is_numeric($_GET["id"]) ? $_GET["id"] : 0;
            $stmt = $conn->prepare("SELECT * FROM comments WHERE comment_id = ? LIMIT 1");
            $stmt->execute(array($comment_id));
            $count = $stmt->rowCount();
            $comment = $stmt->fetch();
            if ($count) :

            ?>

                <div class="container">
                    <h1>Edit Comment</h1>
                    <form action="?do=Update" method="POST" class="form flow" id="edit-comment-form">
                        <ul class="msgs" id="edit-comment-form-messages">
                        </ul>
                        <div class="inputs fields">
                            <input type="hidden" name="id" value="<?php echo $comment_id ?>">
                            <div class="form-input-textarea">
                                <textarea name="comment" id="edit-comment" tabindex="1" required><?php echo trim($comment["comment"]) ?></textarea>
                            </div>
                        </div>
                        <div class="inputs checks">
                            <div class="form-input-check">
                                <input type="checkbox" name="status" value="1" id="edit-comment-accepting" <?php echo $comment["comment_status"] === 1 ? "checked" : "" ?>>
                                <label for="edit-comment-accepting" tabindex="2">Accepted</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary" id="edit-comment-submit" tabindex="3">Edit Comment</button>
                    </form>
                </div>

            <?php
            else :
                redirect("there is no comment id like this", "back", 1);
            endif;
            ?>

<?php
            break;

        case "Update";

            if ($_SERVER["REQUEST_METHOD"] === "POST") {

                $comment_id = $_POST["id"];
                $comment = $_POST["comment"];
                $comment_status = $_POST["status"] === "1" ? 1 : 0;
                $stmt = $conn->prepare("SELECT comment_id FROM comments WHERE comment_id = ?");
                $stmt->execute(array($comment_id));
                $count = $stmt->rowCount();

                if ($count) {
                    $stmt = $conn->prepare("UPDATE 
                                                comments 
                                            SET 
                                                comment = ?, 
                                                comment_status = ? 
                                            WHERE 
                                                comment_id = ?
                                            ");
                    $stmt->execute(array($comment, $comment_status, $comment_id));
                    redirect("one comment has been updated", "comments.php", 1, "success");
                } else {
                    redirect("there is no comment id like this", "back", 1);
                }
            } else {
                redirect("you can not browse this page directly", "back", 1);
            }

            break;

        case "Delete";

            $comment_id = is_numeric($_GET["id"]) ? $_GET["id"] : 0;
            $stmt = $conn->prepare("SELECT comment_id FROM comments WHERE comment_id = ?");
            $stmt->execute(array($comment_id));
            $count = $stmt->rowCount();

            if ($count) {
                $stmt = $conn->prepare("DELETE FROM comments WHERE comment_id = ?");
                $stmt->execute(array($comment_id));
                redirect("one comment has been Deleted", "back", 1, "success");
            } else {
                redirect("there is no comment_id like this", "back", 1);
            }

            break;

        case "Activate";

            $comment_id = is_numeric($_GET["id"]) ? $_GET["id"] : 0;
            $stmt = $conn->prepare("SELECT comment_id FROM comments WHERE comment_id = ?");
            $stmt->execute(array($comment_id));
            $count = $stmt->rowCount();

            if ($count) {
                $stmt = $conn->prepare("UPDATE comments SET comment_status = 1 WHERE comment_id = ?");
                $stmt->execute(array($comment_id));
                redirect("one comment has been activated", "back", 1, "success");
            } else {
                redirect("there is no comment_id like this", "back", 1);
            }

            break;

        case "Deactivate";

            $comment_id = is_numeric($_GET["id"]) ? $_GET["id"] : 0;
            $stmt = $conn->prepare("SELECT comment_id FROM comments WHERE comment_id = ?");
            $stmt->execute(array($comment_id));
            $count = $stmt->rowCount();

            if ($count) {
                $stmt = $conn->prepare("UPDATE comments SET comment_status = 0 WHERE comment_id = ?");
                $stmt->execute(array($comment_id));
                redirect("one comment has been Deactivated", "back", 1, "success");
            } else {
                redirect("there is no comment_id like this", "back", 1);
            }

            break;
    }

    include  $tpl . "footer.php";
} else {
    header("Location: index.php");
    exit;
}

ob_end_flush();
