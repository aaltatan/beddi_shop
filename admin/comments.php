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
                                echo "<tr title='" . $title . "'>";
                                echo "<td>" . $row["comment_id"] . "</td>";
                                echo "<td>" . $row["comment"] . "</td>";
                                echo "<td>" . ($row["comment_status"] ? "Enabled" : "Disabled") . "</td>";
                                echo "<td>" . $row["added_date"] . "</td>";
                                echo "<td>" . $row["full_name"] . "</td>";
                                echo "<td>" . $row["item_name"] . "</td>";
                                echo "<td>" . $row["cat_name"] . "</td>";
                                echo "<img class='show-images' src='" . $row["img"] . "' data-images-src='" . $src . "' style='cursor:pointer;'>";
                                echo "<td class='dots'>
                                        <div class='list'>
                                            <a class='btn btn-secondary' href='?do=Edit&id=" . $row["comment_id"] . "'>Edit</a>";
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

        ?>

            <div class="container">
                <h1>Edit Comment</h1>
                <form action="?do=Update" method="POST" class="form flow" id="edit-comment-form">
                    <ul class="msgs" id="edit-comment-form-messages">
                    </ul>
                    <div class="inputs fields">
                        <input type="hidden" name="item_id" value="<?php echo $id ?>">
                        <div class="form-input">
                            <textarea type="text" name="comment" id="edit-comment-description" placeholder="Full Name" autocomplete="off" tabindex="1" value="<?php echo $item["item_desc"] ?>" required>
                            </textarea>
                            <label for="edit-comment">Comment</label>
                        </div>
                    </div>
                    <div class="inputs checks">
                        <div class="form-input-check">
                            <input type="checkbox" name="accept" value="1" id="edit-comment-accepting" <?php echo $item["available"] === 1 ? "checked" : "" ?>>
                            <label for="edit-comment-accepting" tabindex="2">Accepted</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" id="edit-comment-submit" tabindex="3">Edit Comment</button>
                </form>
            </div>

<?php
            break;
        case "Update";
            break;
        case "Delete";
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
