<?php
session_start();
$page_title = "Dashboard";
if (isset($_SESSION["username"])) {
    include "init.php";
    include $tpl . "aside.php";
?>

    <div class="container flow">

        <h1>Dashboard</h1>

        <section class="statistics flow">
            <h2>Statistics</h2>
            <div class="wrapper">
                <a href="members.php" class="count-card">
                    <span>Total Members</span>
                    <p>200</p>
                </a>
                <a href="members.php" class="count-card">
                    <span>Pending Members</span>
                    <p>25</p>
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
                        <li>
                            <a href="#">
                                <img src="../layout/images/login-landscape-1.jpg" alt="dasd">
                                <div class="title">
                                    <p>Abdullah</p>
                                    <p>a.altatan@gmail.com</p>
                                </div>
                                <span>3,000,000</span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <img src="../layout/images/login-landscape-1.jpg" alt="dasd">
                                <div class="title">
                                    <p>Abdullah</p>
                                    <p>a.altatan@gmail.com</p>
                                </div>
                                <span>3,000,000</span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <img src="../layout/images/login-landscape-1.jpg" alt="dasd">
                                <div class="title">
                                    <p>Abdullah</p>
                                    <p>a.altatan@gmail.com</p>
                                </div>
                                <span>3,000,000</span>
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <img src="../layout/images/login-landscape-1.jpg" alt="dasd">
                                <div class="title">
                                    <p>Abdullah</p>
                                    <p>a.altatan@gmail.com</p>
                                </div>
                                <span>3,000,000</span>
                            </a>
                        </li>
                    </ul>
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
