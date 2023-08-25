<div class="main-wrapper">
    <aside class="main-aside flow">
        <ul class="flow">
            <li>
                <a href="dashboard.php" role="button" aria-current="false">
                    <span>🛒</span>
                    Dashboard
                </a>
            </li>
            <li>
                <a href="categories.php" role="button" aria-current="false">
                    <span>🛍️</span>
                    Categories
                </a>
            </li>
            <li>
                <a href="items.php" role="button" aria-current="false">
                    <span>🔦</span>
                    Items
                </a>
            </li>
            <li>
                <a href="members.php" role="button" aria-current="false">
                    <span>🧑‍🤝‍🧑</span>
                    Members
                </a>
            </li>
            <li>
                <a href="#" role="button" aria-current="false">
                    <span>💹</span>
                    Statistics
                </a>
            </li>
            <li>
                <a href="#" role="button" aria-current="false">
                    <span>📃</span>
                    Logs
                </a>
            </li>
        </ul>
    </aside>
    <div class="main">
        <header>
            <nav>
                <div class="brand">
                    <a href="dashboard.php">
                        <h2>🛒Beddi Shop</h2>
                    </a>
                </div>
                <div class="search">
                    <input type="search" name="main-search" placeholder="Alt + s to Search" data-status="hidden" tabindex="5" />
                    <ul class="list">
                        <?php
                        $stmt = $conn->prepare("SELECT * FROM items WHERE acceptable = 1");
                        $stmt->execute();
                        $data = $stmt->fetchAll();

                        foreach ($data as $item) :
                            $stmt = $conn->prepare("SELECT img FROM items_images WHERE item_id = ? LIMIT 1");
                            $stmt->execute(array($item["item_id"]));
                            $img = $stmt->fetch();
                        ?>

                            <li>
                                <a href="<?php echo 'items.php?do=Edit&id=' . $item['item_id'] ?>" tabindex="6">
                                    <img src="<?php echo $img['img'] ?>" alt="">
                                    <p><?php echo $item['item_name'] ?></p>
                                </a>
                            </li>

                        <?php endforeach ?>

                    </ul>
                </div>
                <span class="burger" id="burger"></span>
                <span class="mode">
                    <input type="checkbox" name="" id="mode-btn" />
                    <label for="mode-btn" data-theme-dark="false"></label>
                </span>
                <div class="user">
                    <div class="icon">
                        <h2 id="user-name"><?php echo ucfirst(explode(".", $_SESSION["username"])[0]) ?></h2>
                    </div>
                    <ul class="list">
                        <li><a href="members.php?do=Edit&userid=<?php echo $_SESSION["userid"] ?>">✏️ Edit Profile</a></li>
                        <li><a href="#">⚙️ Settings</a></li>
                        <li><a href="logout.php">📤 Logout</a></li>
                    </ul>
                </div>
            </nav>
        </header>
        <main class="flow">