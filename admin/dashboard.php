<?php
session_start();
$page_title = "Dashboard";
if (isset($_SESSION["admin"])) {
    include "init.php";
    include $tpl . "aside.php";

    $stmt = $conn->prepare("SELECT 
                                user_id,full_name,email,dt 
                            FROM 
                                users 
                            WHERE
                                reg_status = 0
                        ");
    $stmt->execute();
    $rows = $stmt->fetchAll();
    $rows_count = $stmt->rowCount();

    $stmt = $conn->prepare("SELECT 
                                items.item_id,
                                items.item_name,
                                categories.cat_name,
                                items.item_price,
                                items.item_desc,
                                users.full_name,
                                items.add_date,
                                items.country_made
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
                                items.acceptable = 0
    ");
    $stmt->execute();
    $items = $stmt->fetchAll();
    $items_count = $stmt->rowCount();

    $stmt = $conn->prepare("SELECT 
                                users.full_name,
                                comments.comment_id,
                                comments.added_date,
                                comments.comment,
                                items.item_name
                            FROM
                                pending_comments
                            LEFT JOIN
                                comments
                            ON
                                comments.comment_id = pending_comments.comment_id
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
                            WHERE
                                categories.allow_comment = 1
                            ORDER BY
                                comments.added_date
                            DESC
                            ");
    $stmt->execute();
    $comments = $stmt->fetchAll();
    $comments_count = $stmt->rowCount();

    $stmt = $conn->prepare("SELECT 
                                users.full_name,
                                comments.comment_id,
                                comments.added_date,
                                comments.comment,
                                items.item_name
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
                            WHERE
                                categories.allow_comment = 1
                            ORDER BY
                                comments.added_date
                            DESC
                            LIMIT
                                20
                            ");
    $stmt->execute();
    $recent_comments = $stmt->fetchAll();
    $recent_comments_count = $stmt->rowCount();
?>

    <div class="container flow">

        <h1>Dashboard</h1>

        <section class="statistics flow">
            <h2>Statistics</h2>
            <div class="wrapper">

                <?php if (getCount("users") > 0) : ?>
                    <a href="members.php" class="count-card">
                        <span>Total Members</span>
                        <p><?php echo getCount("users") ?></p>
                    </a>
                <?php endif ?>

                <?php if (getCount("items") > 0) : ?>
                    <a href="items.php" class="count-card">
                        <span>Total Items</span>
                        <p><?php echo getCount("items") ?></p>
                    </a>
                <?php endif ?>

                <?php if (getCount("categories") > 0) : ?>
                    <a href="categories.php" class="count-card">
                        <span>Total Categories</span>
                        <p><?php echo getCount("categories") ?></p>
                    </a>
                <?php endif ?>

                <?php if (getCount("items_likes") > 0) : ?>
                    <a href="categories.php" class="count-card">
                        <span>Total Likes</span>
                        <p><?php echo getCount("items_likes") ?></p>
                    </a>
                <?php endif ?>

                <?php if (getCount("items_images") > 0) : ?>
                    <a href="items.php" class="count-card">
                        <span>Total Images</span>
                        <p><?php echo getCount("items_images") ?></p>
                    </a>
                <?php endif ?>

                <?php if (getCount("items", "is_special") > 0) : ?>
                    <a href="items.php" class="count-card">
                        <span>Total Specials</span>
                        <p><?php echo getCount("items", "is_special") ?></p>
                    </a>
                <?php endif ?>

                <?php if (getCount("comments") > 0) : ?>
                    <a href="comments.php" class="count-card">
                        <span>Total Comments</span>
                        <p><?php echo getCount("comments") ?></p>
                    </a>
                <?php endif ?>

            </div>
        </section>

        <section class="last-updates flow">
            <h2>Last Updates</h2>
            <div class="wrapper">

                <?php if ($rows_count) : ?>
                    <div class="last-members">
                        <p class="heading">
                            <a href="members.php?do=Pending"><span>Pending Members (<?php echo getCount("pending_users") ?>)</span></a>
                            <a class="add-new" href="members.php?do=Add" title="Add new Member"></a>
                        </p>

                        <ul class="body">
                            <?php
                            foreach ($rows as $row) {
                                echo "<li>";
                                echo "
                            <a href='members.php?do=Edit&userid=" . $row["user_id"] . "'>
                                <img src='./layout/images/user-128x128.png' alt='dasd'>
                                <div class='title'>
                                    <p>" . $row["full_name"] . "</p>
                                    <p>" . $row["email"] . "</p>
                                </div>
                                <span>" . explode(" ", $row["dt"])[0] . "</span>
                                </a>
                            ";
                                echo "</li>";
                            }
                            ?>
                    </div>
                <?php endif ?>

                <?php if ($comments_count) : ?>
                    <div class="pending-comments">
                        <p class="heading">
                            <a href="comments.php?do=Pending"><span>Pending Comments (<?php echo getCount("pending_comments") ?>)</span></a>
                        </p>

                        <ul class="body">
                            <?php
                            foreach ($comments as $comment) {
                                echo "<li title='" . $comment["comment"] . "'>";
                                echo "
                            <a href='comments.php?do=Edit&id=" . $comment["comment_id"] . "'>
                                <img src='./layout/images/user-128x128.png' alt='dasd'>
                                <div class='title'>
                                    <p>" . $comment["comment"] . "</p>
                                    <p>" . $comment["full_name"] . " on " . $comment["item_name"] . "</p>
                                </div>
                                <span>" . explode(" ", $comment["added_date"])[0] . "</span>
                                </a>
                            ";
                                echo "</li>";
                            }
                            ?>
                    </div>
                <?php endif ?>

                <?php if ($recent_comments_count) : ?>
                    <div class="pending-comments">
                        <p class="heading">
                            <a href="comments.php?do=Pending"><span>Recent Comments (<?php echo $recent_comments_count ?>)</span></a>
                        </p>

                        <ul class="body">
                            <?php
                            foreach ($recent_comments as $comment) {
                                echo "<li title='" . $comment["comment"] . "'>";
                                echo "
                            <a href='comments.php?do=Edit&id=" . $comment["comment_id"] . "'>
                                <img src='./layout/images/user-128x128.png' alt='dasd'>
                                <div class='title'>
                                    <p>" . $comment["comment"] . "</p>
                                    <p>" . $comment["full_name"] . " on " . $comment["item_name"] . "</p>
                                </div>
                                <span>" . explode(" ", $comment["added_date"])[0] . "</span>
                                </a>
                            ";
                                echo "</li>";
                            }
                            ?>
                    </div>
                <?php endif ?>

                <?php if ($items_count) : ?>
                    <div class="last-items">
                        <p class="heading">
                            <a href="items.php?do=Pending"><span>Pending Items (<?php echo getCount("pending_items") ?>)</a>
                            <a class="add-new" href="items.php?do=Add" title="Add new Item"></a>
                        </p>
                        <ul class="body">
                            <?php
                            foreach ($items as $item) {
                                $title = $item["item_desc"] . "\nBy " . $item["full_name"] . "\nAdd Date: " . $item["add_date"] . "\nMade in: " . $item["country_made"];
                                echo "<li title='" . $title . "'>";
                                echo "<a href='items.php?do=Edit&id=" . $item["item_id"] . "'>";
                                $stmt = $conn->prepare("SELECT 
                                                        img
                                                    FROM
                                                        items_images
                                                    WHERE 
                                                        item_id = ?
                                                    LIMIT
                                                        1          
                            ");
                                $stmt->execute(array($item["item_id"]));
                                $image = $stmt->fetch();
                                echo "<img src='" . $image["img"] . "' alt='dasd'>";
                                echo "<div class='title'>
                                    <p>" . $item["item_name"] . "</p>
                                    <p>" . $item["cat_name"] . "</p>
                                </div>
                                <span>" . number_format($item["item_price"]) . "</span>
                                </a>
                            ";
                                echo "</li>";
                            }
                            ?>
                        </ul>
                    </div>
                <?php endif ?>

            </div>
        </section>

        <section class="gallery flow">
            <h2>Special Items</h2>
            <div class="wrapper">
                <?php
                $stmt = $conn->prepare("SELECT 
                                            items.item_name,
                                            items.item_id,
                                            items_images.img
                                        FROM
                                            items_images
                                        LEFT JOIN
                                            items
                                        ON
                                            items.item_id = items_images.item_id
                                        WHERE
                                            items.acceptable = 1
                                        AND
                                            items.is_special = 1
                                        ORDER BY
                                            items.add_date
                                        DESC
                                        LIMIT
                                            24
                ");
                $stmt->execute();
                $data = $stmt->fetchAll();
                foreach ($data as $item) :
                ?>
                    <a class="gallery-card" href="<?php echo 'items.php?do=Edit&id=' . $item['item_id'] ?>">
                        <div class="image">
                            <?php echo "<img src='" . $item["img"] . "' alt='dasdasd'>" ?>
                        </div>
                        <div class="info">
                            <?php echo "<p>" . $item["item_name"] . "</p>" ?>
                        </div>
                    </a>
                <?php endforeach ?>
            </div>
        </section>

        <section class="gallery flow">
            <h2>Cover Item</h2>
            <div class="wrapper">
                <?php
                $stmt = $conn->prepare("SELECT 
                                            items.item_name,
                                            items.item_id,
                                            items_images.img
                                        FROM
                                            items_images
                                        LEFT JOIN
                                            items
                                        ON
                                            items.item_id = items_images.item_id
                                        WHERE
                                            items.acceptable = 1
                                        AND
                                            items.is_cover = 1
                                        ORDER BY
                                            items.add_date
                                        DESC
                                        LIMIT
                                            24
                ");
                $stmt->execute();
                $data = $stmt->fetchAll();
                foreach ($data as $item) :
                ?>
                    <a class="gallery-card" href="<?php echo 'items.php?do=Edit&id=' . $item['item_id'] ?>">
                        <div class="image">
                            <?php echo "<img src='" . $item["img"] . "' alt='dasdasd'>" ?>
                        </div>
                        <div class="info">
                            <?php echo "<p>" . $item["item_name"] . "</p>" ?>
                        </div>
                    </a>
                <?php endforeach ?>
            </div>
        </section>

        <section class="gallery flow">
            <h2>Last Items</h2>
            <div class="wrapper">
                <?php
                $stmt = $conn->prepare("SELECT 
                                            items.item_name,
                                            items.item_id,
                                            items_images.img
                                        FROM
                                            items_images
                                        LEFT JOIN
                                            items
                                        ON
                                            items.item_id = items_images.item_id
                                        WHERE
                                            items.acceptable = 1
                                        ORDER BY
                                            items.add_date
                                        DESC
                                        LIMIT
                                            24
                ");
                $stmt->execute();
                $data = $stmt->fetchAll();
                foreach ($data as $item) :
                ?>
                    <a class="gallery-card" href="<?php echo 'items.php?do=Edit&id=' . $item['item_id'] ?>">
                        <div class="image">
                            <?php echo "<img src='" . $item["img"] . "' alt='dasdasd'>" ?>
                        </div>
                        <div class="info">
                            <?php echo "<p>" . $item["item_name"] . "</p>" ?>
                        </div>
                    </a>
                <?php endforeach ?>
            </div>
        </section>

    </div>

<?php

    include $tpl . "footer.php";
} else {
    header("Location: index.php");
    exit();
}
