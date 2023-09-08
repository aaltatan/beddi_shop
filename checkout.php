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
?>

<div class="checkout-container">

    <form action="index.php" method="POST" class="checkout-form">

        <section>
            <h1>Contact</h1>
            <div class="flow">
                <div>
                    <input type="email" name="email" id="checkout-email" placeholder=".">
                    <label for="checkout-email">Email Address</label>
                </div>
            </div>
        </section>

        <section>
            <h1>Shipping Address</h1>
            <div class="flow">
                <div>
                    <select name="country" id="checkout-country">
                    </select>
                    <label for="checkout-country">Country</label>
                </div>
                <div>
                    <input type="text" name="fullname" id="checkout-full-name" placeholder=".">
                    <label for="checkout-full-name">Full Name</label>
                </div>
                <div>
                    <input type="text" name="address" id="checkout-address" placeholder=".">
                    <label for="checkout-address">Address</label>
                </div>
                <div>
                    <input type="text" name="city" id="checkout-city" placeholder=".">
                    <label for="checkout-city">City</label>
                </div>
            </div>
        </section>

        <section>
            <h1>Payment</h1>
            <div class="flow">
                <div>
                    <input type="text" name="cardnumber" id="checkout-card-number" placeholder=".">
                    <label for="checkout-card-number">Card Number</label>
                </div>
                <div>
                    <input type="text" name="namecard" id="checkout-name-card" placeholder=".">
                    <label for="checkout-name-card">Name on Card</label>
                </div>
                <div>
                    <input type="date" name="expiration" id="checkout-expiration">
                    <label for="checkout-expiration">Expiration date (MM / YY)</label>
                </div>
                <div>
                    <input type="password" name="code" id="checkout-code" placeholder=".">
                    <label for="checkout-code">Secure Code</label>
                </div>
            </div>
        </section>

        <button class="btn btn-primary" type="submit">Pay Now</button>

    </form>

    <div class="checkout-container-title">
        <a href="#" id="show-order-summary"><i class="fa-solid fa-shopping-cart"></i> Show order summary</a>
        <span><?php echo number_format(round($subtotal * 1.2, 0)) ?></span>
    </div>

    <div class="order-summary-container">

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

<script src="layout/js/checkout.js"></script>

<?php
include $tpl . "footer.php";
ob_end_flush()
?>