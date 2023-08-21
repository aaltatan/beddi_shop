<?php
session_start();
$page_title = "Dashboard";
if (isset($_SESSION["username"])) {
    include "init.php";
    include $tpl . "aside.php";

    $stmt = $conn->prepare("SELECT user_id,full_name,email,dt FROM users WHERE group_id = 0 AND reg_status = 1 ORDER BY dt DESC LIMIT 5");
    $stmt->execute();
    $rows = $stmt->fetchAll();
?>

    <div class="container flow">

        <h1>Dashboard</h1>

        <section class="statistics flow">
            <h2>Statistics</h2>
            <div class="wrapper">
                <a href="members.php" class="count-card">
                    <span>Total Members</span>
                    <p><?php echo getCount("users") ?></p>
                </a>
                <a href="members.php?do=Pending" class="count-card">
                    <span>Pending Members</span>
                    <p><?php echo getCount("pending_users") ?></p>
                </a>
                <a href="members.php" class="count-card">
                    <span>Total Items</span>
                    <p>3,000</p>
                </a>
                <a href="members.php" class="count-card">
                    <span>Total Comments</span>
                    <p>1,500</p>
                </a>
            </div>
        </section>

        <section class="last-updates flow">
            <h2>Last Updates</h2>
            <div class="wrapper">

                <div class="last-members">
                    <p class="heading">Last Registered Members</p>
                    <ul class="body">
                        <?php

                        foreach ($rows as $row) {
                            echo "<li>";
                            echo "
                            <a href='members.php?do=Edit&userid=" . $row["user_id"] . "'>
                                <img src='../layout/images/login-landscape-1.jpg' alt='dasd'>
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

                <div class="last-items">
                    <p class="heading">Last Items</p>
                    <ul class="body">
                    </ul>
                </div>

            </div>
        </section>

    </div>

<?php

    include $tpl . "footer.php";
} else {
    header("Location: index.php");
    exit();
}
