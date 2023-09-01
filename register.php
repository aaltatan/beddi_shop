<?php

ob_start();

session_start();

$page_title = "Register";

if (isset($_SESSION['user'])) {
    header("Location: index.php");
}

include "init.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $user = $_POST["username"];
    $full = $_POST["fullname"];
    $email = $_POST["email"];
    $pass = $_POST["password"];
    $hashed_password  = sha1($pass);

    $errs = array();

    $stmt = $conn->prepare("SELECT 
                                username
                            FROM
                                users
                            WHERE
                                username = ?
                            ");

    $stmt->execute(array($user));
    $count = $stmt->rowCount();

    if ($count > 0) {
        $errs[] = "<strong>" . $user . "</strong> has been already registered, Do you want to <a href='login.php'>Login</a>";
    } else {

        !preg_match($username_re, $user) && $errs[] = "<strong>UserName</strong> must start with small letter, it must be at least 4 characters and less than or equal 20 characters, it can include digits and period symbol only";
        !preg_match($full_name_re, $full) && $errs[] = "<strong>Fullname</strong> must start with letter and can it has spaces and ends with letter";
        !preg_match($email_re, $email) && $errs[] = "<strong>Email</strong> not valid";
        !preg_match($password_re, $pass) && $errs[] = "<strong>Password</strong> must be more than 8 characters";

        if (empty($errs)) {
            $stmt = $conn->prepare("INSERT INTO users(username, password, email, full_name, dt) VALUES (?,?,?,?,NOW())");
            $stmt->execute(array($user, sha1($pass), $email, $full));
            $msg =  "<strong>$user</strong> has been Added, you will waiting some time for admin acceptation, feel yourself at <a href='index.php'>home</a> while the process";
        }
    }
}

?>

<form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="POST" class="login" id="add-members-form">
    <h1>REGISTER</h1>
    <ul class="error-msgs" id="add-members-form-messages"></ul>
    <?php if (!empty($errs)) : ?>
        <ul class="error-msgs" id="user-login">
            <?php foreach ($errs as $err) : ?>
                <li><?php echo $err ?></li>
            <?php endforeach ?>
        </ul>
    <?php endif ?>
    <?php if (isset($msg)) : ?>
        <span class="success-msg"><?php echo $msg ?></span>
    <?php endif ?>
    <div class="form-input">
        <input type="text" name="username" id="username-register" placeholder="." required>
        <label for="username-register">Username</label>
    </div>
    <div class="form-input">
        <input type="text" name="fullname" id="fullname-register" placeholder="." required>
        <label for="fullname-register">Full Name</label>
    </div>
    <div class="form-input">
        <input type="email" name="email" id="email-register" placeholder="." required>
        <label for="fullname-register">Email</label>
    </div>
    <div class="form-input">
        <input type="password" name="password" id="password-register" placeholder="." required>
        <label for="password-register">Password</label>
    </div>
    <button class="btn btn-primary" type="submit" id="add-members-submit">Register</button>
    <a href="login.php">Do you have an account already? Log in.</a>
</form>

<script>
    const passInput = document.getElementById("password-register");
    passInput.addEventListener("mouseover", () => {
        passInput.setAttribute("type", "text");
    })
    passInput.addEventListener("mouseout", () => {
        passInput.setAttribute("type", "password");
    })
</script>

<script type="module">
    import {
        Validate
    } from "./admin/layout/js/formsValidation.js";
    let addForm = new Validate(
        "add-members-form",
        "add-members-form-messages",
        "add-members-submit"
    );
    addForm.submitForm();
</script>

<?php

include $tpl . "footer.php";
ob_end_flush();

?>