<?php

ob_start();

session_start();

$page_title = "Log in";

if (isset($_SESSION['user'])) {
    header("Location: index.php");
}

include "init.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $user = $_POST["username"];
    $pass = $_POST["password"];
    $hashed_password  = sha1($pass);

    $errs = array();

    $stmt = $conn->prepare("SELECT 
                                user_id,username,`password`,reg_status
                            FROM
                                users
                            WHERE
                                username = ?
                            LIMIT 
                                1
                            ");

    $stmt->execute(array($user));
    $user_data = $stmt->fetch();
    $count = $stmt->rowCount();

    if ($count > 0) {
        $user_data["password"] !== $hashed_password && $errs[] = "<strong>Password</strong> for " . $user . " not matched";
        $user_data["reg_status"] !== 1 && $errs[] =  "<strong>" . $user . "</strong> is waiting for admin acceptation";
        if (empty($errs)) {
            $_SESSION["user"] = $user;
            $_SESSION["user_session_id"] = $user_data["user_id"];
            session_id($user_data["user_id"]);
            header("Location: index.php");
            exit();
        }
    } else {
        $errs[] = "<strong>" . $user . "</strong> was not found, Do you want to <a href='register.php'>Register</a>";
    }
}


?>

<form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="POST" class="login">
    <h1>LOG IN</h1>
    <?php if (!empty($errs)) : ?>
        <ul class="error-msgs" id="user-login">
            <?php foreach ($errs as $err) : ?>
                <li><?php echo $err ?></li>
            <?php endforeach ?>
        </ul>
    <?php endif ?>
    <div class="form-input">
        <input type="text" name="username" id="username-login" placeholder="." required>
        <label for="username-login">Username</label>
    </div>
    <div class="form-input">
        <input type="password" name="password" id="password-login" placeholder="." required>
        <label for="password-login">Password</label>
    </div>
    <button class="btn btn-primary" type="submit">Log in</button>
    <a href="register.php">Don't have an account? Sign up.</a>
</form>

<script>
    const passInput = document.getElementById("password-login");
    passInput.addEventListener("mouseover", () => {
        passInput.setAttribute("type", "text");
    })
    passInput.addEventListener("mouseout", () => {
        passInput.setAttribute("type", "password");
    })
</script>

<?php

include $tpl . "footer.php";
ob_end_flush();

?>