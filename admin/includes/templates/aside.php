<div class="main-wrapper">
    <aside class="main-aside flow">
        <input type="search" name="main-search" placeholder="Alt + s to Search" data-status="showed" />
        <ul class="flow">
            <li>
                <a href="dashboard.php" role="button" aria-current="false">
                    <span>🛒</span>
                    <?php echo lang("HOME") ?>
                </a>
            </li>
            <li>
                <a href="#" role="button" aria-current="false">
                    <span>🛍️</span>
                    <?php echo lang("CATAGORIES") ?>
                </a>
            </li>
            <li>
                <a href="#" role="button" aria-current="false">
                    <span>🔦</span>
                    <?php echo lang("ITEMS") ?>
                </a>
            </li>
            <li>
                <a href="members.php" role="button" aria-current="false">
                    <span>🧑‍🤝‍🧑</span>
                    <?php echo lang("MEMBERS") ?>
                </a>
            </li>
            <li>
                <a href="#" role="button" aria-current="false">
                    <span>💹</span>
                    <?php echo lang("STATISTICS") ?>
                </a>
            </li>
            <li>
                <a href="#" role="button" aria-current="false">
                    <span>📃</span>
                    <?php echo lang("LOGS") ?>
                </a>
            </li>
        </ul>
    </aside>
    <div class="main">
        <header>
            <nav>
                <div class="brand">
                    <a href="dashboard.php">
                        <h2>🛒<?php echo lang("TITLE") ?></h2>
                    </a>
                </div>
                <input type="search" name="main-search" placeholder="Alt + s to Search" data-status="hidden" />
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
                        <li><a href="members.php?do=Edit&userid=<?php echo $_SESSION["userid"] ?>">✏️ <?php echo lang("EDITPROFILE") ?></a></li>
                        <li><a href="#">⚙️ <?php echo lang("SETTINGS") ?></a></li>
                        <li><a href="logout.php">📤 <?php echo lang("LOGOUT") ?></a></li>
                    </ul>
                </div>
            </nav>
        </header>
        <main class="flow">