<?php

ob_start();
session_start();

if (!isset($_SESSION["user_session_id"])) {
    header("Location: index.php");
    exit();
}

$page_title = "Checkout";
include "init.php";

$stmt = $conn->prepare("SELECT 
                            cart.quantity,
                            items.item_name,
                            items.item_id AS main_id,
                            (SELECT img FROM items_images WHERE items_images.item_id = main_id LIMIT 1) AS img,
                            CASE 
                                WHEN items.offer_price = 0 THEN items.item_price
                                ELSE items.offer_price
                            END * cart.quantity AS price
                            FROM
                            cart
                            LEFT JOIN
                            items
                            ON
                            items.item_id = cart.item_id
                            WHERE
                            cart.user_id = ?
                        ");
$stmt->execute(array($_SESSION["user_session_id"]));
$cart_items = $stmt->fetchAll();
$subtotal = array_reduce($cart_items, fn ($acc, $arr) => $acc + $arr["price"], 0);

$stmt = $conn->prepare("SELECT full_name,email FROM users WHERE user_id = ? LIMIT 1");
$stmt->execute(array($_SESSION["user_session_id"]));
$user = $stmt->fetch();

$do = isset($_GET["do"]) ? $_GET["do"] : "Manage";

switch ($do) {
    case "Manage":
?>

        <div class="checkout-container">

            <form action="?do=Insert" method="POST" class="checkout-form" id="add-order-form">

                <ul class="msgs" id="add-order-messages"></ul>

                <section>
                    <h1>Contact</h1>
                    <div class="flow">
                        <div>
                            <input type="email" name="email" id="checkout-email" placeholder="." value="<?php echo $user["email"] ?>">
                            <label for="checkout-email">Email Address</label>
                        </div>
                    </div>
                </section>

                <section>
                    <h1>Shipping Address</h1>
                    <div class="flow">
                        <div>
                            <select name="country" id="checkout-country" required>
                            </select>
                            <label for="checkout-country">Country</label>
                        </div>
                        <div>
                            <input type="text" name="fullname" id="checkout-full-name" placeholder="." value="<?php echo $user["full_name"] ?>" required>
                            <label for="checkout-full-name">Full Name</label>
                        </div>
                        <div>
                            <input type="text" name="address" id="checkout-address" placeholder="." required>
                            <label for="checkout-address">Address</label>
                        </div>
                        <div>
                            <input type="text" name="city" id="checkout-city" placeholder="." required>
                            <label for="checkout-city">City</label>
                        </div>
                    </div>
                </section>

                <section>
                    <h1>Payment</h1>
                    <div class="flow">
                        <div>
                            <input type="text" name="cardnumber" id="checkout-card-number" placeholder="." required>
                            <label for="checkout-card-number">Card Number</label>
                        </div>
                        <div>
                            <input type="text" name="namecard" id="checkout-name-card" placeholder="." required>
                            <label for="checkout-name-card">Name on Card</label>
                        </div>
                        <div>
                            <input type="date" name="expiration" id="checkout-expiration" required>
                            <label for="checkout-expiration">Expiration date (MM / YY)</label>
                        </div>
                        <div>
                            <input type="password" name="code" id="checkout-code" placeholder="." required>
                            <label for="checkout-code">Secure Code</label>
                        </div>
                    </div>
                </section>

                <button class="btn btn-primary" type="submit" id="add-order-submit">Pay Now</button>

            </form>

            <div class="checkout-container-title">
                <a href="#" id="show-order-summary"><i class="fa-solid fa-shopping-cart"></i> Show order summary</a>
                <span><?php echo number_format(round($subtotal * 1.2, 0)) ?></span>
            </div>

            <div class="order-summary-container">

                <div class="container">
                    <div class="items">

                        <?php
                        foreach ($cart_items as $item) :
                        ?>
                            <div class="item">
                                <div class="image">
                                    <img src="<?php echo substr($item["img"], 1) ?>" alt="dasda">
                                    <span><?php echo $item["quantity"] ?></span>
                                </div>
                                <span><?php echo $item["item_name"] ?></span>
                                <span><?php echo number_format($item["price"]) ?></span>
                            </div>
                        <?php endforeach ?>

                    </div>

                    <div class="total">
                        <div>
                            <span>Subtotal:</span>
                            <span><?php echo number_format($subtotal) ?></span>
                        </div>
                        <div>
                            <span>Shipping (20%):</span>
                            <span><?php echo number_format(round($subtotal * 0.2, 0)) ?></span>
                        </div>
                    </div>

                    <div class="total">
                        <div>
                            <span>Total:</span>
                            <span><?php echo number_format(round($subtotal * 1.2, 0)) ?></span>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <script src="layout/js/checkout.js"></script>

        <script type="module">
            import {
                Validate
            } from "./admin/layout/js/formsValidation.js";
            let addForm = new Validate(
                "add-order-form",
                "add-order-messages",
                "add-order-submit"
            );
            addForm.submitForm();
        </script>

<?php
        break;
    case "Insert":

        extract($_POST);

        $errorArr = array();

        !preg_match($email_re, $email) && $errorArr[] = "<strong>Email</strong> is not valid.";
        !preg_match($full_name_re, $fullname) && $errorArr[] = "<strong>Full Name</strong> is not valid.";
        !preg_match($description_title_re, $address) && $errorArr[] = "<strong>Address</strong> is not valid.";
        !preg_match($name_country_re, $city) && $errorArr[] = "<strong>City</strong> is not valid.";
        !preg_match($price_order_re, $cardnumber) && $errorArr[] = "<strong>Card Number</strong> is not valid.";
        !preg_match($username_re, $namecard) && $errorArr[] = "<strong>Name Card</strong> is not valid.";
        !preg_match($date_re, $expiration) && $errorArr[] = "<strong>Expiration Date</strong> is not valid.";
        !preg_match($price_order_re, $code) && $errorArr[] = "<strong>Code</strong> is not valid.";

        echo "<ul class='error-msgs'>";
        foreach ($errorArr as $err) {
            echo "<li>" . $err . "</li>";
        }
        echo "</ul>";

        if (empty($errorArr)) {
            $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
            $stmt->execute(array($_SESSION["user_session_id"]));
            echo "<p style='padding:1em;text-align:center;'>Your Order has been set, we will contact you Soon</p>";
            echo "<p style='padding:1em;text-align:center;'><a href='index.php'>Shop More</a></p>";
        }

        break;
}

?>



<?php
include $tpl . "footer.php";
ob_end_flush()
?>