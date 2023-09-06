<div class="main-wrapper">
    <aside class="main-aside flow">
        <ul class="flow">
            <li>
                <a href="dashboard.php" role="button" aria-current="false" title="Alt + 1">
                    <span>🛒</span>
                    Dashboard
                </a>
            </li>
            <li>
                <a href="categories.php" role="button" aria-current="false" title="Alt + 2">
                    <span>🛍️</span>
                    Categories
                </a>
            </li>
            <li>
                <a href="items.php" role="button" aria-current="false" title="Alt + 3">
                    <span>🔦</span>
                    Items
                </a>
            </li>
            <li>
                <a href="members.php" role="button" aria-current="false" title="Alt + 4">
                    <span>🧑‍🤝‍🧑</span>
                    Members
                </a>
            </li>
            <li>
                <a href="comments.php" role="button" aria-current="false" title="Alt + 5">
                    <span>💬</span>
                    Comments
                </a>
            </li>
        </ul>
    </aside>
    <div class="main">
        <header>
            <nav>
                <div class="brand">
                    <a href="dashboard.php">
                        <h2>🛒Beddi</h2>
                    </a>
                    <?php
                    $current_page = explode("/", $_SERVER["PHP_SELF"]);
                    $current_page = end($current_page);
                    $page_title = explode(".", $current_page)[0];
                    echo "<a href=" . $current_page . ">" . $page_title . "</a>"
                    ?>
                </div>
                <div class="search">
                    <input type="search" name="main-search" placeholder="Alt + s to Search" data-status="hidden" tabindex="50" />
                    <ul class="list">
                        <?php

                        $current_page = explode("/", $_SERVER["PHP_SELF"]);
                        $current_page = end($current_page);

                        switch ($current_page) {
                            case "items.php":
                                $stmt = $conn->prepare("SELECT * FROM items WHERE acceptable = 1");
                                $stmt->execute();
                                $data = $stmt->fetchAll();

                                foreach ($data as $item) :

                                    $stmt = $conn->prepare("SELECT img FROM items_images WHERE item_id = ? LIMIT 1");
                                    $stmt->execute(array($item["item_id"]));
                                    $img = $stmt->fetch();

                                    echo "<li>";
                                    echo    "<a href='items.php?do=Edit&id=" . $item['item_id'] . "' tabindex='51'>";
                                    echo        "<img src='" . $img['img'] . "' alt='' >";
                                    echo        "<p>" . $item['item_name'] . "</p>";
                                    echo    "</a>";
                                    echo "</li>";

                                endforeach;

                                break;

                            case "members.php":

                                $stmt = $conn->prepare("SELECT * FROM users WHERE reg_status = 1");
                                $stmt->execute();
                                $data = $stmt->fetchAll();

                                foreach ($data as $user) :
                                    echo "<li>";
                                    echo    "<a href='members.php?do=Edit&userid=" . $user['user_id'] . "' tabindex='51'>";
                                    echo        "<img src='./layout/images/user-128x128.png' alt=''>";
                                    echo        "<p>" . $user['full_name'] . "</p>";
                                    echo    "</a>";
                                    echo "</li>";
                                endforeach;

                                break;

                            case "categories.php":

                                $stmt = $conn->prepare("SELECT * FROM categories");
                                $stmt->execute();
                                $data = $stmt->fetchAll();

                                foreach ($data as $category) :
                                    echo "<li>";
                                    echo    "<a href='categories.php?do=Edit&id=" . $category['id'] . "' tabindex='51'>";
                                    echo        "<img src='./layout/images/no-item.png' alt=''>";
                                    echo        "<p>" . $category['cat_name'] . "</p>";
                                    echo    "</a>";
                                    echo "</li>";
                                endforeach;

                                break;

                            case "dashboard.php":

                                $stmt = $conn->prepare("SELECT 
                                                            user_id as `id`,full_name as `name`,'user' as `type`
                                                        FROM 
                                                            users
                                                        UNION 
                                                        SELECT 
                                                            id as `id`,cat_name as `name`,'category' as `type`
                                                        FROM
                                                            categories
                                                        UNION 
                                                        SELECT 
                                                            item_id as `id`,item_name as `name`,'item' as `type`
                                                        FROM
                                                            items
                                                        ");
                                $stmt->execute();
                                $data = $stmt->fetchAll();

                                foreach ($data as $row) :
                                    echo "<li>";
                                    if ($row["type"] === "user") {
                                        echo "<a href='members.php?do=Edit&userid=" . $row['id'] . "' tabindex='51'>";
                                    } elseif ($row["type"] === "category") {
                                        echo "<a href='categories.php?do=Edit&id=" . $row['id'] . "' tabindex='51'>";
                                    } else {
                                        echo "<a href='items.php?do=Edit&id=" . $row['id'] . "' tabindex='51'>";
                                    }
                                    echo        "<img src='./layout/images/no-item.png' alt=''>";
                                    echo        "<p>" . $row['name'] . " | " . ucfirst($row['type']) . "</p>";
                                    echo    "</a>";
                                    echo "</li>";
                                endforeach;

                                break;
                        }


                        ?>

                    </ul>
                </div>
                <span class="burger" id="burger"></span>
                <span class="mode">
                    <input type="checkbox" name="" id="mode-btn" />
                    <label for="mode-btn" data-theme-dark="false"></label>
                </span>
                <div class="user">
                    <div class="icon">
                        <h2 id="user-name"><?php echo ucfirst(explode(".", $_SESSION["admin"])[0]) ?></h2>
                    </div>
                    <ul class="list">
                        <li><a href="members.php?do=Edit&userid=<?php echo $_SESSION["adminid"] ?>">✏️ Edit Profile</a></li>
                        <li><a href="../index.php" target="_blank">🌐 Main Site</a></li>
                        <li><a href="#">⚙️ Settings</a></li>
                        <li><a href="logout.php">📤 Logout</a></li>
                    </ul>
                </div>
            </nav>
        </header>
        <main class="flow">